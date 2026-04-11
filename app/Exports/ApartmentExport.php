<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApartmentExport implements FromArray, WithHeadings
{
    protected $columns;

    public function __construct($columns)
    {
        $this->columns = $columns;
    }

    public function array(): array
    {
        return []; // empty rows (only header export)
    }

    public function headings(): array
    {
        return $this->columns;
    }
}
