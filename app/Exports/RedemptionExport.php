<?php

namespace App\Exports;

use App\Models\Redemption;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Customers;
use App\Models\SchemeDetails;
use App\Models\EmployeeDetail;
use App\Models\ParentDetail;
use App\Models\SchemeHeader;
use App\Models\Services;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;


class RedemptionExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    public function __construct($request)
    {
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->branch_id = $request->input('branch_id');
        $this->parent_customer = $request->input('parent_customer');
        $this->redeem_mode = $request->input('redeem_mode');
        $this->customer_id = $request->input('customer_id');
    }

    public function collection()
    {

        $data = Redemption::with('customer', 'product', 'neft_details');
        if ($this->branch_id && $this->branch_id != null && count($this->branch_id) > 0) {
            $branch_user_id = User::whereIn('branch_id', $this->branch_id)->pluck('id');
            if (!empty($branch_user_id)) {
                $branch_customer_id = Customers::whereIn('executive_id', $branch_user_id)->pluck('id');
            }
            if (!empty($branch_customer_id)) {
                $data->whereIn('customer_id', $branch_customer_id);
            }
        }
        if ($this->parent_customer && $this->parent_customer != null  && count($this->parent_customer) > 0) {
            $parent_customer_id = ParentDetail::whereIn('parent_id', $this->parent_customer)->pluck('customer_id');

            if (!empty($parent_customer_id)) {
                $data->whereIn('customer_id', $parent_customer_id);
            }
        }
        if ($this->customer_id && $this->customer_id != null  && $this->customer_id != '') {
            $data->where('customer_id', $this->customer_id);
        }
        if($this->redeem_mode && $this->redeem_mode != null  && $this->redeem_mode != ''){
            $data->where('redeem_mode', $this->redeem_mode);
        }
        if ($this->startdate && $this->startdate != null && $this->startdate != '' && $this->enddate && $this->enddate != null && $this->enddate != '') {
            $startDate = date('Y-m-d', strtotime($this->startdate));
            $endDate = date('Y-m-d', strtotime($this->enddate));
            $data = $data->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        }
        $data = $data->latest()->get();
        return $data;
    }

    public function headings(): array
    {
        if($this->redeem_mode == '1'){
            return ['Id', 'Date', 'Customer Id', 'Firm Name', 'Contact Person', 'Parent Code', 'Parent Name', 'Mobile Number', 'City', 'District', 'State', 'Branch', 'Div', 'Category Name', 'Prodcut Name', 'Point', 'Status', 'Dispatch Date', 'Dispatch Number', 'Gift Recived Date', 'Received Remark'];
        }elseif($this->redeem_mode == '2'){
            return ['Id', 'Date', 'Customer Id', 'Firm Name', 'Contact Person', 'Parent Code', 'Parent Name', 'Mobile Number', 'City', 'District', 'State', 'Branch', 'Div', 'Redeem Mode', 'Redeem Point', 'Status', 'Bank Name', 'Account Holder Name', 'Account Number', 'IFSC Code', 'Adhar Number', 'PAN Number', 'TDS Dedcution %', 'TDS Amount', 'Final Pay', 'Payment Date', 'Trasaction Id'];
        }
    }

    public function map($data): array
    {
        // $data['gmap_address'] = UserActivity::where('customerid','=',$data['id'])->where('type','=','Counter Created')->pluck('address')->first();
        $data['gmap_address'] = UserActivity::where('customerid', '=', $data['customer']['id'])->pluck('address')->first();


        //new fields start

        $employee = array();
        $employee_id = array();

        if (!empty($data['customer']['getemployeedetail'])) {
            foreach ($data['customer']['getemployeedetail'] as $key_new => $datas) {

                $employee[] = isset($datas->employee_detail->name) ? $datas->employee_detail->name : '';
                $employee_id[] = isset($datas->user_id) ? $datas->user_id : '';
            }
        }

        $parent = array();
        $parent_id = array();
        $parent_code = array();
        if (!empty($data['customer']['getparentdetail'])) {
            foreach ($data['customer']['getparentdetail'] as $key => $parent_data) {
                $parent[] = isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name : '';
                $parent_code[] = isset($parent_data->parent_detail->customer_code) ? $parent_data->parent_detail->customer_code : '';
                $parent_id[] = isset($parent_data->parent_id) ? $parent_data->parent_id : '';
            }
        }


        $getdesignation_arr = array();
        $branch_arr = array();
        $division_arr = array();
        $empcode_arr = array();
        if (!empty($data['customer']['getemployeedetail'])) {
            foreach ($data['customer']['getemployeedetail'] as $key_new => $datas) {
                $getdesignation_arr[] = isset($datas->employee_detail->getdesignation->designation_name) ? $datas->employee_detail->getdesignation->designation_name : '';
                $branch_arr[] = isset($datas->employee_detail->getbranch->branch_name) ? $datas->employee_detail->getbranch->branch_name : '';
                $division_arr[] = isset($datas->employee_detail->getdivision->division_name) ? $datas->employee_detail->getdivision->division_name : '';
                $empcode_arr[] = isset($datas->employee_detail->employee_codes) ? $datas->employee_detail->employee_codes : '';
            }
        }

        // $scheme_details = SchemeDetails::where('product_id', $data['scheme']['product']['id'])->first();        

        //new fields end

        if($this->redeem_mode == '1'){
            return [
                $data['id'],
                $data['created_at'] = isset($data['created_at']) ? date("d-M-Y", strtotime($data['created_at'])) : '',
                $data['customer']['id'],
                $data['customer']['name'],
                $data['customer']['first_name'].' '.$data['customer']['last_name'],
                // implode(',', $employee),
                implode(',', $parent_code),
                implode(',', $parent),
                "'".$data['customer']['mobile'],
                $data['city_name'] = isset($data['customer']['customeraddress']['cityname']['city_name']) ? $data['customer']['customeraddress']['cityname']['city_name'] : '',
                $data['district_name'] = isset($data['customer']['customeraddress']['districtname']['district_name']) ? $data['customer']['customeraddress']['districtname']['district_name'] : '',
                $data['state_name'] = isset($data['customer']['customeraddress']['statename']['state_name']) ? $data['customer']['customeraddress']['statename']['state_name'] : '',
                implode(',', $branch_arr),
                implode(',', $division_arr),
                $data['product']['categories']['category_name'],
                $data['product']['product_name'],
                $data['redeem_amount'],
                (($data['status'] == '0') ? 'Pendding' : (($data['status'] == '1') ? 'Approved' : (($data['status'] == '2') ? 'Rejected' : (($data['status'] == '3') ? 'Dispatch' : (($data['status'] == '4') ? 'Delivered' : ''))))),
                date('d-M-Y', strtotime($data['created_at'])),
                $data['dispatch_number']??'',
                date('d-M-Y', strtotime($data['updated_at'])),
                $data['remark'],
            ];
        }elseif($this->redeem_mode == '2'){
            return [
                $data['id'],
                $data['created_at'] = isset($data['created_at']) ? date("d-M-Y", strtotime($data['created_at'])) : '',
                $data['customer']['id'],
                $data['customer']['name'],
                $data['customer']['first_name'].' '.$data['customer']['last_name'],
                // implode(',', $employee),
                implode(',', $parent_code),
                implode(',', $parent),
                "'".$data['customer']['mobile'],
                $data['city_name'] = isset($data['customer']['customeraddress']['cityname']['city_name']) ? $data['customer']['customeraddress']['cityname']['city_name'] : '',
                $data['district_name'] = isset($data['customer']['customeraddress']['districtname']['district_name']) ? $data['customer']['customeraddress']['districtname']['district_name'] : '',
                $data['state_name'] = isset($data['customer']['customeraddress']['statename']['state_name']) ? $data['customer']['customeraddress']['statename']['state_name'] : '',
                implode(',', $branch_arr),
                implode(',', $division_arr),
                'NEFT',
                $data['redeem_amount'],
                (($data['status'] == '0') ? 'Pendding' : (($data['status'] == '1') ? 'Approved' : (($data['status'] == '2') ? 'Rejected' : (($data['status'] == '3') ? 'Success' : (($data['status'] == '4') ? 'Fail' : ''))))),
                isset($data['customer']['customerdetails']['bank_name']) ? $data['customer']['customerdetails']['bank_name'] : '',
                isset($data['customer']['customerdetails']['account_holder']) ? $data['customer']['customerdetails']['account_holder'] : '',
                isset($data['customer']['customerdetails']['account_number']) ? "'".$data['customer']['customerdetails']['account_number'] : '',
                isset($data['customer']['customerdetails']['ifsc_code']) ? $data['customer']['customerdetails']['ifsc_code'] : '',
                isset($data['customer']['customerdetails']['aadhar_no']) ? "'".$data['customer']['customerdetails']['aadhar_no'] : '',
                isset($data['customer']['customerdetails']['pan_no']) ? $data['customer']['customerdetails']['pan_no'] : '',
                isset($data['neft_details'])?$data['neft_details']['tds'].'%':'10%',
                (isset($data['neft_details']) && $data['status'] == '3') ? $data['redeem_amount'] * $data['neft_details']['tds'] / 100 : ($data['redeem_amount'] * 10) / 100,
                (isset($data['neft_details']) && $data['status'] == '3') ? $data['redeem_amount'] - (($data['redeem_amount']*$data['neft_details']['tds']) / 100) :  $data['redeem_amount'] - (($data['redeem_amount'] * 10) / 100),
                isset($data['neft_details']) ? date('d-M-Y', strtotime($data['updated_at'])) : '',
                isset($data['neft_details']) ? $data['neft_details']['utr_number'] : '',
            ];
        }
    }
}
