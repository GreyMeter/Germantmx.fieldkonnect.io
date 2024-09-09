<?php

namespace App\Exports;

use App\Models\SalesTargetUsers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;

class SalesTargetUsersTemplate implements FromCollection,WithHeadings,ShouldAutoSize
{
    public function collection()
    {
        return SalesTargetUsers::select('user_id', 'month', 'year', 'target')->limit(0)->get();   
    }

    public function headings(): array
    {
        return [['User Id', 'Branch Id', 'User Name', 'Type', 'Month', 'Target Value'],['','','Add primary or secondary value only.please remove this row before upload.']];
    }

}