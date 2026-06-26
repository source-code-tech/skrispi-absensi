<?php

namespace App\Http\Controllers\Admin;

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

class CentralAbsenceController extends Controller
{
    /**
     * Tampilkan halaman Scan QR Code untuk Admin.
     */
    public function index()
    {
        $today = Carbon::today();
        
        $recentAbsences = Absence::with('student.class')
                                 ->whereDate('attendance_time', $today)
                                 ->orderBy('attendance_time', 'desc')
                                 ->take(10)
                                 ->get();

        return view('admin.absensi.scan_live', compact('recentAbsences'));
    }

    /**
     * Proses pencatatan absensi dari scan QR code (IN/OUT Logic).
     * @param WhatsAppService $waService Injeksi layanan WhatsApp
     */
    public function record(Request $request, WhatsAppService $waService)
    {
        // Validasi input barcode/QR data
        $request->validate(['barcode' => 'required|string']);

        $barcode_data = $request->barcode;
        
        // Eager load relasi 'class' untuk response AJAX
       $student = Student::with(['class', 'parents'])->where('barcode_data', $barcode_data)->first();

        if (!$student || $student->status !== 'active') {
            Log::warning("Absensi Gagal: QR/Barcode tidak valid atau siswa tidak aktif.", ['barcode' => $barcode_data]);
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau Siswa tidak ditemukan.'], 404);
        }

        $currentTime = Carbon::now();
        $today = Carbon::today();
        $parentPhone = $student->parents->first() ? $student->parents->first()->phone_number : null;
        
        // 💡 BARU: Muat Semua Pengaturan yang Diperlukan (termasuk waktu pulang)
        $settings = Cache::remember('attendance_settings', 3600, function () { 
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
        // Cek Record Masuk Hari Ini yang BELUM ADA WAKTU PULANGNYA
        $existingAbsence = Absence::where('student_id', $student->id)
                                 ->whereDate('attendance_time', $today)
                                 ->whereNull('checkout_time') 
                                 ->first();

        if ($existingAbsence) {
            // --- LOGIC SCAN OUT (PULANG) ---
            if (in_array($existingAbsence->status, ['Alfa', 'Sakit', 'Izin'])) {
                return response()->json([
                    'success' => false, 
                    'message' => "❌ Gagal Absen. Siswa sudah tercatat dengan status: {$existingAbsence->status} hari ini.",
                    'type' => 'INVALID_STATUS'
                ], 409);
            }
            
            // 💡 PENGECEKAN KUNCI: Apakah sudah waktunya pulang?
            if ($currentTime->lessThan($designatedEndTime)) {
                $timeRemaining = $designatedEndTime->diffForHumans($currentTime, [
                    'parts' => 2, // Hanya tampilkan Jam dan Menit
                    'join' => true,
                    'syntax' => Carbon::DIFF_ABSOLUTE
                ]);
                
                $message = "❌ Gagal Pulang. Belum waktunya pulang (Jam Pulang: {$displayEndTime}). Sisa waktu: {$timeRemaining} lagi.";
                Log::warning("Absensi Gagal: Siswa mencoba pulang sebelum waktunya.", ['student_id' => $student->id, 'current_time' => $currentTime]);
                
                // Mengembalikan response 403 (Forbidden) atau 409 (Conflict) untuk indikasi bisnis logic fail
                return response()->json(['success' => false, 'message' => $message], 409); 
            }
            
            // Jika sudah waktunya pulang, proses seperti biasa
            $existingAbsence->checkout_time = $currentTime;
            $existingAbsence->save();

            $message = $student->name . ' berhasil PULANG pada pukul ' . $currentTime->format('H:i:s') . '.';

            // KIRIM NOTIFIKASI PULANG
            $this->sendWaNotification($waService, $parentPhone, $student->name, 'PULANG', $currentTime->format('H:i:s'));
            
            return response()->json([
                'success' => true, 
                'message' => $message,
                'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'], 
                'type' => 'OUT'
            ]);

        } else {
            // --- LOGIC SCAN IN (MASUK) ---
            
            // Cek apakah sudah ada record IN/Manual hari ini (mencegah double IN/Manual)
            $alreadyScannedIn = Absence::where('student_id', $student->id)
                                      ->whereDate('attendance_time', $today)
                                      ->exists(); 

            if ($alreadyScannedIn) {
                $message = $student->name . ' sudah Absen MASUK hari ini.';
                Log::warning("Absensi Gagal: Siswa sudah masuk hari ini.", ['student_id' => $student->id]);
                return response()->json(['success' => false, 'message' => $message], 409);
            }

            // Tentukan Status Masuk (Hadir/Terlambat)
            
            // Ambil setting waktu masuk
            $startTimeSetting = $settings['attendance_start_time'] ?? '07:00'; // Asumsi sudah HH:MM
            $toleranceSetting = $settings['late_tolerance_minutes'] ?? 10;
            
            $defaultStartTime = '07:00'; 
            $toleranceMinutes = $toleranceSetting ? (int)$toleranceSetting : 10;
            
            // Waktu mulai yang ditetapkan (dengan penambahan detik :00 secara implisit)
            $startTime = Carbon::parse($today->format('Y-m-d') . ' ' . ($startTimeSetting ?: $defaultStartTime));
            // Batas waktu toleransi
            $toleranceTime = $startTime->copy()->addMinutes($toleranceMinutes);

            $status = 'Hadir';
            $lateDuration = null; // Pastikan defaultnya adalah NULL

            if ($currentTime->greaterThan($toleranceTime)) {
                $status = 'Terlambat';
                
                // Hitung durasi keterlambatan dari Waktu Mulai
               $lateDuration = round($currentTime->diffInMinutes($startTime));
            }
            
            // Catat Absensi Masuk
            Absence::create([
                'student_id' => $student->id,
                'attendance_time' => $currentTime,
                'status' => $status,
                'late_duration' => $lateDuration, 
                'recorded_by' => Auth::user()->name ?? 'Admin',
            ]);

            $message = $status == 'Terlambat' 
                        ? "TERLAMBAT: {$student->name} masuk pukul {$currentTime->format('H:i:s')} (+{$lateDuration} menit)." 
                        : "HADIR: {$student->name} masuk pukul {$currentTime->format('H:i:s')}.";
            
            // KIRIM NOTIFIKASI MASUK
            $this->sendWaNotification($waService, $parentPhone, $student->name, $status, $currentTime->format('H:i:s'), $lateDuration);
            
            return response()->json([
                'success' => true, 
                'message' => $message,
                'student' => ['name' => $student->name, 'class' => $student->class->name ?? 'N/A'], 
                'type' => 'IN',
                'status' => $status
            ]);
        }
    }
    
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
            return;
        }
        
        // Panggil service untuk mengirim pesan
        $waService->sendNotification($phone, $msg);
    }
}