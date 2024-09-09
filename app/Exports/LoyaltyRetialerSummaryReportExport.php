<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\{ParentDetail, TransactionHistory, Redemption, MobileUserLoginDetails};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class LoyaltyRetialerSummaryReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping, WithEvents
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
        $retailers_sarthi = TransactionHistory::groupBy('customer_id')->pluck('customer_id');
        $userids = getUsersReportingToAuth();
        $data = Customers::with('customertypes', 'createdbyname', 'customeraddress.cityname', 'customeraddress.statename', 'customer_transacation')->whereIn('id', $retailers_sarthi)
            ->where(function ($query) use ($userids) {
                if ($this->branch_id && $this->branch_id != '' && $this->branch_id != null) {
                    $userIdsss = User::where('branch_id', $this->branch_id)->whereIn('id', $userids)->pluck('id');
                    $query->whereIn('executive_id', $userIdsss)
                    ->orWhereIn('created_by', $userIdsss);
                } else {
                    if(!auth()->user()->hasRole('superadmin') && !auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('Sub_Admin')){
                        $query->whereIn('executive_id', $userids)
                        ->orWhereIn('created_by', $userids);
                    }
                }

                if ($this->dealer_id && $this->dealer_id != '' && $this->dealer_id != null) {
                    $query->where('id', $this->dealer_id);
                }
            })->orderBy('id', 'asc')->get();

        return $data;
    }

    public function headings(): array
    {

        return [
            'Branch',
            'Retailer Id',
            'Retailer Name',
            'Distributor/Dealer Name',
            'State',
            'City',
            'Coupon Scan Nos',
            'Provision Point',
            'Active Point',
            'Total Point',
            'Redeem Gift',
            'Redeem Neft',
            'Total Redeem',
            'Balance Active Point'
        ];
    }




    public function map($data): array
    {
        $all_parents = '';
        if (count($data->getparentdetail) > 0) {
            foreach ($data->getparentdetail as $key => $value) {
                $all_parents .= $value->parent_detail?$value->parent_detail->name . ' ,':'';
            }
        }


        $coupon_scan_nos = TransactionHistory::where('customer_id', $data->id)->count();
        $thistorys = TransactionHistory::where('customer_id', $data->id)->get();
        $total_points = TransactionHistory::where('customer_id', $data->id)->sum('point') ?? 0;
        $active_points = 0;
        $provision_points = 0;
        foreach ($thistorys as $thistory) {
            if ($thistory->status == '1') {
                $active_points += $thistory->point;
            } else {
                $active_points += $thistory->active_point;
                $provision_points += $thistory->provision_point;
            }
        }
        $redeem_gift = Redemption::where('customer_id', $data->id)->whereNot('status', '2')->where('redeem_mode', '1')->sum('redeem_amount') ?? 0;
        $redeem_neft = Redemption::where('customer_id', $data->id)->whereNot('status', '2')->where('redeem_mode', '2')->sum('redeem_amount') ?? 0;
        $total_redemption = Redemption::where('customer_id', $data->id)->whereNot('status', '2')->sum('redeem_amount') ?? 0;
        $total_rejected = Redemption::where('customer_id', $data->id)->where('status', '2')->sum('redeem_amount') ?? 0;
        $total_balance = (int)$active_points - (int)$total_redemption;


        return [
            $data['createdbyname']['getbranch']['branch_name'] ?? '',
            $data['id'] ?? '',
            $data['name'] ?? '',
            $all_parents,
            $data['customeraddress']['statename']['state_name'] ?? '',
            $data['customeraddress']['cityname']['city_name'] ?? '',
            $coupon_scan_nos ?? '0',
            $provision_point ?? '0',
            $active_point ?? '0',
            $total_points ?? '0',
            $redeem_gift ?? '0',
            $redeem_neft ?? '0',
            $total_redemption ?? '0',
            $total_balance ?? '0',
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
            },
        ];
    }
}
