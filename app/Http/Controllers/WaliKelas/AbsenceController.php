<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Absence;
use App\Models\Setting;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\HomeroomTeacher;



class AbsenceController extends Controller
{
    // -----------------------------------------------------------------
    // READ & VIEW (Scan Kamera)
    // -----------------------------------------------------------------

    /**
     * Tampilkan halaman scan barcode (kamera + log terbaru kelas yang diampu).
     */
    public function scanForm()
    {
        $user = Auth::user();
        // Asumsi relasi user->homeroomTeacher->class tersedia
        $class = $user->homeroomTeacher->class ?? null;

        if (!$class) {
            return redirect()->route('walikelas.dashboard')
                             ->with('error', 'Anda belum mengampu kelas. Silakan hubungi admin.');
        }

        // ✅ CEK HARI LIBUR (Weekend + Hari Libur Nasional)
        $holiday = $this->checkHoliday();
        if ($holiday['is_holiday']) {
            return redirect()->route('walikelas.dashboard')
                             ->with('error', 'Hari ini libur: ' . $holiday['reason'] . '. Absensi tidak tersedia.');
        }

        $classId = $class->id;
        $today = Carbon::today();
        
        // Ambil log absensi terbaru untuk kelas ini
        $recentAbsences = Absence::with('student.class')
            ->whereDate('attendance_time', $today)
            ->whereIn('status', ['Hadir', 'Terlambat'])
            ->whereHas('student', function ($query) use ($classId) {
                $query->where('class_code', $classId);
            })
            ->orderBy('attendance_time', 'desc')
            ->take(10)
            ->get();
        
        // Muat data siswa aktif untuk form manual (jika form manual di-include)
        $students = Student::with('class')
                             ->where('class_code', $classId)
                             ->where('status', 'active') 
                             ->orderBy('name')
                             ->get();
        
        return view('walikelas.absensi.scan', compact('class', 'students', 'recentAbsences')); 
    }
    
    // -----------------------------------------------------------------
    // READ & VIEW (Halaman Manual / Koreksi Data)
    // -----------------------------------------------------------------
    
    /**
     * Tampilkan halaman manajemen Absensi Manual/Koreksi Data Harian.
     */
    public function manualIndex()
    {
        $user = Auth::user();
        $class = $user->homeroomTeacher->class ?? null;

        if (!$class) {
             return redirect()->route('walikelas.dashboard')
                              ->with('error', 'Anda belum mengampu kelas.');
        }

        $classId = $class->id;
        $today = Carbon::today();
        
        // 1. Ambil data siswa aktif untuk dropdown manual
        $students = Student::with('class')
                           ->where('class_code', $classId)
                           ->where('status', 'active') 
                           ->orderBy('name')
                           ->get();

        // 2. Ambil semua log absensi hari ini untuk kelas ini (untuk tabel koreksi)
        $todayAttendance = Absence::whereDate('attendance_time', $today)
                                 ->whereHas('student', function ($q) use ($classId) {
                                     $q->where('class_code', $classId);
                                 })
                                 ->with('student.class')
                                 ->orderBy('attendance_time', 'desc')
                                 ->get();
                                
        return view('walikelas.absensi.manual.index', compact('class', 'students', 'todayAttendance'));
    }

    /**
     * Proses pencatatan absensi dari form manual (Sakit/Izin/Alpha/Hadir/Terlambat).
     */
    public function manualStore(Request $request)
    {
        $request->validate([
             'nis' => 'required|string|exists:students,nis',
             'status' => 'required|in:Hadir,Terlambat,Sakit,Izin,Alfa',
             'notes' => 'nullable|string|max:500',
        ]);

        // CEK HARI LIBUR (Weekend + Hari Libur Nasional)
        $holiday = $this->checkHoliday();
        if ($holiday['is_holiday']) {
            return redirect()->back()
                             ->with('error', 'Hari ini libur: ' . $holiday['reason'] . '. Input manual tidak dapat dilakukan.');
        }
        
        $student = Student::where('nis', $request->nis)->first();
        $currentTime = Carbon::now();
        $today = Carbon::today();

        // Cek record absensi hari ini
        $existingAbsence = Absence::where('student_nisn', $student->nisn)
                                 ->whereDate('attendance_time', $today)
                                 ->exists();
        
        if ($existingAbsence) {
             return redirect()->back()->with('error', "Status {$student->name} sudah memiliki record absensi hari ini.")->withInput();
        }
        
        // Catat Absensi Manual
        $status = $request->status;
        Absence::create([
            'student_nisn' => $student->nisn,
            'attendance_time' => $currentTime,
            'status' => $status,
            'notes' => $request->notes, 
            'recorded_by' => Auth::check() ? Auth::user()->name : 'Manual',
        ]);

        return redirect()->route('walikelas.absensi.manual.index')->with('success', "Status {$student->name} berhasil dicatat sebagai " . $status . '.');
    }
    
    // -----------------------------------------------------------------
    // CREATE (Record Scan)
    // -----------------------------------------------------------------

    /**
     * Proses pencatatan absensi dari scan barcode (IN/OUT Logic).
     * @param WhatsAppService $waService Service WA (di-inject oleh Laravel)
     */
    public function record(Request $request, WhatsAppService $waService)
    {
        $request->validate([
            'barcode' => 'required|string|max:255',
        ]);

        // --- AMBIL DATA KELAS DARI WALI KELAS YANG SEDANG LOGIN ---
        $user = Auth::user();
        $class = $user->homeroomTeacher->class ?? null;

        if (!$class) {
            return response()->json([
                'success' => false, 
                'message' => '❌ Perangkat Gagal Absen: Anda belum terdaftar sebagai wali kelas aktif.'
            ], 403);
        }
        // ---------------------------------------------------------

        // CEK HARI LIBUR (Weekend + Hari Libur Nasional)
        $holiday = $this->checkHoliday();
        if ($holiday['is_holiday']) {
            return response()->json([
                'success' => false,
                'message' => 'Hari ini libur: ' . $holiday['reason'] . '. Absensi tidak dapat dilakukan.',
                'type' => 'HOLIDAY'
            ], 403);
        }

        $barcode_data = $request->barcode;
        $currentTime = Carbon::now();
        $today = Carbon::today();
        
        $student = Student::with(['class', 'parents'])->where('barcode_data', $barcode_data)->first();

        if (!$student || $student->status !== 'active') {
             $message = $student ? "Siswa {$student->name} non-aktif atau status tidak valid." : 'Siswa tidak ditemukan.';
             return response()->json(['success' => false, 'message' => $message], 404);
        }
        
        $parent = $student->parents->first();
        $parentPhone = $parent ? $parent->phone_number : null;
        
        // 🛠️ SEKARANG AMAN: Cache diturunkan ke 1 detik agar responsif membaca perubahan UI Admin saat testing
        $settings = Cache::remember('attendance_settings', 1, function () {
             return Setting::whereIn('key', ['attendance_start_time', 'late_tolerance_minutes', 'attendance_end_time'])
                            ->pluck('value', 'key');
        });

        // 💡 Tentukan Jam Pulang berdasarkan kelas siswa.
        // Jika kelas punya jam pulang khusus, pakai jam pulang kelas.
        // Jika kosong, pakai jam pulang umum dari pengaturan.
        $classDismissalTime = ($student->class && $student->class->dismissal_time)
            ? substr($student->class->dismissal_time, 0, 5)
            : null;

        $endTimeSetting = $classDismissalTime ?: ($settings['attendance_end_time'] ?? '15:00');
        $displayEndTime = substr($endTimeSetting, 0, 5);

        $designatedEndTime = Carbon::parse($today->format('Y-m-d') . ' ' . $displayEndTime);
        
        // --- Cek Record Absensi Hari Ini ---
        $existingAbsence = Absence::where('student_nisn', $student->nisn)
                                 ->whereDate('attendance_time', $today)
                                 ->first(); 

        // 2. LOGIC SCAN OUT (PULANG)
        if ($existingAbsence && is_null($existingAbsence->checkout_time)) {
            
            // 🛑 TAMBAHKAN BLOK KODE INI UNTUK MENCEGAH ALPHA/SAKIT/IZIN BISA ABSEN PULANG
            if (in_array($existingAbsence->status, ['Alfa', 'Sakit', 'Izin'])) {
                return response()->json([
                    'success' => false, 
                    'message' => "❌ Gagal Absen. Siswa sudah tercatat dengan status: {$existingAbsence->status} hari ini.",
                    'type' => 'INVALID_STATUS'
                ], 409);
            }
            
            // 🛑 PENGECEKAN JAM PULANG
            if ($currentTime->lessThan($designatedEndTime)) {
                $timeRemaining = $designatedEndTime->diffForHumans($currentTime, [
                    'parts' => 2,
                    'join' => true,
                    'syntax' => Carbon::DIFF_ABSOLUTE
                ]);
                
                $message = "❌ Gagal Pulang. Belum waktunya pulang (Jam Pulang: {$displayEndTime}). Sisa waktu: {$timeRemaining} lagi.";
                Log::warning("Absensi Gagal: Siswa mencoba pulang sebelum waktunya.", ['student_nisn' => $student->nisn, 'current_time' => $currentTime]);
                
                return response()->json(['success' => false, 'message' => $message], 409); 
            }
            // -----------------------------------
            
            $existingAbsence->checkout_time = $currentTime;
            $existingAbsence->save();
            
            // Notifikasi WA PULANG
            dispatch(function () use ($waService, $parentPhone, $student, $currentTime) {
                $this->sendWaNotification($waService, $parentPhone, $student->name, 'PULANG', $currentTime->format('H:i:s'));
            })->afterResponse();

            return response()->json([
                'success' => true, 
                'message' => $student->name . ' berhasil PULANG pada pukul ' . $currentTime->format('H:i:s') . '.',
                'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'],
                'type' => 'OUT'
            ]);
        } 
        
        // 3. Mencegah scan kedua kali (sudah masuk, sudah pulang, atau sudah dicatat)
        if ($existingAbsence) {
             $message = $existingAbsence->checkout_time ? 
                         "Siswa {$student->name} sudah PULANG hari ini." : 
                         "Siswa {$student->name} sudah Absen MASUK/dicatat hari ini.";
             return response()->json(['success' => false, 'message' => $message], 409);
        }

        // 4. LOGIC SCAN IN (MASUK/TERLAMBAT)
        $startTimeSetting = $settings['attendance_start_time'] ?? '07:00';
        $toleranceSetting = $settings['late_tolerance_minutes'] ?? 10;
        
        $defaultStartTime = '07:00'; 
        $toleranceMinutes = (int)($toleranceSetting ?: 10);
        
        $startTime = Carbon::parse($today->format('Y-m-d') . ' ' . ($startTimeSetting ?: $defaultStartTime));

       
        // =================================================================
        // 🛑 [VALIDASI 1]: CEK APAKAH SISWA BERASAL DARI KELAS INI
        // =================================================================
        // Catatan: Sesuaikan variabel '$class' dengan objek kelas wali/sesi yang kamu miliki di fungsi tersebut
        if ($student->class_code !== $class->code) {
            return response()->json([
                'success' => false,
                'message' => "❌ Akses Ditolak! Siswa bernama {$student->name} bukan anggota dari kelas {$class->name}.",
                'type'    => 'WRONG_CLASS'
            ], 403); // Menggunakan 403 karena tidak memiliki hak akses (Forbidden)
        }

        // =================================================================
        // 🛑 [VALIDASI 2]: CEK SCAN SEBELUM JAM MASUK DIBUKA
        // =================================================================
        if ($currentTime->lessThan($startTime)) {
            $timeRemaining = $startTime->diffForHumans($currentTime, [
                'parts' => 2,
                'join' => true,
                'syntax' => Carbon::DIFF_ABSOLUTE
            ]);
            
            $msg = "❌ Scan Gagal! Belum memasuki jam masuk kelas (Jam Masuk: " . substr($startTimeSetting, 0, 5) . "). Mohon tunggu {$timeRemaining} lagi.";

            return response()->json([
                'success' => false, 
                'message' => $msg,
                'type'    => 'EARLY_REJECTION'
            ], 403); 
        }
        // ===============================================================
        // =================================================================

        $toleranceTime = $startTime->copy()->addMinutes($toleranceMinutes);

        $status = 'Hadir';
        $lateDuration = null;

        // 🛠️ SEKARANG AMAN: Perbandingan berbasis Jam:Menit murni, agar abaikan hitungan detik sistem
        $currentTimeString = $currentTime->format('H:i');
        $toleranceTimeString = $toleranceTime->format('H:i');

        if ($currentTimeString > $toleranceTimeString) {
             $status = 'Terlambat';
            $lateDuration = round($currentTime->diffInMinutes($startTime));
        }

        // Catat Absensi Masuk
        Absence::create([
             'student_nisn' => $student->nisn,
             'attendance_time' => $currentTime,
             'status' => $status,
             'late_duration' => $lateDuration,
             'recorded_by' => Auth::check() ? Auth::user()->name : 'System Scan',
        ]);
        
        // Notifikasi WA MASUK/TERLAMBAT
        dispatch(function () use ($waService, $parentPhone, $student, $status, $currentTime, $lateDuration) {
            $this->sendWaNotification($waService, $parentPhone, $student->name, $status, $currentTime->format('H:i:s'), $lateDuration);
        })->afterResponse();
        

        $message = $student->name . ' berhasil MASUK. Status: ' . $status;
        if ($status === 'Terlambat') {
             $message .= " (+{$lateDuration} menit)";
        }

        return response()->json([
             'success' => true, 
             'message' => $message,
             'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'],
             'type' => 'IN',
             'status' => $status
        ]);
    }

    // -----------------------------------------------------------------
    // FUNGSI PENDUKUNG (CRUD TAMBAHAN & WA NOTIFIKASI)
    // -----------------------------------------------------------------
    
    /**
     * Helper function untuk mengirim WA notification ke nomor orang tua/wali.
     */
    private function sendWaNotification(WhatsAppService $waService, $phone, $studentName, $status, $time, $lateDuration = null)
    {
        if (!$phone) {
            Log::warning("No phone number found for student: {$studentName}. Skipping WA notification.");
            return;
        }

        // Tentukan pesan berdasarkan status
        if ($status == 'Hadir') {
            $msg = "Anak Anda, {$studentName}, telah berhasil absen MASUK pada pukul {$time}. Status: HADIR.";
        } elseif ($status == 'Terlambat') {
            $duration = $lateDuration ?? 0;
            $msg = "⚠️ Anak Anda, {$studentName}, absen MASUK TERLAMBAT pada pukul {$time}. Keterlambatan: {$duration} menit.";
        } elseif ($status == 'PULANG') {
            $msg = "Anak Anda, {$studentName}, telah absen PULANG pada pukul {$time}.";
        } else {
            return; // Lewati jika status Sakit/Izin/Alpha
        }
        
        // Panggil service untuk mengirim pesan
        $waService->sendNotification($phone, $msg);
    }
    
    /**
     * Hapus record absensi.
     */
    public function destroy(Absence $attendance)
    {
        $studentName = $attendance->student->name ?? 'Siswa';
        $attendance->delete();
        
        return redirect()->back()->with('success', "Absensi {$studentName} berhasil dihapus.");
    }
    
    /**
     * Tampilkan form edit absensi (Opsional).
     */
    public function manualEdit(Absence $attendance)
    {
        $students = Student::with('class')->where('status', 'active')->orderBy('name')->get();
        return view('walikelas.absensi.manual.edit', compact('attendance', 'students')); 
    }
    
    /**
     * Update/Edit status absensi manual.
     */
    public function manualUpdate(Request $request, Absence $attendance)
    {
        $request->validate([
             'status' => 'required|in:Hadir,Terlambat,Sakit,Izin,Alfa',
             'notes' => 'nullable|string|max:500',
             'nis' => 'required|string|exists:students,nis',
             'correction_reason' => 'required|string|max:500', 
        ]);
        
        $correctedBy = Auth::check() ? Auth::user()->name : 'System';

        // Update status, reason, dan field audit
        $attendance->update([
            'status' => $request->status,
            'notes' => $request->notes, // Jika keterangan izin/sakitnya ikut diubah
            'is_manual_corrected' => true,
            'corrected_by' => $correctedBy,
            'correction_note' => $request->correction_reason, // Kolom audit log tambahan jika ada
        ]);

        return redirect()->route('walikelas.absensi.manual.index')->with('success', "Status absensi {$attendance->student->name} berhasil diperbarui (Audit Logged).");
    }

    /**
     * 💡 [FITUR BARU] Mengirim notifikasi WhatsApp massal untuk semua siswa yang Absen (Sakit/Izin/Alpha) hari ini.
     * @param WhatsAppService $waService Service WA (di-inject oleh Laravel)
     */
    public function sendDailyAbsenceNotification(WhatsAppService $waService)
    {
        $user = Auth::user();
        $classId = $user->homeroomTeacher->class_code ?? null;

        if (!$classId) {
            return redirect()->back()->with('error', 'Akses ditolak: Anda belum mengampu kelas.');
        }

        $today = Carbon::today();
        $walikelasName = $user->name;
        $class = $user->homeroomTeacher->class->name ?? 'N/A';
        
        // Counter yang jujur
        $sentCount = 0;
        $failedCount = 0;

        // 1. Ambil semua catatan absensi hari ini yang berstatus SIA
        $absencesToNotify = Absence::whereDate('attendance_time', $today)
            ->whereIn('status', ['Sakit', 'Izin', 'Alfa'])
            ->whereHas('student', function ($query) use ($classId) {
                $query->where('class_code', $classId); 
            })
            ->with('student.parents')
            ->get();
            
        if ($absencesToNotify->isEmpty()) {
            return redirect()->back()->with('warning', 'Tidak ada siswa dengan status Sakit, Izin, atau Alfa hari ini di kelas Anda.');
        }

        // 2. Loop dan Kirim Notifikasi per Siswa
        foreach ($absencesToNotify as $absence) {
            $student = $absence->student;
            $parent = $student->parents->first(); 
            $phone = $parent ? $parent->phone_number : null;

            if ($phone) {
                $status = $absence->status;
                $reason = $absence->notes ? "Keterangan: {$absence->notes}" : '';
                
                $msg = "🔔 PEMBERITAHUAN KETIDAKHADIRAN 🔔\n\n"
                    . "Yth. Wali Murid {$student->name} (Kelas {$class}),\n\n"
                    . "Anak Anda tercatat **Absen** pada hari ini ({$today->isoFormat('D MMMM YYYY')}) dengan status:\n\n"
                    . "Status: *{$status}*\n"
                    . "{$reason}\n\n"
                    . "Pencatat: {$walikelasName}\n"
                    . "Terima kasih.";

                try {
                    // Panggil Service WA dan tangkap hasilnya
                    // Pastikan sendNotification mengembalikan nilai true/false
                    $isSent = $waService->sendNotification($phone, $msg);

                    if ($isSent) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                        Log::error("Fonnte reject message for: {$student->name}");
                    }
                    sleep(1);
                } catch (\Exception $e) {
                    // Jika cURL mati (undefined function curl_init), error akan ditangkap di sini
                    Log::error("WA Service Crash: " . $e->getMessage());
                    
                    // Jika error pertama kali langsung crash (biasanya cURL mati), 
                    // hentikan loop dan beri tahu user yang sebenarnya terjadi.
                    return redirect()->back()->with('error', 'Sistem Gagal: Extension cURL belum aktif di Laragon atau internet putus. Detail: ' . $e->getMessage());
                }
            } else {
                $failedCount++;
                Log::warning("WA Notif Gagal: Siswa {$student->name} tidak memiliki nomor HP wali.");
            }
        }
        
        // Berikan feedback yang detail
        if ($sentCount > 0) {
            $msgSuccess = "✅ Berhasil mengirim {$sentCount} notifikasi.";
            if ($failedCount > 0) {
                $msgSuccess .= " ({$failedCount} gagal, cek log/nomor HP).";
            }
            return redirect()->back()->with('success', $msgSuccess);
        }

        return redirect()->back()->with('error', "❌ Gagal mengirim notifikasi. Pastikan API Connected dan nomor HP benar.");
    }

    
    public function quickScanBypass($token)
    {
        // 1. Cari wali kelas berdasarkan token
        $teacher = \App\Models\HomeroomTeacher::where('scan_token', $token)->first();

        // 2. Jika token salah, kunci aksesnya
        if (!$teacher) {
            abort(403, 'Akses ditolak. Token tidak valid.');
        }

        // 3. Ambil data akun User milik wali kelas tersebut
        $user = $teacher->user; 

        if (!$user) {
            abort(404, 'Akun user tidak ditemukan.');
        }

        // 4. Login otomatis tanpa ketik password
        Auth::login($user);

        // 5. Alihkan langsung ke halaman kamera scan
        return redirect()->route('walikelas.absensi.scan');
    }

    // -----------------------------------------------------------------
    // HELPER PRIVATE
    // -----------------------------------------------------------------

    /**
     * Cek apakah hari ini adalah hari libur (weekend atau hari libur nasional).
     * Return array ['is_holiday' => bool, 'reason' => string]
     */
    private function checkHoliday(): array
    {
        $today = Carbon::today();

        // Cek Weekend (Sabtu & Minggu)
        if ($today->isWeekend()) {
            $dayName = $today->isSaturday() ? 'Sabtu' : 'Minggu';
            return ['is_holiday' => true, 'reason' => "Hari {$dayName} (Weekend)"];
        }

        // Cek Hari Libur Nasional dari database
        $holiday = \App\Models\Holiday::whereDate('date', $today)->first();
        if ($holiday) {
            return ['is_holiday' => true, 'reason' => $holiday->name];
        }

        return ['is_holiday' => false, 'reason' => ''];
    }
}