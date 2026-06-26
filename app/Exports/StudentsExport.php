<?php

namespace App\Exports;

use App\Models\Student;
use App\Exports\Sheets\StudentDataSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentsExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            new StudentDataSheet(), // Hanya data siswa, tanpa template
        ];
    }
}