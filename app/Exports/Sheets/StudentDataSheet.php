<?php

namespace App\Exports\Sheets;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat; // Diperlukan untuk NumberFormat

class StudentDataSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    private $rowNumber = 0; 

    public function title(): string
    {
        return 'Data Siswa Aktif';
    }

    /**
     * Ambil data koleksi siswa dari database.
     */
    public function collection()
    {
        return Student::with('class')
            ->orderBy('name', 'asc') // Urutkan berdasarkan nama agar lebih rapi di Excel
            ->get();
    }
    
    /**
     * Definisi Header/Judul Kolom Excel
     */
    public function headings(): array
    {
        return [
            'No', 
            'NISN',
            'NIS',
            'Nama Siswa',
            'Jenis Kelamin',
            'Nama Kelas',
            'Tingkat Kelas',
            'Tempat Lahir', 
            'Tanggal Lahir (yyyy-mm-dd)', // Disesuaikan untuk format Excel
            'Alamat', 
            'Data Barcode (ID Unik)',
            'Status',
            'Dibuat Pada',
        ];
    }
    
    /**
     * Mapping data ke kolom header
     */
    public function map($student): array
    {
        $this->rowNumber++; 
        
        // 💡 FIX: Pengecekan aman untuk birth_date dan created_at (karena sudah di-cast di Model)
        $birthDate = $student->birth_date ? $student->birth_date->format('Y-m-d') : null;
        $createdAt = $student->created_at ? $student->created_at->format('d/m/Y H:i') : null;
        
        return [
            $this->rowNumber, 
            $student->nisn,
            $student->nis,
            $student->name,
            $student->gender,
            $student->class->name ?? 'N/A', 
            $student->class->grade ?? 'N/A', 
            $student->birth_place, 
            $birthDate, // Sudah aman (Carbon/null)
            $student->address,
            $student->barcode_data,
            $student->status,
            $createdAt, 
        ];
    }
    
    /**
     * Tambahkan style pada header (baris 1) dan format kolom.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FF007BFF'], // Warna Biru Primary
                ]
            ],
            // 💡 FIX: Set kolom Tanggal Lahir (I) sebagai format teks yyyy-mm-dd
            'I' => ['numberFormat' => ['formatCode' => NumberFormat::FORMAT_DATE_YYYYMMDD2]], 
        ];
    }
}