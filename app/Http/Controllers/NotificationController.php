<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getLatestNotifications()
    {
        $user = Auth::user();
        $limit = 5;
        
        // Tempat menampung semua jenis notifikasi sebelum disatukan
        $allNotifications = collect();

        // -----------------------------------------------------------------
        // ROLE 1: ORANG TUA
        // -----------------------------------------------------------------
        if ($user->role === 'orangtua') {
            $parentRecord = \App\Models\ParentModel::where('user_id', $user->id)->first();
            
            if ($parentRecord) {
                $studentIds = $parentRecord->students->pluck('id');
                $classIds = $parentRecord->students->pluck('class_id')->unique();

                // 1. Ambil Absen Anak Sendiri
                $absences = Absence::with(['student'])
                    ->whereIn('student_id', $studentIds)
                    ->latest()->take($limit)->get();

                foreach ($absences as $abs) {
                    $allNotifications->push([
                        'icon' => $this->getAbsenceIcon($abs->status),
                        'title' => "<strong>{$abs->student->name}</strong> tercatat {$abs->status}",
                        'time' => $abs->created_at->diffForHumans(),
                        'raw_time' => $abs->created_at,
                        'url' => route('orangtua.dashboard')
                    ]);
                }

                // 2. Ambil Pengumuman (Global / Kelas Anak)
                $announcements = \App\Models\Announcement::where('is_active', true)
                    ->where(function($query) use ($classIds) {
                        $query->where('target_type', 'all')
                              ->orWhere(function($q) use ($classIds) {
                                  $q->where('target_type', 'class')->whereIn('target_id', $classIds);
                              });
                    })->latest()->take($limit)->get();

                foreach ($announcements as $ann) {
                    $allNotifications->push([
                        'icon' => 'fas fa-bullhorn text-indigo-600',
                        'title' => "Pengumuman: <strong>{$ann->title}</strong>",
                        'time' => $ann->created_at->diffForHumans(),
                        'raw_time' => $ann->created_at,
                        'url' => '#'
                    ]);
                }
            }
        }
        
        // -----------------------------------------------------------------
        // ROLE 2: WALI KELAS
        // -----------------------------------------------------------------
        elseif ($user->role === 'walikelas') {
            // Cari data kelas yang dipegang guru ini
            $myClass = \App\Models\SchoolClass::whereHas('homeroomTeacher', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->first();

            if ($myClass) {
                // 1. Ambil Absen Siswa di Kelasnya
                $absences = Absence::with(['student'])
                    ->whereHas('student', function($q) use ($myClass) {
                        $q->where('class_id', $myClass->id);
                    })->latest()->take($limit)->get();

                foreach ($absences as $abs) {
                    $allNotifications->push([
                        'icon' => $this->getAbsenceIcon($abs->status),
                        'title' => "<strong>{$abs->student->name}</strong> ({$myClass->name}) tercatat {$abs->status}",
                        'time' => $abs->created_at->diffForHumans(),
                        'raw_time' => $abs->created_at,
                        'url' => route('admin.dashboard')
                    ]);
                }

                // 2. Ambil Pengajuan Izin yang statusnya 'Pending' dari kelasnya
                if (class_exists('\App\Models\Permission')) {
                    $permissions = \App\Models\Permission::with('student')
                        ->where('status', 'Pending')
                        ->whereHas('student', function($q) use ($myClass) {
                            $q->where('class_id', $myClass->id);
                        })->latest()->take($limit)->get();

                    foreach ($permissions as $perm) {
                        $allNotifications->push([
                            'icon' => 'fas fa-envelope-open-text text-purple-600',
                            'title' => "Siswa <strong>{$perm->student->name}</strong> mengajukan izin",
                            'time' => $perm->created_at->diffForHumans(),
                            'raw_time' => $perm->created_at,
                            'url' => route('walikelas.izin.index')
                        ]);
                    }
                }

                // 3. Ambil Pengumuman Sekolah (Target All atau Target Kelasnya)
                $announcements = \App\Models\Announcement::where('is_active', true)
                    ->where(function($query) use ($myClass) {
                        $query->where('target_type', 'all')
                              ->orWhere(function($q) use ($myClass) {
                                  $q->where('target_type', 'class')->where('target_id', $myClass->id);
                              });
                    })->latest()->take($limit)->get();

                foreach ($announcements as $ann) {
                    $allNotifications->push([
                        'icon' => 'fas fa-bullhorn text-indigo-600',
                        'title' => "Pengumuman: <strong>{$ann->title}</strong>",
                        'time' => $ann->created_at->diffForHumans(),
                        'raw_time' => $ann->created_at,
                        'url' => '#'
                    ]);
                }
            }
        }

        // -----------------------------------------------------------------
        // ROLE 3: ADMIN / SUPER ADMIN (Melihat Semua Data Global)
        // -----------------------------------------------------------------
        else {
            // 1. Absen Global terbaru
            $absences = Absence::with(['student.class'])->latest()->take($limit)->get();
            foreach ($absences as $abs) {
                // FIX: Logika ?? dikeluarkan dari kurung kurawal string agar tidak error
                $namaKelasSiswa = $abs->student->class->name ?? '-';
                
                $allNotifications->push([
                    'icon' => $this->getAbsenceIcon($abs->status),
                    'title' => "<strong>{$abs->student->name}</strong> ({$namaKelasSiswa}) tercatat {$abs->status}",
                    'time' => $abs->created_at->diffForHumans(),
                    'raw_time' => $abs->created_at,
                    'url' => route('admin.dashboard')
                ]);
            }

            // 2. Pengumuman baru dibuat
            $announcements = \App\Models\Announcement::latest()->take($limit)->get();
            foreach ($announcements as $ann) {
                $allNotifications->push([
                    'icon' => 'fas fa-bullhorn text-indigo-600',
                    'title' => "Admin mempublish: <strong>{$ann->title}</strong>",
                    'time' => $ann->created_at->diffForHumans(),
                    'raw_time' => $ann->created_at,
                    'url' => '#'
                ]);
            }

            // 3. Pengajuan izin masuk global (Pending)
            if (class_exists('\App\Models\Permission')) {
                $permissions = \App\Models\Permission::with('student')->where('status', 'Pending')->latest()->take($limit)->get();
                foreach ($permissions as $perm) {
                    $allNotifications->push([
                        'icon' => 'fas fa-envelope-open-text text-purple-600',
                        'title' => "Izin masuk baru: <strong>{$perm->student->name}</strong>",
                        'time' => $perm->created_at->diffForHumans(),
                        'raw_time' => $perm->created_at,
                        'url' => '#'
                    ]);
                }
            }
        }

        // -----------------------------------------------------------------
        // PROSES AKHIR: Sortir Berdasarkan Waktu Terbaru & Potong Jadi 5 Data
        // -----------------------------------------------------------------
        $sortedNotifications = $allNotifications
            ->sortByDesc('raw_time')
            ->take($limit)
            ->values();

        return response()->json([
            'count'         => $sortedNotifications->count(),
            'notifications' => $sortedNotifications
        ]);
    }

    /**
     * Helper privat untuk menentukan warna ikon absen
     */
    private function getAbsenceIcon($status)
    {
        $statusClean = strtolower($status);
        if ($statusClean === 'terlambat') {
            return 'fas fa-exclamation-triangle text-warning';
        } elseif ($statusClean === 'alpa' || $statusClean === 'alpha') {
            return 'fas fa-times-circle text-danger';
        } elseif ($statusClean === 'sakit' || $statusClean === 'izin') {
            return 'fas fa-envelope text-info';
        }
        return 'fas fa-user-check text-success';
    }
}