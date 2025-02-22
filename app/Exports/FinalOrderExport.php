<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\Order;
use App\Models\OrderConfirm;
use App\Models\OrderDetails;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class FinalOrderExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    public function __construct($request)
    {
        $this->pending_status = $request->input('pending_status');
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->order_id = $request->input('order_id');
        $this->customer_id = $request->input('customer_id');
        $this->customer_type_id = $request->input('customer_type_id');

        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return OrderConfirm::with('order', 'brands', 'sizes', 'grades', 'order.customer')->where( function ($query) {
            if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
                $query->whereIn('created_by', $this->userids);
            }
            if ($this->startdate) {
                $query->whereDate('created_at', '>=', $this->startdate);
            }
            if ($this->enddate) {
                $query->whereDate('created_at', '<=', $this->enddate);
            }
            if ($this->customer_id) {
                $query->where('customer_id', $this->customer_id);
            }
        })->latest()->get();
    }

    public function headings(): array
    {
        return ['id', 'Order Date', 'PO Number', 'Order Number', 'Distributor/Dealer Name', 'Brand', 'Grade', 'Size', 'Quantity', 'Base Price', 'consignee_details'];
        
    }

    public function map($data): array
    {
        return[
            $data['id'],
            date('d M Y', strtotime($data['created_at'])),
            $data['po_no'],
            $data['confirm_po_no'],
            $data['order']?($data['order']['customer']?$data['order']['customer']['name']:'-'):'-',
            $data['brands']?$data['brands']['brand_name']:'-',
            $data['grades']?$data['grades']['unit_name']:'-',
            $data['sizes']?$data['sizes']['category_name']:'-',
            $data['qty'],
            $data['base_price'],
            $data['consignee_details'],
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

                for ($col = 'A'; $col <= $lastColumn; $col++) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];
    }
}
