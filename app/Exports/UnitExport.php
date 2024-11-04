<?php

namespace App\Exports;

use App\Models\Plant;
use App\Models\UnitMeasure;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class UnitExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Plant::select('id','plant_name')->latest()->get();   
    }

    public function headings(): array
    {
        return ['ID','Unit Name'];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->plant_name,
            // $data->unit_code,
        ];
    }

}