<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingController; 
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Parent\ParentController;
use App\Http\Controllers\Parent\IzinRequestController;
use App\Http\Controllers\WaliKelas\AbsenceController; 
use App\Http\Controllers\Admin\CentralAbsenceController;
use App\Http\Controllers\WaliKelas\IzinProcessorController;
use App\Http\Controllers\Admin\ParentController as AdminParentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\WaliKelas\ParentController as WaliKelasParentController;
use App\Http\Controllers\WaliKelas\ReportController as WaliKelasReportController;
use App\Http\Controllers\WaliKelas\StudentController as WaliKelasStudentController;
use App\Http\Controllers\WaliKelas\DashboardController as WaliKelasDashboardController;
use App\Http\Controllers\Admin\HolidayController;

// =======================================================
// 1. RUTE PUBLIK & OTENTIKASI
// =======================================================

Route::get('/', [LandingController::class, 'index'])->name('landing');
// Rute Akses Instan Scan Menggunakan Token (Bypass Login)
Route::get('/scan-cepat/{token}', [App\Http\Controllers\WaliKelas\AbsenceController::class, 'quickScanBypass'])
    ->name('walikelas.quick_scan');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatestNotifications'])->name('notifications.latest');
});

require __DIR__.'/auth.php';

// =======================================================
// 2. RUTE SUPER ADMIN (admin/)
// =======================================================
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->group(function () {
    
    Route::get('dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // --- MANAJEMEN DATA (Resource Routes) ---
    Route::resource('classes', ClassController::class)->names('classes');
    Route::resource('teachers', TeacherController::class)->names('teachers');
    Route::resource('parents', AdminParentController::class)->names('parents');
    Route::resource('subjects', SubjectController::class)->names('admin.subjects');
    Route::resource('schedules', ScheduleController::class)->names('admin.schedules');
    Route::resource('announcements', AnnouncementController::class)->names('announcements');

    // Hari Libur
    Route::get('holidays', [HolidayController::class, 'index'])->name('admin.holidays.index');
    Route::post('holidays', [HolidayController::class, 'store'])->name('admin.holidays.store');
    Route::post('holidays/sync-google', [HolidayController::class, 'syncFromGoogle'])->name('admin.holidays.sync');
    Route::delete('holidays/{holiday}', [HolidayController::class, 'destroy'])->name('admin.holidays.destroy');

    // --- RUTE SISWA (STUDENTS) ---
    Route::prefix('students')->group(function () {
        Route::get('{student}/barcode', [StudentController::class, 'generateBarcode'])->name('students.barcode');
        Route::put('{student}/deactivate', [StudentController::class, 'deactivate'])->name('students.deactivate');
        Route::put('{student}/activate', [StudentController::class, 'activate'])->name('students.activate');
        Route::get('export', [StudentController::class, 'export'])->name('students.export');
        Route::get('import', [StudentController::class, 'importForm'])->name('students.importForm');
        Route::post('import', [StudentController::class, 'import'])->name('students.import');
        Route::get('barcode/bulk', [StudentController::class, 'generateBulkBarcode'])->name('students.barcode.bulk');
        Route::delete('bulk-delete', [StudentController::class, 'bulkDelete'])->name('students.bulkDelete'); 
        Route::resource('/', StudentController::class, ['parameters' => ['' => 'student']])
             ->names('students')->except(['show']); 
        Route::get('template', [StudentController::class, 'downloadTemplate'])
            ->name('students.downloadTemplate');
    });
    

    // =======================================================
    // RUTE MANAJEMEN PENGGUNA (USERS)
    // =======================================================
    Route::prefix('users')->group(function () {
        // CRUD utama
        Route::get('/', [UserController::class, 'index'])->name('admin.users.index');
        Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // Custom Actions
        Route::put('/{user}/toggle-approval', [UserController::class, 'toggleApproval'])->name('admin.users.toggleApproval');
        
        // ✅ RUTE BULK ACTION (Workaround GET)
        Route::get('/bulk-approve', [UserController::class, 'bulkApprove'])->name('admin.users.bulkApprove');
        Route::get('/bulk-delete', [UserController::class, 'bulkDelete'])->name('admin.users.bulkDelete');
        Route::get('/bulk-toggle', [UserController::class, 'bulkToggleApproval'])->name('admin.users.bulkToggleApproval');
    });


    // =======================================================
    // RUTE MODUL LAPORAN & PENGATURAN
    // =======================================================
   Route::prefix('report')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('report.index');
    Route::get('generate', [ReportController::class, 'generate'])->name('report.generate');
    Route::get('export/excel', [ReportController::class, 'exportExcel'])->name('report.export.excel');
    Route::get('export/excel-rekap', [ReportController::class, 'exportExcelRekap'])->name('report.export.excel-rekap'); // ← TAMBAH INI
    Route::get('export/pdf', [ReportController::class, 'exportPdf'])->name('report.export.pdf');
});

    // Modul Pengaturan
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

    // =======================================================
    // MODUL ABSENSI TERPUSAT (SCAN LIVE)
    // =======================================================
    Route::get('absensi', fn() => redirect()->route('admin.absensi.scan'));

    Route::prefix('absensi')->group(function () {
        Route::get('scan-kelas', [CentralAbsenceController::class, 'index'])->name('admin.absensi.scan'); 
        Route::post('record', [CentralAbsenceController::class, 'record'])->name('admin.absensi.record');
    });
});




// =======================================================
// 3. RUTE WALI KELAS (walikelas/)
// =======================================================
Route::middleware(['auth', 'role:wali_kelas'])->group(function () {
    
    Route::get('/walikelas/dashboard', [WaliKelasDashboardController::class, 'index'])->name('walikelas.dashboard');

    // MODUL SISWA (STUDENTS) - CRUD LENGKAP
    Route::prefix('walikelas/students')->group(function () {
    
        // 1. Resource CRUD (index, create, store, edit, update, destroy)
        //    Rute ini HARUS DULUAN untuk menangkap 'create' dan 'index'
        Route::resource('/', WaliKelasStudentController::class, ['parameters' => ['' => 'student']])
            ->names('walikelas.students')
            ->except(['show']); 
            
        // 2. Rute Eksplisit Non-Parameter (Bulk Actions)
        Route::delete('bulk-delete', [WaliKelasStudentController::class, 'bulkDelete'])->name('walikelas.students.bulkDelete'); 
        Route::get('barcode/bulk', [WaliKelasStudentController::class, 'generateBulkBarcode'])->name('walikelas.students.barcode.bulk');
        
        // 3. Rute Parameter (Harus diletakkan di paling bawah)
        Route::get('{student}', [WaliKelasStudentController::class, 'show'])->name('walikelas.students.show'); 
        Route::get('{student}/barcode', [WaliKelasStudentController::class, 'generateBarcode'])->name('walikelas.students.barcode');
        
    });
    
    // MODUL ABSENSI (Scan & Koreksi Data Log)
    Route::prefix('walikelas/absensi')->group(function () {
        Route::get('scan', [AbsenceController::class, 'scanForm'])->name('walikelas.absensi.scan');
        Route::post('record', [AbsenceController::class, 'record'])->name('walikelas.absensi.record');
        Route::delete('{attendance}', [AbsenceController::class, 'destroy'])->name('walikelas.absensi.destroy');

        // ABSENSI MANUAL / KOREKSI
        Route::prefix('manual')->group(function () {
            Route::get('/', [AbsenceController::class, 'manualIndex'])->name('walikelas.absensi.manual.index');
            Route::post('store', [AbsenceController::class, 'manualStore'])->name('walikelas.absensi.manual.store');
            Route::get('{attendance}/edit', [AbsenceController::class, 'manualEdit'])->name('walikelas.absensi.manual.edit');
            Route::put('{attendance}', [AbsenceController::class, 'manualUpdate'])->name('walikelas.absensi.manual.update');
            Route::delete('/walikelas/absensi/manual/{attendance}', [AbsenceController::class, 'destroy'])->name('walikelas.absensi.manual.destroy');
        });
    });

   // MODUL LAPORAN
Route::prefix('walikelas')->group(function () {

    // halaman filter laporan
    Route::get('report', [ReportController::class, 'walikelasIndex'])
        ->name('walikelas.report.index');

    // hasil laporan
    Route::get('report/generate', [ReportController::class, 'walikelasGenerate'])
        ->name('walikelas.report.generate');

    // export excel laporan harian
    Route::get('report/export/excel', [ReportController::class, 'exportExcel'])
        ->name('walikelas.report.export.excel');

    // ✅ export PDF pake controller wali kelas
    Route::get('report/export/pdf', [WaliKelasReportController::class, 'exportPdf'])
        ->name('walikelas.report.export.pdf');

    // rekap bulanan
    Route::get('report/monthly-recap', [WaliKelasReportController::class, 'monthlyRecap'])
        ->name('walikelas.report.monthly_recap');

    // export excel rekap bulanan
    Route::get('report/monthly-recap/export', [WaliKelasReportController::class, 'exportMonthlyRecap'])
        ->name('walikelas.report.monthly_recap.export');
});
    // Rute Kirim Notifikasi Absen Massal
    Route::post('send-daily-absences', [AbsenceController::class, 'sendDailyAbsenceNotification'])->name('walikelas.absensi.send_daily_absences');

    // MODUL ORANG TUA (Dikelola oleh Wali Kelas)
    Route::prefix('walikelas/parents')->group(function () {
        Route::get('{parent}/edit', [WaliKelasParentController::class, 'edit'])->name('walikelas.parents.edit');
        Route::put('{parent}', [WaliKelasParentController::class, 'update'])->name('walikelas.parents.update');
    });

    // MODUL PROSES IZIN
    Route::prefix('walikelas/izin')->group(function () {
        Route::get('/', [IzinProcessorController::class, 'index'])->name('walikelas.izin.index'); // Daftar permintaan
        Route::post('{izinRequest}/approve', [IzinProcessorController::class, 'approve'])->name('walikelas.izin.approve');
        Route::post('{izinRequest}/reject', [IzinProcessorController::class, 'reject'])->name('walikelas.izin.reject');
        Route::delete('{izinRequest}', [IzinProcessorController::class, 'destroy'])->name('walikelas.izin.destroy');
    });
});


// =======================================================
// 4. RUTE ORANG TUA (orangtua/)
// =======================================================
Route::middleware(['auth', 'role:orang_tua'])->group(function () {
    
    // 1. DASHBOARD (Ringkasan/Statistik)
    Route::get('/orangtua/dashboard', [ParentController::class, 'index'])->name('orangtua.dashboard');
    
    // 2. MODUL RIWAYAT & DETAIL
    // Rute Report Index (Untuk Tabel Riwayat Absensi)
    Route::get('/orangtua/report', [ParentController::class, 'showAbsenceHistory'])->name('orangtua.report.index'); // 💡 Rute Baru
    Route::get('/orangtua/report/export/{format}', [ParentController::class, 'exportHistory'])->name('orangtua.report.export');
    // Rute Detail Absensi
    Route::get('/orangtua/absensi/{absence}', [ParentController::class, 'showAbsenceDetail'])->name('orangtua.absensi.show_detail');

    // 💡 MODUL JADWAL PELAJARAN
    Route::get('/orangtua/jadwal', [ParentController::class, 'showSchedule'])->name('orangtua.jadwal.index');
    
    // 3. MODUL IZIN/SAKIT ONLINE
    Route::prefix('orangtua/izin')->group(function () {
        Route::get('/', [IzinRequestController::class, 'index'])->name('orangtua.izin.index');
        Route::post('store', [IzinRequestController::class, 'store'])->name('orangtua.izin.store');
    });
});


// =======================================================
// 5. PENGALIHAN DASHBOARD UTAMA
// =======================================================
Route::middleware('auth')->get('/dashboard', function () {
    // ✅ PERBAIKAN: Menambahkan Type Hinting PHPDoc untuk Intelephense
    /** @var \App\Models\User $user */ 
    $user = Auth::user(); 

    if ($user->isSuperAdmin()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isWaliKelas()) {
        return redirect()->route('walikelas.dashboard');
    } else {
        // Asumsi: Selain Super Admin dan Wali Kelas, sisanya adalah Orang Tua atau peran default lainnya.
        return redirect()->route('orangtua.dashboard');
    }
    
})->middleware('verified')->name('dashboard');