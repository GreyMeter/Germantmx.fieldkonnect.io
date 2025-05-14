<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Customers;
use App\Models\Order;
use App\Models\OrderConfirm;
use App\Models\OrderDetails;
use App\Models\OrderDispatch;
use DB;
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
        $this->sizes = Category::where('active', 'Y')->get();

        $this->userids = getUsersReportingToAuth();
    }

    public function collection()
    {
        return OrderConfirm::with('order', 'brands', 'sizes', 'grades', 'order.customer')
            ->select([
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('GROUP_CONCAT(category_id) as sizes'),
                DB::raw('GROUP_CONCAT(qty) as qtys'),
                'special_cut',
                'brand_id',
                'consignee_details',
                'order_id',
                'confirm_po_no',
                DB::raw('COALESCE(unit_id, random_cut) as unit_id'),
            ])
            ->where(function ($query) {
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
            })
            ->groupBy('order_id', 'confirm_po_no', DB::raw('COALESCE(unit_id, random_cut)'))
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        $headings = ['PARTY NAME', 'CONSIGNEE/DESTINATION', 'GRADE', 'BRAND', 'SPECIAL CUT', 'TOTAL QTY', 'PENDING TOTAL', 'UP / DOWN'];

        foreach ($this->sizes as $key => $value) {
            $headings[] = $value->category_name . ' MM';
        }

        return $headings;
    }

    public function map($data): array
    {
        $all_order_size = explode(',', $data['sizes']);
        $all_order_qty = explode(',', $data['qtys']);
        $dispatch_qty = OrderDispatch::where('order_id', $data['order_id'])->where('confirm_po_no', $data['confirm_po_no'])->whereIn('category_id', $all_order_size)->sum('qty');
        $main_data = [
            $data['order'] ? ($data['order']['customer'] ? $data['order']['customer']['name'] : '-') : '-',
            $data['consignee_details'],
            $data['grades'] ? $data['grades']['unit_name'] : $data['unit_id']. '(Random Cut)',
            $data['brands'] ? $data['brands']['brand_name'] : '-',
            $data['special_cut'] ? $data['special_cut'] : '-',
            $data['total_qty'],
            $dispatch_qty > 0 ? $data['total_qty'] - $dispatch_qty : $data['total_qty'],
        ];

        foreach ($this->sizes as $size) {
            $found = false;
            foreach ($all_order_size as $k => $v) {
                if ($v == $size->id) {
                    $main_data[] = $all_order_qty[$k];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $main_data[] = '0';
            }
        }

        return $main_data;
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
