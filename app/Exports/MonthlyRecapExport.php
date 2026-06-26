<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\AfterSheet;

class MonthlyRecapExport implements FromArray, WithHeadings, WithColumnWidths, WithEvents
{
    protected $recapData;
    protected $monthName;
    protected $className; // Tambahan untuk nama kelas

    public function __construct(array $recapData, string $monthName, string $className)
    {
        $this->recapData = $recapData;
        $this->monthName = $monthName;
        $this->className = $className;
    }

    public function array(): array
    {
        $exportArray = [];
        foreach ($this->recapData as $data) {
            $summary = ['Hadir' => 0, 'Terlambat' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alfa' => 0];
            foreach ($data['status_by_day'] as $status) {
                if ($status === 'Pulang') $status = 'Hadir';
                if (isset($summary[$status])) $summary[$status]++;
            }

            $exportArray[] = [
                'Nama Siswa' => $data['name'],
                'NISN'       => $data['nisn'] ?? '-',
                'Hadir'      => $summary['Hadir'],
                'Terlambat'  => $summary['Terlambat'],
                'Sakit'      => $summary['Sakit'],
                'Izin'       => $summary['Izin'],
                'Alfa'      => $summary['Alfa'],
            ];
        }
        return $exportArray;
    }

    public function headings(): array
    {
        return ['Nama Siswa', 'NISN', 'Hadir', 'Terlambat', 'Sakit', 'Izin', 'Alfa'];
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 18, 'C' => 10, 'D' => 13, 'E' => 10, 'F' => 10, 'G' => 10];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = count($this->recapData) + 4; // Ditambah karena ada tambahan baris judul

                // Header & Judul
                $sheet->insertNewRowBefore(1, 3);
                $sheet->setCellValue('A1', 'Rekap Absensi Bulanan - ' . $this->className);
                $sheet->setCellValue('A2', 'Periode: ' . $this->monthName);
                $sheet->setCellValue('A3', 'Dicetak pada: ' . now()->format('d/m/Y H:i'));
                
                $sheet->mergeCells('A1:G1');
                $sheet->mergeCells('A2:G2');
                $sheet->mergeCells('A3:G3');
                
                // Styling Judul
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Header Tabel Style
                $sheet->getStyle('A4:G4')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E40AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Pewarnaan Status
                $sheet->getStyle('C5:C' . $lastRow)->getFill()->setFillType('solid')->getStartColor()->setARGB('FF22C55E'); // Hijau
                $sheet->getStyle('D5:D' . $lastRow)->getFill()->setFillType('solid')->getStartColor()->setARGB('FFF59E0B'); // Kuning
                $sheet->getStyle('E5:F' . $lastRow)->getFill()->setFillType('solid')->getStartColor()->setARGB('FF3B82F6'); // Biru
                $sheet->getStyle('G5:G' . $lastRow)->getFill()->setFillType('solid')->getStartColor()->setARGB('FFEF4444'); // Merah

                // Border
                $sheet->getStyle('A4:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4:G' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}