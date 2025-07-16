<?php

namespace App\Exports;

use App\Models\CheckIn;
use App\Models\OrderDetails;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class CheckinExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping,WithEvents
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->user_id = $request->input('user_id');
        $this->userids = getUsersReportingToAuth();
    }
    public function collection()
    {
        return CheckIn::with('customers', 'users', 'visitreports')->where(function ($query) {
            if ($this->user_id) {
                $query->where('user_id', $this->user_id);
            }elseif (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('user_id', $this->userids);
            }
            if ($this->startdate) {
                $query->whereDate('checkin_date', '>=', $this->startdate);
            }
            if ($this->enddate) {
                $query->whereDate('checkin_date', '<=', $this->enddate);
            }
        })
            ->select('id', 'customer_id', 'user_id', 'checkin_date', 'checkin_time', 'checkout_date', 'checkout_time', 'checkin_address', 'checkout_address', 'distance', 'beatscheduleid', 'created_at')
            ->orderBy('id', 'desc')->limit(5000)->get();
    }

    public function headings(): array
    {
        return ['id', 'Checkin Date', 'User ID', 'Employee Code', 'User Name', 'Designation', 'Checkin Time', 'Checkout Time', 'Spend Time', 'Checkin Address', 'Checkout Address', 'Distance', 'Customer Id', 'Customer Type', 'Customer Name', 'Customer Mobile', 'City', 'District', 'Address', 'Existing/New', 'Visit Remark'];
    }

    public function map($data): array
    {
        if(!empty($data->checkout_time) && !empty($data->checkin_time)){
            $parsedTime1 = Carbon::createFromFormat('H:i:s', $data->checkout_time);
            $parsedTime2 = Carbon::createFromFormat('H:i:s', $data->checkin_time);

            $difference = $parsedTime1->diff($parsedTime2);
            $interval = $difference->format('%H:%I:%S');
        }else{
            $interval = '-';
        }

        return [
            $data['id'],
            isset($data['checkin_date']) ? $data['checkin_date'] : '',
            isset($data['user_id']) ? $data['user_id'] : '',
            isset($data['users']['employee_codes']) ? $data['users']['employee_codes'] : '',
            isset($data['users']['name']) ? $data['users']['name'] : '',
            isset($data['users']['getdesignation']['designation_name']) ? $data['users']['getdesignation']['designation_name'] : '',

            isset($data['checkin_time']) ? $data['checkin_time'] : '',
            isset($data['checkout_time']) ? $data['checkout_time'] : '',
            $interval,
            isset($data['checkin_address']) ? $data['checkin_address'] : '',
            isset($data['checkout_address']) ? $data['checkout_address'] : '',
            isset($data['distance']) ? $data['distance'] : '',

            isset($data['customer_id']) ? $data['customer_id'] : '',
            isset($data['customers']['customertypes']['customertype_name']) ? $data['customers']['customertypes']['customertype_name'] : '',
            isset($data['customers']['name']) ? $data['customers']['name'] : '',
            isset($data['customers']['mobile']) ? $data['customers']['mobile'] : '',
            isset($data['customers']['customeraddress']['cityname']['city_name']) ? $data['customers']['customeraddress']['cityname']['city_name'] : '',

            isset($data['customers']['customeraddress']['districtname']['district_name']) ? $data['customers']['customeraddress']['districtname']['district_name'] : '',


            isset($data['customers']['customeraddress']['address1']) ? $data['customers']['customeraddress']['address1'] . ' ' . $data['customers']['customeraddress']['address2'] : '',
            (date("Y-m-d", strtotime($data['customers']['created_at'])) == date("Y-m-d", strtotime($data['checkin_date']))) ? 'New' : 'Existing',
            // isset($data['visitreports']['visittypename']['type_name']) ? $data['visitreports']['visittypename']['type_name'] : '',
            isset($data['visitreports']['description']) ? $data['visitreports']['description'] : '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestDataRow();
                $lastColumn = $sheet->getHighestDataColumn();

                $firstRowRange = 'A1:' . $lastColumn . '1';
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getStyle($firstRowRange)->getAlignment()->setWrapText(true);
                $sheet->getStyle($firstRowRange)->getFont()->setSize(14);

                $event->sheet->getStyle($firstRowRange)->applyFromArray([
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
                        'startColor' => ['rgb' => '00aadb'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                $event->sheet->getStyle('A1:' . $lastColumn . '' . $lastRow)->applyFromArray([
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
