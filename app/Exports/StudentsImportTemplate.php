<?php

namespace App\Exports;

use App\Exports\Sheets\InstructionSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class StudentsImportTemplate implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return [
            new InstructionSheet(),
        ];
    }
}