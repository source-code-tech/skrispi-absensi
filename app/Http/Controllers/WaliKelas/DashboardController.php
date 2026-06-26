<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Absence;
use App\Models\Student;
use App\Models\HomeroomTeacher;
use App\Models\IzinRequest; // 💡 Import Model IzinRequest
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; 
use Illuminate\Support\Facades\DB; 

class DashboardController extends Controller
{
  public function index()
    {
        $user = Auth::user();
        $homeroomTeacher = $user->homeroomTeacher ?? null;
        $class = $homeroomTeacher->class ?? null; 

        // Inisialisasi variabel statistik
        $classCode = $class ? $class->code : null; 
        $totalStudents = 0;
        $presentToday = 0;
        $absentToday = 0;
        $recentAbsences = collect(); 
        $warningStudents = collect();
        $dailyStats = []; 
        $pendingRequestsCount = 0; 
        
        $maxAlpha = 3;
        $maxSick = 5;
        $maxIzin = 5;

        // 💡 PERBAIKAN 1: Inisialisasi variabel grafik di sini
        $grafikTanggal = [];
        $grafikHadir = [];
        $grafikIzinSakit = [];
        $grafikAlpha = [];

        if ($classCode) {
            $today = Carbon::today();
            $totalStudents = Student::where('class_code', $classCode)->count();

            // 1. Ambil Semua Settings (termasuk batas absensi)
            $settings = Cache::remember('max_absensi_settings_full', 60*60, function () {
                return Setting::whereIn('key', ['attendance_start_time', 'max_alpha', 'max_sick', 'max_izin'])
                               ->pluck('value', 'key');
            });
            
            // Tentukan batas absensi dan pastikan tipe integer
            $maxAlpha = (int)($settings['max_alpha'] ?? 3);
            $maxSick = (int)($settings['max_sick'] ?? 5);
            $maxIzin = (int)($settings['max_izin'] ?? 5);

            // 2. Hitung Total Absensi SIA (Untuk Peringatan)
            $currentCounts = Absence::select('student_nisn', 'status', DB::raw('count(*) as count'))
                ->whereIn('status', ['Alfa', 'Sakit', 'Izin'])
                ->whereHas('student', function ($query) use ($classCode) {
                    $query->where('class_code', $classCode);
                })
                ->groupBy('student_nisn', 'status')
                ->get();

            // 3. Proses Peringatan
            foreach ($currentCounts as $count) {
                $maxLimit = 0;
                $warningType = '';

                if ($count->status == 'Alfa') {
                    $maxLimit = $maxAlpha;
                    $warningType = 'Alfa';
                } elseif ($count->status == 'Sakit') {
                    $maxLimit = $maxSick;
                    $warningType = 'Sakit';
                } elseif ($count->status == 'Izin') {
                    $maxLimit = $maxIzin;
                    $warningType = 'Izin';
                }

                if ($count->count >= $maxLimit && $maxLimit > 0) {
                    if (!$warningStudents->pluck('student_nisn')->contains($count->student_nisn)) {
                        $student = Student::find($count->student_nisn);
                        if ($student) {
                            $warningStudents->push([
                                'student_nisn' => $student->nisn,
                                'name' => $student->name,
                                'class_name' => $class->name,
                                'warning_status' => $warningType,
                                'count' => $count->count,
                                'max_limit' => $maxLimit
                            ]);
                        }
                    }
                }
            }
            
            // 4. Hitung Statistik Harian per Status
            $dailyStats = Absence::whereDate('attendance_time', $today)
                                  ->whereHas('student', function ($q) use ($classCode) {
                                      $q->where('class_code', $classCode);
                                  })
                                  ->select('status', DB::raw('count(DISTINCT student_nisn) as count'))
                                  ->groupBy('status')
                                  ->pluck('count', 'status')
                                  ->toArray();

            // 5. Hitung Hadir dan Sisa Siswa
            $presentToday = ($dailyStats['Hadir'] ?? 0) + ($dailyStats['Terlambat'] ?? 0);
            
            $recordedStudentsCount = Absence::whereDate('attendance_time', $today)
                                            ->whereHas('student', function ($q) use ($classCode) {
                                                $q->where('class_code', $classCode);
                                            })
                                            ->distinct('student_nisn')
                                            ->count('student_nisn');
                                            
            $absentToday = $totalStudents - $recordedStudentsCount;
            // Hitung total Izin dan Sakit
            $totalIzinSakit = ($dailyStats['Izin'] ?? 0) + ($dailyStats['Sakit'] ?? 0);
            // 6. Log Absensi Terbaru
            $recentAbsences = Absence::with('student.class')
                                     ->whereDate('attendance_time', $today)
                                     ->whereHas('student', function ($query) use ($classCode) {
                                         $query->where('class_code', $classCode);
                                     })
                                     ->orderBy('attendance_time', 'desc')
                                     ->take(5)
                                     ->get();
                                     
            // 7. Hitung Permintaan Izin Pending
            $pendingRequestsCount = IzinRequest::where('status', 'Pending')
                                               ->whereHas('student', function ($query) use ($classCode) {
                                                   $query->where('class_code', $classCode);
                                               })
                                               ->count();

            // --- REVISI KODE ANALITIK GRAFIK 7 HARI ---
                $grafikTanggal = [];
                $grafikHadir = [];
                $grafikTerlambat = []; // 💡 Tambahkan ini
                $grafikIzinSakit = [];
                $grafikAlpha = [];

                for ($i = 6; $i >= 0; $i--) {
                    $date = Carbon::today()->subDays($i);
                    $grafikTanggal[] = $date->translatedFormat('d M');

                    // Hadir (Murni Hadir saja)
                    $grafikHadir[] = Absence::whereDate('attendance_time', $date)
                        ->where('status', 'Hadir')
                        ->whereHas('student', function ($q) use ($classCode) {
                            $q->where('class_code', $classCode);
                        })->count();

                    // Terlambat (Pisahkan ke sini) 💡
                    $grafikTerlambat[] = Absence::whereDate('attendance_time', $date)
                        ->where('status', 'Terlambat')
                        ->whereHas('student', function ($q) use ($classCode) {
                            $q->where('class_code', $classCode);
                        })->count();

                    $grafikIzinSakit[] = Absence::whereDate('attendance_time', $date)
                        ->whereIn('status', ['Izin', 'Sakit'])
                        ->whereHas('student', function ($q) use ($classCode) {
                            $q->where('class_code', $classCode);
                        })->count();

                    $grafikAlpha[] = Absence::whereDate('attendance_time', $date)
                        ->where('status', 'Alfa')
                        ->whereHas('student', function ($q) use ($classCode) {
                            $q->where('class_code', $classCode);
                        })->count();
                     }
                        }

            // Kirim SEMUA data (statistik lama + data grafik baru)
        return view('walikelas.dashboard', [
            'user' => $user, 
            'class' => $class, 
            'totalStudents' => $totalStudents, 
            'presentToday' => $presentToday, 
            'absentToday' => $absentToday, 
            'recentAbsences' => $recentAbsences,
            'warningStudents' => $warningStudents, 
            'dailyStats' => $dailyStats,
            'maxLimits' => ['Alfa' => $maxAlpha, 'Sakit' => $maxSick, 'Izin' => $maxIzin],
            'pendingRequestsCount' => $pendingRequestsCount,
            'totalIzinSakit' => $totalIzinSakit,
            // Data Grafik
            'grafikTanggal' => $grafikTanggal,
            'grafikHadir' => $grafikHadir,
            'grafikTerlambat' => $grafikTerlambat, // Jangan lupa yang ini!
            'grafikIzinSakit' => $grafikIzinSakit,
            'grafikAlpha' => $grafikAlpha
            
        ]);
    }
}