<?php

namespace App\Exports;

use App\Models\Zone;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class ZoneExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Zone::with('createdbyname')->latest()->get();   
    }

    public function headings(): array
    {
        return ['ID','Name', 'Created By'];
    }

    public function map($data): array
    {
        return [
            $data->id,
            $data->name,
            $data->createdbyname->name
        ];
    }

}