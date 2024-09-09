<?php

namespace App\Exports;

use App\Models\Tasks;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class TasksExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function collection()
    {
        return Tasks::select('id','user_id', 'title', 'descriptions', 'datetime', 'reminder', 'completed_at', 'completed', 'is_done', 'customer_id', 'status_id', 'created_by', 'created_at')->latest()->get();   
    }

    public function headings(): array
    {
        return ['id','user_id', 'user_name', 'title', 'descriptions', 'datetime', 'reminder', 'completed_at', 'completed', 'is_done', 'customer', 'status'];
    }

    public function map($data): array
    {
        return [
            $data['id'],
            $data['user_id'],
            isset($data['users']['name']) ? $data['users']['name'] :'',
            $data['title'],
            $data['descriptions'],
            Date::dateTimeToExcel($data['datetime']),
            Date::dateTimeToExcel($data['reminder']),
            Date::dateTimeToExcel($data['completed_at']),
            $data['completed'],
            $data['is_done'],
            isset($data['customers']['name']) ? $data['customers']['name'] :'',
            isset($data['statusname']['status_name']) ? $data['statusname']['status_name'] :'',
        ];
    }

}