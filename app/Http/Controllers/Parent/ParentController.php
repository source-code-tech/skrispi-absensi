<?php

namespace App\Http\Controllers\Parent;

use Carbon\Carbon;
use App\Models\Absence;
use App\Models\ParentModel;
use App\Models\Schedule; // Added this
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ParentAbsenceExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;


class ParentController extends Controller
{
    /**
     * Menampilkan dashboard Orang Tua (Fokus pada Statistik/Ringkasan).
     */
    public function index()
    {
        $user = Auth::user();
        $parentRecord = ParentModel::with('students.class.homeroomTeacher.user')
            ->where('user_id', $user->id)
            ->first();

        if (!$parentRecord) {
            return view('orangtua.dashboard', [
                'user' => $user,
                'parentRecord' => null,
                'totalSIA' => [] 
            ]);
        }

        $studentIds = $parentRecord->students->pluck('id');
        
        // Hitung Statistik Total Absensi SIA (Sejak Awal Semester/Waktu Tertentu)
        $totalSIA = Absence::whereIn('student_id', $studentIds)
                           ->select('status', DB::raw('count(*) as count'))
                           ->whereIn('status', ['Alpha', 'Sakit', 'Izin', 'Terlambat', 'Hadir'])
                           ->groupBy('status')
                           ->groupBy('status')
                           ->pluck('count', 'status')
                           ->toArray();

        // 💡 FETCH PENGUMUMAN (Baru)
        // Ambil ID kelas anak-anak
        $classIds = $parentRecord->students->pluck('class_id')->unique();
        
        $announcements = \App\Models\Announcement::where('is_active', true)
            ->where(function($query) use ($classIds) {
                $query->where('target_type', 'all')
                      ->orWhere(function($q) use ($classIds) {
                          $q->where('target_type', 'class')
                            ->whereIn('target_id', $classIds);
                      });
            })
            ->latest()
            ->take(5)
            ->get();

        // Riwayat absensi tidak dimuat di sini lagi, hanya statistik
        // Ambil 5 absensi terbaru untuk preview di dashboard
        $absences = Absence::with('student.class')
            ->whereIn('student_id', $studentIds)
            ->where('attendance_time', '>=', Carbon::now()->subDays(30))
            ->orderBy('attendance_time', 'desc')
            ->take(5)
            ->get();
        $dailyStatus = []; // Kirim koleksi kosong agar view tidak error
       
        
        return view('orangtua.dashboard', [
            'user' => $user,
            'parentRecord' => $parentRecord,
            'absences' => $absences, // Kosongkan atau pertahankan untuk kompatibilitas view
            'totalSIA' => $totalSIA, 
            'dailyStatus' => $dailyStatus,
            'announcements' => $announcements, // Pass ke view
        ]);
    }
    
    /**
     * 💡 [FUNGSI BARU] Menampilkan halaman Riwayat Absensi (Tabel 30 hari).
     */
    /**
     * Menampilkan halaman Riwayat Absensi (Tabel 30 hari).
     * Kami akan mempertahankan pagination untuk view.
     */
    public function showAbsenceHistory()
    {
        $user = Auth::user();
        $parentRecord = ParentModel::with('students.class')
            ->where('user_id', $user->id)
            ->first();

        if (!$parentRecord) {
            return redirect()->route('orangtua.dashboard')->with('error', 'Akun belum terhubung ke data siswa.');
        }

        $studentIds = $parentRecord->students->pluck('id');
        
        // Ambil riwayat absensi untuk semua anak (dalam 30 hari terakhir) dengan pagination
        $absences = Absence::with('student.class')
            ->whereIn('student_id', $studentIds)
            ->where('attendance_time', '>=', Carbon::now()->subDays(30))
            ->orderBy('attendance_time', 'desc')
            ->get();
        return view('orangtua.report.index', compact('parentRecord', 'absences'));
    }

    /**
     * 💡 [FITUR BARU] Export data Riwayat Absensi ke Excel/PDF.
     */
    public function exportHistory(Request $request, string $format = 'excel')
    {
        $user = Auth::user();
        $parentRecord = ParentModel::where('user_id', $user->id)->first();

        if (!$parentRecord) {
            return redirect()->route('orangtua.dashboard')->with('error', 'Akses Ditolak.');
        }
        
        $studentIds = $parentRecord->students->pluck('id');
        $parentName = $parentRecord->name;
        
        // Ambil SEMUA data tanpa pagination untuk export
        $absencesToExport = Absence::with('student.class')
            ->whereIn('student_id', $studentIds)
            ->where('attendance_time', '>=', Carbon::now()->subDays(30))
            ->orderBy('attendance_time', 'desc')
            ->get(); // Ambil koleksi penuh

        $fileName = 'Riwayat_Absensi_' . str_replace(' ', '_', $parentName) . '_' . Carbon::now()->format('Ymd_His');

        if ($format === 'pdf') {
            $data = [
                'absences'   => $absencesToExport,
                'parentName' => $parentName,
                'startDate'  => Carbon::now()->subDays(30),
                'endDate'    => Carbon::now(),
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('orangtua.report.pdf_template', $data);

    return $pdf->stream($fileName . '.pdf');
}

        // Export ke Excel (XLSX)
        return Excel::download(new ParentAbsenceExport($absencesToExport), $fileName . '.xlsx');
    }

    /**
     * Menampilkan jadwal pelajaran anak.
     */
    public function showSchedule()
    {
        $user = Auth::user();
        $parentRecord = ParentModel::with('students.class')->where('user_id', $user->id)->first();

        if (!$parentRecord) {
            return redirect()->route('orangtua.dashboard')->with('error', 'Akun belum terhubung ke data siswa.');
        }

        // Ambil semua jadwal untuk semua anak
        // Struktur: ['Nama Anak' => ['Senin' => [JadwalItems...], 'Selasa' => ...]]
        $schedules = [];
        
        foreach ($parentRecord->students as $student) {
            if ($student->class) {
                $classSchedules = Schedule::with('subject')
                    ->where('class_id', $student->class_id)
                    ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat')")
                    ->orderBy('start_time')
                    ->get()
                    ->groupBy('day');
                
                $schedules[$student->name] = $classSchedules;
            }
        }

        return view('orangtua.jadwal.index', compact('schedules'));
    }

    /**
     * Menampilkan detail satu record absensi, termasuk log audit.
     */
    public function showAbsenceDetail(Absence $absence)
    {
        $user = Auth::user();
        
        // 🛑 OTORISASI KRITIS: Pastikan record absensi ini milik anak dari user yang login
        $parentRecord = ParentModel::where('user_id', $user->id)->first();
        if (!$parentRecord || !$parentRecord->students->pluck('id')->contains($absence->student_id)) {
            abort(403, 'Akses Ditolak. Record absensi ini bukan milik anak Anda.');
        }

        $absence->load('student.class');

        return view('orangtua.absensi.show_detail', compact('absence'));
    }
}