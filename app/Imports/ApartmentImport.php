<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class ApartmentImport implements ToArray
{
    public function array(array $rows)
    {
        return $rows;
    }
}
