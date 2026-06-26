<?php

namespace App\Http\Controllers\WaliKelas;

use App\Models\Absence;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Exports\MonthlyRecapExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\ClassModel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Holiday;

class ReportController extends Controller
{
    public function monthlyRecap(Request $request)
    {
        $user = Auth::user();
        $class = $user->homeroomTeacher->class ?? null;

        if (!$class) {
            return redirect()->route('walikelas.dashboard')
                             ->with('error', 'Anda belum mengampu kelas.');
        }

        $classId = $class->id;

        $year = $request->input('year', Carbon::now()->year);
        $month = $request->input('month', Carbon::now()->month);
        
        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // 2. Ambil Siswa di Kelas yang Diampu
        $students = Student::where('class_id', $classId)
                           ->orderBy('name', 'asc')
                           ->get(); 
        
        // 4. Proses Data menjadi Struktur Pivot
        $recapData = [];
        $daysInMonth = $startOfMonth->daysInMonth;
        
        // Loop ini menggunakan $students sebagai object agar kita bisa ambil $student->id dan $student->nisn
        foreach ($students as $student) {
            $recapData[$student->id] = [
                'name' => $student->name,
                'nisn' => $student->nisn, // NISN ditambahkan di sini
                'status_by_day' => []
            ];
            
            for ($i = 1; $i <= $daysInMonth; $i++) {
                $currentDay = $startOfMonth->copy()->day($i);
                
                if ($currentDay->isFuture()) {
                    $recapData[$student->id]['status_by_day'][$i] = 'N/A';
                } elseif ($currentDay->isWeekend()) {
                    $recapData[$student->id]['status_by_day'][$i] = 'N/A';
                } elseif (Holiday::where('date', $currentDay->format('Y-m-d'))->exists()) {
                    $recapData[$student->id]['status_by_day'][$i] = 'N/A';
                } else {
                    $recapData[$student->id]['status_by_day'][$i] = 'N/A';
                }
            }
        }

        // 5. Isi status kehadiran
        $absences = Absence::whereIn('student_id', $students->pluck('id'))
                           ->whereBetween('attendance_time', [$startOfMonth, $endOfMonth->endOfDay()])
                           ->get();

        foreach ($absences as $absence) {
            $studentId = $absence->student_id;
            $day = $absence->attendance_time->day;
            $status = $absence->status;
            
            $recapData[$studentId]['status_by_day'][$day] = $status;
        }
        
        $data = [
            'class' => $class,
            'recapData' => $recapData,
            'daysInMonth' => $daysInMonth,
            'currentMonth' => $startOfMonth->isoFormat('MMMM YYYY'),
            'currentYear' => $year,
            'currentMonthNum' => $month,
        ];
        
        return view('walikelas.report.monthly_recap', $data);
    }


    public function exportPdf(Request $request)
{
    $user = Auth::user();
    $class = $user->homeroomTeacher->class ?? null;

    if (!$class) {
        return redirect()->route('walikelas.dashboard')
            ->with('error', 'Anda belum mengampu kelas.');
    }

    $request->validate([
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $startDate = Carbon::parse($request->start_date)->startOfDay();
    $endDate = Carbon::parse($request->end_date)->endOfDay();

    $absences = Absence::with(['student.class'])
        ->whereBetween('attendance_time', [$startDate, $endDate])
        ->whereHas('student', function ($q) use ($class) {
            $q->where('class_id', $class->id);
        })
        ->orderBy('attendance_time', 'asc')
        ->get();

    $data = [
        'absences' => $absences,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'class' => $class,
    ];

    $pdf = Pdf::loadView('admin.reports.pdf_template', $data);

    $fileName = "Laporan_Absensi_" . $class->name . ".pdf";

    return $pdf->stream($fileName);
}

    /**
     * 💡 [FITUR BARU] Proses Export data Rekap Absensi Bulanan ke Excel.
     */
   public function exportMonthlyRecap(Request $request)
{
    // 1. Panggil kembali logika monthlyRecap untuk mendapatkan data
    $response = $this->monthlyRecap($request); 

    // 2. Ambil data dari response view
    if ($response instanceof \Illuminate\View\View) {
        $data = $response->getData();
    } else {
        return $response; // Jika redirect (misal: belum mengampu kelas)
    }
    
    $recapData = $data['recapData'];
    $monthName = $data['currentMonth'];
    $className = $data['class']->name ?? 'Kelas';
    
    // 3. Buat nama file
    $fileName = 'Rekap_Absensi_' . $className . '_' . str_replace(' ', '_', $monthName) . '.xlsx';

    // 4. Panggil Export Class
    // PERBAIKAN: Gunakan $className yang sudah diambil dari $data['class']->name
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\MonthlyRecapExport($recapData, $monthName, $className), 
        $fileName
        );
    }
}
