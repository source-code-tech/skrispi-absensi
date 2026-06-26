<?php

namespace App\Exports;

use App\Models\Absence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill; // Import Fill Class
use Illuminate\Database\Eloquent\Collection; // 💡 Diperlukan untuk type hint Collection
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class AbsenceReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle, WithEvents
{
    private $absences;
    private $rowNumber = 0;
    private $startDate;
    private $endDate;
    private $className;

    public function __construct(Collection $absences, Carbon $startDate = null, Carbon $endDate = null, string $className = '')
    {
        $this->absences  = $absences;
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->className = $className;
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();

            // Insert 2 baris di atas untuk judul dan periode
            $sheet->insertNewRowBefore(1, 2);
            $sheet->setCellValue('A1', "Laporan Absensi - {$this->className}");
            $sheet->setCellValue('A2', "Periode: {$this->startDate->format('d/m/Y')} s/d {$this->endDate->format('d/m/Y')}");
            $sheet->mergeCells('A1:H1');
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
            $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');
        },
    ];
}

    /**
     * Mengatur nama sheet/lembar kerja di Excel.
     */
    public function title(): string
    {
        return 'Laporan Absensi';
    }
    
    /**
     * Mengembalikan koleksi absensi yang sudah di-query dari Controller.
     */
    public function collection()
    {
        // 💡 KUNCI: Langsung mengembalikan koleksi yang sudah disortir dan difilter
        return $this->absences;
    }
    
    /**
     * Definisi Header/Judul Kolom Excel.
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Waktu Absen',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Status',
            'Keterlambatan (Menit)',
        ];
    }
    
    /**
     * Mapping data ke kolom header.
     */
    public function map($absence): array
    {
        $this->rowNumber++;
        $status = $absence->status ?? 'N/A';
        
        return [
            $this->rowNumber,
            $absence->attendance_time->format('d/m/Y'),
            $absence->attendance_time->format('H:i:s'),
            $absence->student->nisn ?? 'N/A',
            $absence->student->name ?? 'Siswa Dihapus',
            $absence->student->class->name ?? 'N/A', 
            $status,
            ($status == 'Terlambat') ? $absence->late_duration . ' min' : '-',
        ];
    }
    
    /**
     * Tambahkan style pada header (baris 1).
     */
   public function styles(Worksheet $sheet)
{
    return [
        1 => [
            'font' => ['bold' => true, 'size' => 11, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FF1E40AF'], // ← ganti dari FF198754 ke FF1E40AF
            ]
        ],
    ];
}
}