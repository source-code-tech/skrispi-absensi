<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AbsenceRekapExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected Collection $rekap;
    protected Carbon $startDate;
    protected Carbon $endDate;
    protected string $className;

    public function __construct(Collection $rekap, Carbon $startDate, Carbon $endDate, string $className)
    {
        $this->rekap     = $rekap;
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->className = $className;
    }

    public function collection(): Collection
    {
        return $this->rekap->map(function ($item, $index) {
            return [
                'no'        => $index + 1,
                'nisn'      => $item['student']->nisn ?? '-',
                'nama'      => $item['student']->name ?? '-',
                'kelas'     => $item['kelas'],
                'hadir'     => $item['hadir'],
                'terlambat' => $item['terlambat'],
                'sakit'     => $item['sakit'],
                'izin'      => $item['izin'],
                'alpha'     => $item['alpha'],
                'total'     => $item['total'],
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alfa', 'Total'];
    }

    public function title(): string
    {
        return 'Rekap Absensi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 25,
            'D' => 10,
            'E' => 10,
            'F' => 12,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 10,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E40AF']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $dataCount = $this->rekap->count();

                // Insert 2 baris header dulu di atas
                $sheet->insertNewRowBefore(1, 2);

                // Setelah insert, lastRow sudah benar (+2 baris header + 1 heading kolom)
                $lastRow = $dataCount + 3;

                // Isi baris judul dan periode
                $sheet->setCellValue('A1', "Rekap Absensi - {$this->className}");
                $sheet->setCellValue('A2', "Periode: {$this->startDate->format('d/m/Y')} s/d {$this->endDate->format('d/m/Y')}");
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
                $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

                // Center kolom angka
                $sheet->getStyle("E4:J{$lastRow}")->getAlignment()->setHorizontal('center');

                // Border semua data (setelah insert, range sudah benar)
                $sheet->getStyle("A3:J{$lastRow}")->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                // Pewarnaan status absensi
                $sheet->getStyle("E4:E{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF22C55E'); // Hadir - hijau
                $sheet->getStyle("F4:F{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF59E0B'); // Terlambat - orange
                $sheet->getStyle("G4:H{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF3B82F6'); // Sakit & Izin - biru
                $sheet->getStyle("I4:I{$lastRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFEF4444'); // Alpha - merah
            },
        ];
    }
}