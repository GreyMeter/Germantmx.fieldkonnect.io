<?php

namespace App\Exports;

use App\Models\EmployeeDetail;
use App\Models\{CustomerOutstanting, ParentDetail, TransactionHistory, Redemption, MobileUserLoginDetails, Order};
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
        $this->start_date = $request->input('start_date');
        $this->end_date = $request->input('end_date');
        $this->po_no = $request->input('po_no');
        $this->customer_id = $request->input('customer_id');
        $this->dealer_id = $request->input('dealer_id');
    }

    public function collection()
    {
        $customerIds = EmployeeDetail::where('user_id', Auth::user()->id)->pluck('customer_id');

        $data = Order::with('order_confirm', 'customer', 'dispatchorders')->whereNotIn('status', ['4','5']);

        if (!Auth::user()->hasRole('superadmin')) {
            $data->whereIn('customer_id', $customerIds);
        }

        if ($this->start_date && !empty($this->start_date)) {
            $data->whereDate('created_at', '>=', $this->start_date);
        }

        if ($this->end_date && !empty($this->end_date)) {
            $data->whereDate('created_at', '<=', $this->end_date);
        }

        if ($this->po_no && !empty($this->po_no)) {
            $data->whereIn('po_no', $this->po_no);
        }
        if ($this->customer_id && !empty($this->customer_id)) {
            $data->whereIn('customer_id', $this->customer_id);
        }

        $data = $data->orderBy('created_at', 'desc')->get();

        $data = $data->filter(function ($item) {
            $confirmedQty = $item->order_confirm->pluck('qty')->sum();
            $pending = round((float)$item->qty - (float)$confirmedQty, 2);
            return $pending > 0.55;
        });

        return $data;
    }

    public function headings(): array
    {

        return [
            'Date',
            'PO Number',
            'Party Name',
            'Rate',
            'Order QTY',
            'Pending Dispatch QTY',
            'Dispatch QTY',
            'Pending QTY',
            'Days',
        ];
    }




    public function map($data): array
    {
        if (count($data->order_confirm) > 0) {
            if (($data->qty - $data->order_confirm->pluck('qty')->sum()) > 0) {
                $days = isset($data->created_at)
                    ? \Carbon\Carbon::parse($data->created_at->toDateString())->diffInDays(now()->toDateString())
                    : 0;
            } else {
                $lastCreatedAt = $data->order_confirm()
                    ->latest('created_at')
                    ->value('created_at');
                $days = \Carbon\Carbon::parse($data->created_at->toDateString())->diffInDays(\Carbon\Carbon::parse($lastCreatedAt)->toDateString());
            }
        } else {
            $days = isset($data->created_at)
                ? \Carbon\Carbon::parse($data->created_at->toDateString())->diffInDays(now()->toDateString())
                : 0;
        }

        return [
            isset($data->created_at) ? date('d M Y', strtotime($data->created_at)) : '-',
            $data->po_no,
            $data->customer->name,
            $data->base_price + $data->discount_amt,
            $data->qty,
            isset($data->order_confirm) && count($data->order_confirm) > 0 ? ($data->order_confirm->pluck('qty')->sum() - $data->dispatchorders->pluck('qty')->sum() > 0 ? $data->order_confirm->pluck('qty')->sum() - $data->dispatchorders->pluck('qty')->sum() : '0') : '0',
            isset($data->dispatchorders) && count($data->dispatchorders) > 0 ? $data->dispatchorders->pluck('qty')->sum() : '0',
            $data->qty - ($data->order_confirm?->pluck('qty')->sum() ?? 0) > 0 ? $data->qty - ($data->order_confirm?->pluck('qty')->sum() ?? 0) : '0',
            $days > 0 ? $days : '0',
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

                $event->sheet->getStyle('A1:' . $lastColumn . $lastRow)->applyFromArray([
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

                $totalRow = $lastRow + 2;
                $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
                $sheet->setCellValue('A' . $totalRow, 'Total : ');
                $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $columnsToSum = ['E', 'F', 'G', 'H'];
                foreach ($columnsToSum as $col) {
                    $formula = '=SUM(' . $col . '2:' . $col . $lastRow . ')';
                    $sheet->setCellValue($col . $totalRow, $formula);
                }
                $event->sheet->getStyle('A' . $totalRow . ':I' . $totalRow)->getFont()->setBold(true);
                $event->sheet->getStyle('E' . $totalRow . ':I' . $totalRow)->applyFromArray([
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
