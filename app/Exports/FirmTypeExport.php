<?php

namespace App\Exports;

use App\Models\FirmType;
use Maatwebsite\Excel\Concerns\FromCollection;

class FirmTypeExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return FirmType::all();
    }
}
