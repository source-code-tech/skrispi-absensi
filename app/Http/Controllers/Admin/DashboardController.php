<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Absence;
use App\Models\HomeroomTeacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan Dashboard Super Admin dengan statistik utama.
     */
    public function index(Request $request) // Tambahkan Request $request
    {
        $today = Carbon::today();
        
        // --- 1. STATISTIK DATA MASTER & PENGGUNA ---
        
        // Total Kelas Aktif
        $totalClasses = ClassModel::where('status', 'active')->count();
        
        // Total Siswa Aktif
        $totalStudents = Student::where('status', 'active')->count();
        
        // Total Guru/Wali Kelas (Berdasarkan role: wali_kelas atau guru)
        $totalTeachers = User::whereIn('role', ['wali_kelas', 'guru'])->count();
        
        // Total Semua Akun Pengguna di Sistem
        $totalUsers = User::count();

        // ✅ TAMBAHAN: Total Akun Menunggu Persetujuan
        $pendingUsers = User::where('is_approved', false)
                            ->where('role', '!=', 'super_admin') // Tidak menghitung super admin
                            ->count();
        
        // --- 2. STATISTIK ABSENSI HARI INI ---

        //hitung siswa yg aktif
        $totalStudents = Student::where('status', 'active')->count();
        // Hitung siswa unik yang hadir/terlambat hari ini
        $presentToday = Absence::whereDate('attendance_time', $today)
                            ->whereIn('status', ['Hadir', 'Terlambat'])
                            ->distinct('student_nisn')
                            ->count('student_nisn');

        // Hitung persentase kehadiran
       $attendancePercentage = ($totalStudents > 0) ? round(($presentToday / $totalStudents) * 100) : 0;

        //Analitik Kehadiran 
        $classCode = $request->get('class_code'); // Untuk filter
        $classes = ClassModel::where('status', 'active')->get(); // Untuk dropdown filter

        $grafikTanggal = [];
        $grafikHadir = [];
        $grafikTerlambat = [];
        $grafikIzinSakit = [];
        $grafikAlfa = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $grafikTanggal[] = $date->translatedFormat('d M');

            // Query dasar absensi pada tanggal tersebut
            $query = Absence::whereDate('attendance_time', $date);

            // 💡 Tambahan: Jika admin memilih kelas tertentu, filter datanya
            if ($classCode) {
                $query->whereHas('student', function ($q) use ($classCode) {
                    $q->where('class_code', $classCode);
                });
            }

            // Ambil jumlah per status (Gunakan clone agar query dasar tidak rusak)
            $grafikHadir[] = (clone $query)->where('status', 'Hadir')->count();
            $grafikTerlambat[] = (clone $query)->where('status', 'Terlambat')->count();
            $grafikIzinSakit[] = (clone $query)->whereIn('status', ['Izin', 'Sakit'])->count();
            $grafikAlfa[] = (clone $query)->where('status', 'Alfa')->count();
        }
        
        $recentAbsences = Absence::with(['student.class'])
                    ->whereDate('attendance_time', $today)
                    ->orderBy('attendance_time', 'desc')
                    ->take(10)
                    ->get();

        
        return view('admin.dashboard', compact(
        'totalClasses', 'totalStudents', 'attendancePercentage', 'totalTeachers',
        'recentAbsences', 'totalUsers', 'pendingUsers',
        'classes', 'classCode', 'grafikTanggal', 'grafikHadir', 'grafikTerlambat', 'grafikIzinSakit', 'grafikAlfa'
    ));
    }
}