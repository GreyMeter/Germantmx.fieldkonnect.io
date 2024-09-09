<?php

namespace App\Exports;

use App\Models\Complaint;
use App\Models\Customers;
use App\Models\OrderDetails;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Auth;


class ComplaintExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
{
    public function __construct($request)
    {
        $this->user_id = $request->input('user_id');
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
     
    }

    public function collection()
    {

        $query = Complaint::with('party', 'service_center_details', 'seller_details', 'customer', 'complaint_type_details', 'assign_user_details');
        if($this->start_date && $this->start_date != '' && $this->start_date != NULL && $this->end_date && $this->end_date != '' && $this->end_date != NULL){
            $query->whereBetween('created_at', [$this->start_date, $this->end_date]);
        }
        $query = $query->latest()->get();

        return $query;
    }

    public function headings(): array
    {
        return ['Complaint Number', 'End User', 'Complaint Date', 'Complaint Type', 'Purchased Party Name', 'Service Center', 'User Assign', 'Product Category', 'Product Serial Number', 'Product Code', 'Complaint Status'];
    }

    public function map($data): array
    {

        if($data->complaint_status == '0'){
            $status = 'Open';
        }elseif($data->complaint_status == '1'){
            $status = 'Pending';
        }elseif($data->complaint_status == '2'){
            $status = 'Work Done';
        }elseif($data->complaint_status == '3'){
            $status = 'Completed';
        }elseif($data->complaint_status == '4'){
            $status = 'Closed';
        }elseif($data->complaint_status == '5'){
            $status = 'Cancel';
        }


        return [
            $data['complaint_number'] ?? '',
            $data['customer']?$data['customer']['name'] : '',
            $data['complaint_date'] ?date("d/M/Y", strtotime($data->complaint_date)):'',
            $data['complaint_type_details']['name'] ?? '',
            $data['party']['name'] ?? '',
            $data['service_center_details']['name'] ?? '',
            $data['assign_user_details']['name'] ?? '',
            $data['category'] ?? '',
            $data['product_serail_number'] ?? '',
            $data['product_code'] ?? '',
            $status,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $lastRow = $event->sheet->getHighestDataRow() + 1;
              
                $event->sheet->getStyle('A1:K1')->applyFromArray([
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

                $event->sheet->getStyle('A' . $lastRow . ':AA' . $lastRow)->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'], // Border color
                        ],
                    ],
                ]);
            },
        ];
    }
}
