<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class InstructionSheet implements FromArray, WithHeadings, ShouldAutoSize, WithTitle, WithStyles
{
    public function title(): string
    {
        return 'TEMPLATE_IMPORT';
    }

    public function headings(): array
    {
        return [
            'NISN',
            'NIS',
            'NAMA_SISWA',
            'JENIS_KELAMIN',
            'NAMA_KELAS',
            'TEMPAT_LAHIR',
            'BIRTH_DATE',
            'ADDRESS',
        ];
    }

    public function array(): array
    {
        return [
            [
                '1234567890',
                '12345',
                'Siti Nurhaliza',
                'Perempuan',
                '1A',
                'Jakarta',
                '2005-08-17',
                'Jl. Mawar No. 5, Jakarta Pusat',
            ]
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling baris 1 (header)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'name' => 'Arial',
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF4472C4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            // Styling baris 2 (contoh data)
            2 => [
                'font' => [
                    'name' => 'Arial',
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9E1F2'],
                ],
            ],
        ];
    }
}