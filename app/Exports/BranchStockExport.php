<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\{BranchStock, CustomerOutstanting, ParentDetail, TransactionHistory, Redemption, MobileUserLoginDetails};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class BranchStockExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $data = BranchStock::with('plant','brands','sizes','grades');
        // All filtters
        if($this->request->plant_id && !empty($this->request->plant_id)){
            $data->where('plant_id', $this->request->plant_id);
        }
        if($this->request->brand_id && !empty($this->request->brand_id)){
            $data->where('brand_id', $this->request->brand_id);
        }
        if($this->request->category_id && !empty($this->request->category_id)){
            $data->where('category_id', $this->request->category_id);
        }
        if($this->request->unit_id && !empty($this->request->unit_id)){
            $data->where('unit_id', $this->request->unit_id);
        }
        $data = $data->get();

        return $data;
    }

    public function headings(): array
    {

        return [
            'Plant Name',  
            'Brand', 
            'Size', 
            'Grade', 
            'Stock QTY',
        ];
    }




    public function map($data): array
    {
        $day_wise_amount_array = json_decode($data->day_amount_pairs, true);
        
        return [
            $data->plant ? $data->plant->plant_name : '-',
            $data->brands ? $data->brands->brand_name : '-',
            $data->sizes ? $data->sizes->category_name : '-',
            $data->grades ? $data->grades->unit_name : '-',
            $data->stock ? $data->stock : '-',
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
