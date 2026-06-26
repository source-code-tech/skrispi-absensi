<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class ParentAbsenceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $absences;

    public function __construct(Collection $absences)
    {
        $this->absences = $absences;
    }

    public function collection()
    {
        // Mengembalikan koleksi absensi yang sudah di-query dari Controller
        return $this->absences;
    }

    public function headings(): array
    {
        return [
            'ID Absensi',
            'Nama Anak',
            'Kelas',
            'Tanggal',
            'Waktu Masuk',
            'Waktu Pulang',
            'Status',
            'Keterangan',
            'Dikoreksi Oleh', // Log Audit
        ];
    }

    /**
     * Memetakan data dari objek Absensi ke baris Excel
     */
    public function map($absence): array
    {
        $status = $absence->status;
        $statusDetail = $absence->checkout_time ? $status . ' / PULANG' : $status;

        return [
            $absence->id,
            $absence->student->name ?? 'N/A',
            $absence->student->class->name ?? 'N/A',
            $absence->attendance_time->format('d-m-Y'),
            $absence->attendance_time->format('H:i'),
            $absence->checkout_time ? $absence->checkout_time->format('H:i') : '-',
            $statusDetail,
            $absence->notes ?? '-',
            $absence->corrected_by ?? 'Otomatis'
        ];
    }
}