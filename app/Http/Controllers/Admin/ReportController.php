<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Absence;
use App\Models\Student;
use App\Models\Holiday;
use Barryvdh\DomPDF\Facade\Pdf; 
use App\Models\ClassModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsenceReportExport; 
use Illuminate\Support\Facades\DB;
use App\Exports\AbsenceRekapExport;

class ReportController extends Controller
{
    /**
     * Helper untuk mengambil data laporan berdasarkan filter.
     * Mengurutkan berdasarkan Tingkat Kelas, Nama Kelas, dan Nama Siswa.
     */
    private function getReportData(Carbon $startDate, Carbon $endDate, $classCode = null)
    {
        $query = Absence::with(['student.class'])
            ->whereBetween('attendance_time', [$startDate, $endDate]); 

        if ($classCode) {
            $query->whereHas('student', function ($q) use ($classCode) {
                $q->where('class_code', $classCode);
            });
        }
        
        // 💡 PENGURUTAN KUNCI: Menggunakan JOIN untuk OrderBy Relasi
        $query->join('students', 'absences.student_nisn', '=', 'students.nisn')
              ->join('classes', 'students.class_code', '=', 'classes.code')
              ->orderBy('classes.grade', 'asc') // Urutkan Tingkat (7, 8, 9)
              ->orderBy('classes.name', 'asc')  // Urutkan Kelas (7A, 7B)
              ->orderBy('students.name', 'asc') // Urutkan Nama Siswa di dalam Kelas
              ->orderBy('absences.attendance_time', 'asc') // Kemudian waktu absensi
              ->select('absences.*'); // Penting: Pilih kembali semua kolom dari tabel absences
              
        return $query->get();
    }

    private function getRekapData(Carbon $startDate, Carbon $endDate, $classCode = null)
{
    $absences = $this->getReportData($startDate, $endDate, $classCode);

    return $absences
        ->groupBy('student_nisn')
        ->map(function ($records) {
            $student = $records->first()->student;
            return [
                'student'   => $student,
                'kelas'     => $student->class->name ?? '-',
                'hadir'     => $records->where('status', 'Hadir')->count(),
                'terlambat' => $records->where('status', 'Terlambat')->count(),
                'sakit'     => $records->where('status', 'Sakit')->count(),
                'izin'      => $records->where('status', 'Izin')->count(),
                'alpha' => $records->where('status', 'Alfa')->count(),
                'total'     => $records->count(),
            ];
        })
        ->values();
}
    
    // -----------------------------------------------------------------
    // SUPER ADMIN REPORTS
    // -----------------------------------------------------------------

    /**
     * Tampilkan halaman filter laporan. (Super Admin)
     */
    public function index()
    {
        $classes = ClassModel::orderBy('grade')->orderBy('name')->get(); 
        return view('admin.reports.index', compact('classes'));
    }

    /**
     * Menampilkan hasil laporan absensi berdasarkan filter.
     */
    /**
     * Menampilkan hasil laporan absensi berdasarkan filter.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'class_code' => 'nullable|exists:classes,code',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ], [
            'start_date.required' => 'Tanggal awal wajib diisi.',
            'start_date.date' => 'Format tanggal awal tidak valid.',
            'end_date.required' => 'Tanggal akhir wajib diisi.',
            'end_date.date' => 'Format tanggal akhir tidak valid.',
            'end_date.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal awal.',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $classCode = $request->class_code;

        $absences = $this->getReportData($startDate, $endDate, $classCode);
        $class = $classCode ? ClassModel::find($classCode) : null;
        
        return view('admin.reports.result', compact('absences', 'startDate', 'endDate', 'class'));
    }

    /**
     * Export laporan ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_code' => 'nullable|exists:classes,code',
        ], [
            'start_date.required' => 'Tanggal awal wajib diisi.',
            'end_date.required' => 'Tanggal akhir wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir tidak valid.',
        ]);
        
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $classCode = $request->class_code;
        
        // Data absensi diambil menggunakan helper
        $absences = $this->getReportData($startDate, $endDate, $classCode);
        
        $className = $classCode ? ClassModel::find($classCode)->name : 'Semua-Kelas';

        $fileName = "Laporan_Absensi_{$className}_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.xlsx";

        
       return Excel::download(new AbsenceReportExport($absences, $startDate, $endDate, $className), $fileName);
    }

    public function exportExcelRekap(Request $request)
{
    $request->validate([
        'start_date' => 'required|date',
        'end_date'   => 'required|date|after_or_equal:start_date',
        'class_code'   => 'nullable|exists:classes,code',
    ], [
        'start_date.required'     => 'Tanggal awal wajib diisi.',
        'end_date.required'       => 'Tanggal akhir wajib diisi.',
        'end_date.after_or_equal' => 'Tanggal akhir tidak valid.',
    ]);

    $startDate = Carbon::parse($request->start_date)->startOfDay();
    $endDate   = Carbon::parse($request->end_date)->endOfDay();
    $classCode   = $request->class_code;

    $rekap     = $this->getRekapData($startDate, $endDate, $classCode);
    $className = $classCode ? ClassModel::find($classCode)->name : 'Semua-Kelas';

    $fileName  = "Rekap_Absensi_{$className}_{$startDate->format('Ymd')}_to_{$endDate->format('Ymd')}.xlsx";

    return Excel::download(new AbsenceRekapExport($rekap, $startDate, $endDate, $className), $fileName);
}
    /**
     * Export laporan ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'class_code' => 'nullable|exists:classes,code',
        ], [
            'start_date.required' => 'Tanggal awal wajib diisi.',
            'end_date.required' => 'Tanggal akhir wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir tidak valid.',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $classCode = $request->class_code;
        
        $absences = $this->getReportData($startDate, $endDate, $classCode);
        $class = $classCode ? ClassModel::find($classCode) : null;

        $data = [
            'absences' => $absences,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'class' => $class,
        ];
        
        $pdf = Pdf::loadView('admin.reports.pdf_template', $data); 
        
        $fileName = "Laporan_Absensi_" . ($class ? $class->name . "_" : "Semua_Kelas_") . $startDate->format('Ymd') . "-" . $endDate->format('Ymd') . ".pdf";
        
        return $pdf->stream($fileName);
    }

    // -----------------------------------------------------------------
    // WALI KELAS REPORTS
    // -----------------------------------------------------------------

    /**
     * Tampilkan halaman filter laporan absensi untuk Wali Kelas.
     */
    public function walikelasIndex()
    {
        $user = Auth::user();
        $class = $user->homeroomTeacher->class ?? null; 

        if (!$class) {
             return redirect()->route('walikelas.dashboard')
                              ->with('error', 'Anda belum mengampu kelas. Silakan hubungi admin untuk pengaturan.');
        }

        return view('walikelas.reports.index', compact('class')); 
    }

    /**
     * Menampilkan hasil laporan absensi untuk Wali Kelas.
     */
   public function walikelasGenerate(Request $request)
    {
        $user = Auth::user();
        $class = $user->homeroomTeacher->class ?? null;

        if (!$class) {
            return redirect()->route('walikelas.dashboard')->with('error', 'Anda belum mengampu kelas.');
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();
        $classCode = $class->code;

        // ============================================================
        // AUTO-ALFA: Sekarang sudah ditangani oleh Scheduler secara profesional
        // (Lihat file app/Console/Commands/AutoMarkAlpha.php dan routes/console.php)
        // ============================================================

        $absences = $this->getReportData($startDate, $endDate, $classCode);

        return view('walikelas.reports.result', compact('absences', 'startDate', 'endDate', 'class'));
    }
}