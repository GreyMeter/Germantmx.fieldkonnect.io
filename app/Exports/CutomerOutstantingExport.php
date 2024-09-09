<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\{CustomerOutstanting, ParentDetail, TransactionHistory, Redemption, MobileUserLoginDetails};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class CutomerOutstantingExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->branch_id = $request->input('branch_id');
        $this->dealer_id = $request->input('dealer_id');
    }

    public function collection()
    {
        DB::statement("SET SESSION group_concat_max_len = 100000000");
        $data = CustomerOutstanting::with('branch', 'customer')->select(
            'customer_id',
            'branch_id',
            'user_id',
            'year',
            'quarter',
            DB::raw('SUM(amount) as total_amounts'),
            DB::raw('GROUP_CONCAT(amount) as amounts'),
            DB::raw('GROUP_CONCAT(days) as days'),
            DB::raw('JSON_OBJECTAGG(days, amount) as day_amount_pairs'),
        )->groupBy('customer_id', 'branch_id', 'user_id', 'year', 'quarter',)->get();

        return $data;
    }

    public function headings(): array
    {

        return [
            'Branch',
            'Customer ID',
            'User ID',
            'Dealer Name',
            'Year',
            'Quarter',
            '0-30',
            '31-60',
            '61-90',
            '91-150',
            '>150',
            'Total Outstanding',
        ];
    }




    public function map($data): array
    {
        $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
        
        return [
            $data->branch ? $data->branch->branch_name : '-',
            $data->customer_id ? $data->customer_id : '-',
            $data->user_id ? $data->user_id : '-',
            $data->customer ? $data->customer->name : '-',
            $data->year ? $data->year : '-',
            $data->quarter ? $data->quarter : '-',
            $day_wise_amount_array['0-30'] ?? '0',
            $day_wise_amount_array['31-60'] ?? '0',
            $day_wise_amount_array['61-90'] ?? '0',
            $day_wise_amount_array['91-150'] ?? '0',
            $day_wise_amount_array['150'] ?? '0',
            $data->total_amounts ?? '0',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 2;
                $lastColumn = $event->sheet->getHighestDataColumn();

                $event->sheet->getStyle('A1:' . $lastColumn . '1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '336677'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getStyle('A2:' . $lastColumn . '' . ($lastRow - 2))->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ]);
            },
        ];
    }
}
