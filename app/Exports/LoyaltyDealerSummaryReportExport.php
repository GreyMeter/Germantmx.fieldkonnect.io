<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\{ParentDetail,TransactionHistory,Redemption,MobileUserLoginDetails};
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoyaltyDealerSummaryReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
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
        // return Customers::where(function ($query)  {
        //                         if($this->userids)

        //                         {
        //                             $query->whereIn('executive_id', $this->userids);
        //                         }
        //                         if($this->startdate)
        //                         {
        //                             $query->whereDate('created_at','>=',$this->startdate);
        //                         }
        //                         if($this->enddate)
        //                         {
        //                             $query->whereDate('created_at','<=',$this->enddate);
        //                         }
        //                     })
        //                 ->select('id','name', 'first_name', 'last_name', 'mobile', 'email', 'latitude', 'longitude', 'customertype', 'created_at','created_by','executive_id','customer_code','contact_number','parent_id')
        //                 ->limit(5000)->latest()->get();   

        // return Branch::with(['getuser','getuser.createdbyname','getuser.getbranch'])->where(function ($query)  {

        //     if(!empty($this->branch_id))
        //     {
        //      $branch_user_id = User::whereIn('branch_id',$this->branch_id)->pluck('id');
        //     }

        // })
        // ->limit(5000)->latest()->get();   

    return Customers::with('customertypes', 'firmtypes', 'createdbyname','getretailers','customeraddress.cityname','customeraddress.statename','getretailers.redemption','getretailers.transactions','userdetails.getbranch')->where('customertype',['1','3'])
            ->where(function ($query) {
                $userids = getUsersReportingToAuth();
                if ($this->branch_id && $this->branch_id != '' && $this->branch_id != null) {
                    $userIdsss = User::where('branch_id', $this->branch_id)->whereIn('id', $userids)->pluck('id');
                    $query->whereIn('executive_id', $userIdsss);
                }else{
                    $query->whereIn('executive_id', $userids);
                }

                if ($this->dealer_id && $this->dealer_id != '' && $this->dealer_id != null) {
                    $query->where('id', $this->dealer_id);
                }
            })->limit(5000)->latest()->get();



    }

    public function headings(): array
    {

        return ['Dealer & Distributor Firm Name','State','City','Branch','Total Retailer Registred Nos','Total Retailer Under Saarthi Nos','Coupon Scan Nos','Mobile App Donwload Nos','Provision Point','Active Point','Total Point','Redeem Gift','Redeem Neft','Total Redeem','Balance Active Point'
        ];
    }


    

    public function map($data): array
    {
        $customerIds= $data->getretailers->pluck('customer_id');
        $total_registered_retailers = $data->getretailers->count();
        $total_retailers_under_saarthi = TransactionHistory::whereIn('customer_id',$customerIds)->groupBy('customer_id')->count();
        $coupon_scan_nos = TransactionHistory::whereIn('customer_id',$customerIds)->count();
        $mobile_app_downloads = MobileUserLoginDetails::whereIn('customer_id',$customerIds)->count();
        $provision_point = TransactionHistory::whereIn('customer_id',$customerIds)->where('status','0')->sum('point');
        $active_point = TransactionHistory::whereIn('customer_id',$customerIds)->where('status','1')->sum('point');
        $total_point = $provision_point + $active_point;
        $redeem_gift = Redemption::with('customer')->where('status','!=' , '2')->whereIn('customer_id',$customerIds)->where('redeem_mode','1')->sum('redeem_amount');
        $redeem_neft = Redemption::with('customer')->where('status','!=' , '2')->whereIn('customer_id',$customerIds)->where('redeem_mode','2')->sum('redeem_amount');
        $total_redeem = $redeem_gift + $redeem_neft;
        $balance_active_point = $total_redeem - $total_point;


        // $coupon_scan_nos = TransactionHistory::whereIn('customer_id',$customerIds)->count();
        // $mobile_app_downloads = MobileUserLoginDetails::whereIn('customer_id',$customerIds)->count();
        // $provision_point = TransactionHistory::whereIn('customer_id',$customerIds)->where('point','0')->sum('point');
        // $active_point = TransactionHistory::whereIn('customer_id',$customerIds)->where('point','1')->sum('point');
        // $total_point = $provision_point + $active_point;
        // $redeem_gift = Redemption::with('customer')->where('status','!=' , '2')->whereIn('customer_id',$customerIds)->where('redeem_mode','1')->sum('redeem_amount');
        // $redeem_neft = Redemption::with('customer')->where('status','!=' , '2')->where('redeem_mode','2')->sum('redeem_amount');
        // $total_redeem = $redeem_gift + $redeem_neft;
        // $balance_active_point = $total_redeem - $total_point;


        // $data['id'];
        // $data['total_retailers_under_saarthi'] = $nosOfRetailerRegistredSaarthi;
        // $data['coupon_scan_nos'] = $coupon_scan_nos;
        // $data['mobile_app_downloads'] = $mobile_app_downloads;
        // $data['provision_point'] = $provision_point;
        // $data['active_point'] = $active_point;
        // $data['total_point'] = $total_point;
        // $data['redeem_gift'] = $redeem_gift;
        // $data['redeem_neft'] = $redeem_neft;
        // $data['total_redeem'] = $total_redeem;
        // $data['balance_active_point'] = $balance_active_point;

        return [
            
               $data['name'] ?? '',
               $data['customeraddress']['statename']['state_name'] ?? '', 
               $data['customeraddress']['cityname']['city_name'] ?? '',
               $data['userdetails']['getbranch']['branch_name'] ?? '',
               $total_registered_retailers ?? '0',
               $total_retailers_under_saarthi ?? '0',
               $coupon_scan_nos ?? '0',
               $mobile_app_downloads ?? '0',
               $provision_point ?? '0',
               $active_point ?? '0',
               $total_point ?? '0',
               $redeem_gift ?? '0',
               $redeem_neft ?? '0',
               $total_redeem ?? '0',
               $balance_active_point ?? '0',
           
        ];
    }
}