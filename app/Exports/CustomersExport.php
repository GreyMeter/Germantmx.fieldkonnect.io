<?php

namespace App\Exports;

use App\Models\Customers;
use App\Models\UserActivity;
use App\Models\Branch;
use App\Models\User;
use App\Models\Division;
use App\Models\Designation;
use App\Models\EmployeeDetail;
use App\Models\ParentDetail;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomersExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {    
        $this->startdate = $request->input('start_date');
        $this->enddate = $request->input('end_date');
        $this->customertype = $request->input('customertype');
        $this->branch_id = $request->input('branch_id');   
        $this->state_id = $request->input('state_id');
        $this->city_id = $request->input('city_id'); 
        $this->active = $request->input('active'); 

        //$this->userid = !empty($request->input('executive_id')) ? $request->input('executive_id') : Auth::user()->id;
        //$this->userids = getUsersReportingToAuth($this->userid); 

        // $this->userid = Auth::user()->id;
        // $this->userids = getUsersReportingToAuth($this->userid); 
        $this->userids = getUsersReportingToAuth(); 
        $this->user_new_id = $request->input('executive_id');

      
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


            return Customers::with(['customertypes','firmtypes','createdbyname','getemployeedetail','getparentdetail'])->where(function ($query)  {
                                if(!empty($this->user_new_id)){
                                    $query->where('executive_id', $this->user_new_id);
                                 }
                                 
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id',$userids);
                                }

                                if($this->active)
                                {
                                    $query->where('active',$this->active);
                                }
                                if($this->startdate)
                                {
                                    $query->whereDate('created_at','>=',$this->startdate);
                                }
                                if($this->enddate)
                                {
                                    $query->whereDate('created_at','<=',$this->enddate);
                                }
                                if(!empty($this->customertype))
                                {
                                    $query->where('customertype', $this->customertype);
                                }
                                if(!empty($this->branch_id))
                                {
                                   $branch_user_id = User::whereIn('branch_id',$this->branch_id)->pluck('id');
                                if(!empty($branch_user_id)){
                                    $query->whereIn('executive_id', $branch_user_id);  
                                   }
                                }
                                if(!empty($this->state_id))
                                {  $state = $this->state_id;
                                 $query->whereHas('customeraddress',function($q) use($state){
                                    $q->where('state_id', $state);
                                 });
                                }
                                if(!empty($this->city_id))
                                {  $city = $this->city_id;
                                 $query->whereHas('customeraddress',function($q) use($city){
                                    $q->where('city_id', $city);
                                 });
                                }

                            })
                        ->limit(5000)->latest()->get();   



    }

    public function headings(): array
    {
        // return ['Created Date','Customer ID','Customer Type','Created by','Firm Name', 'First Name', 'Last Name', 'Mobile', 'Email','Address', 'Gmap address','Pin Code','Zip Code','Market Place','City','District','State','Beat Name', 'Latitude', 'Longitude','GST No','Adhar No','Pan No','Other No','Shop Image', 'Employee Name', 'Grade', 'Visit Status','Contact number -2','Customer Code','Employee Code','Branch Name','Department','Designation','Parent Customer'];

    return ['Created Date','customer_id','customer_code','status','Customer Type','Created by','firm_name','Parent Customer','first_name', 'last_name', 'Mobile','contact_number2', 'email','address', 'Gmap address','Pin Code','Zip Code','market_place','City','District','State','grade','visit_status','gstin_no','aadhar_no','pan_no','other_no','Shop Image','Employee Code','Employee Name','Designation','Branch Name','Division','Latitude', 'Longitude','employee_id','parent_id','pincode_id','city_id','district_id','state_id','customer_type_id','Working Status','Creation Date'];

    }

    public function map($data): array
    {
        // $data['gmap_address'] = UserActivity::where('customerid','=',$data['id'])->where('type','=','Counter Created')->pluck('address')->first();
        $data['gmap_address'] = UserActivity::where('customerid','=',$data['id'])->pluck('address')->first();


        //new fields start

         $employee = array();
         $employee_id = array();
        
        if(!empty($data['getemployeedetail']))  
        {
            foreach($data['getemployeedetail'] as $key_new => $datas) {  

              $employee[] = isset($datas->employee_detail->name) ? $datas->employee_detail->name: '';
              $employee_id[] = isset($datas->user_id) ? $datas->user_id: '';
               
            }
            
        }

        $parent = array();
        $parent_id = array();
         if(!empty($data['getparentdetail']))
        {
            foreach($data['getparentdetail'] as $key => $parent_data) {
                $parent[] = isset($parent_data->parent_detail->name) ? $parent_data->parent_detail->name: '';
                $parent_id[] = isset($parent_data->parent_id) ? $parent_data->parent_id: '';
            }
            
        }


        $getdesignation_arr = array();
        $branch_arr = array();
        $division_arr = array();
        $empcode_arr = array();
        if(!empty($data['getemployeedetail']))  
        {
            foreach($data['getemployeedetail'] as $key_new => $datas) {  
              $getdesignation_arr[] = isset($datas->employee_detail->getdesignation->designation_name) ? $datas->employee_detail->getdesignation->designation_name: '';
              $branch_arr[] = isset($datas->employee_detail->getbranch->branch_name) ? $datas->employee_detail->getbranch->branch_name: '';
              $division_arr[] = isset($datas->employee_detail->getdivision->division_name) ? $datas->employee_detail->getdivision->division_name: '';
              $empcode_arr[] = isset($datas->employee_detail->employee_codes) ? $datas->employee_detail->employee_codes: '';
            }
            
        }
    
        //new fields end


        return [
            $data['created_at'] = isset($data['created_at']) ? date("d-m-Y", strtotime($data['created_at'])) :'',
            $data['id'],
            $data['customer_code'], 
            $data['active'], 
            isset($data['customertypes']['customertype_name']) ? $data['customertypes']['customertype_name'] :'',
            $data['createdbyname'] = isset($data['createdbyname']['name']) ? $data['createdbyname']['name'] : 'Self',
            $data['name'],
            //$data->parentdetail->first_name??'',
            implode(',',$parent),
            $data['first_name'],
            $data['last_name'],
            $data['mobile'],
            $data['contact_number'],  
            $data['email'],
            isset($data['customeraddress']['address1']) ? $data['customeraddress']['address1'] : '',
            $data['gmap_address'] = isset($data['gmap_address']) ? $data['gmap_address'] :'',
            $data['pincode_id'] = isset($data['customeraddress']['pincodename']['pincode']) ? $data['customeraddress']['pincodename']['pincode'] : '',
            isset($data['customeraddress']['zipcode']) ? $data['customeraddress']['zipcode'] : '',
            $data['landmark'] = isset($data['customeraddress']['landmark']) ? $data['customeraddress']['landmark'] : '',
            $data['city_name'] = isset($data['customeraddress']['cityname']['city_name']) ? $data['customeraddress']['cityname']['city_name'] : '',
            $data['district_name'] = isset($data['customeraddress']['districtname']['district_name']) ? $data['customeraddress']['districtname']['district_name'] :'',
            $data['state_name'] = isset($data['customeraddress']['statename']['state_name']) ? $data['customeraddress']['statename']['state_name'] :'',
            // $data['beat_name'] = isset($data['beatdetails']['beats']['beat_name']) ? $data['beatdetails']['beats']['beat_name'] :'',
            isset($data['customerdetails']['grade']) ? $data['customerdetails']['grade'] :'',
            isset($data['customerdetails']['visit_status']) ? $data['customerdetails']['visit_status'] : '',
            isset($data['customerdetails']['gstin_no']) ? $data['customerdetails']['gstin_no'] :'',
            isset($data['customerdetails']['aadhar_no']) ? $data['customerdetails']['aadhar_no'] : '',
            isset($data['customerdetails']['pan_no']) ? $data['customerdetails']['pan_no'] :'',
            isset($data['customerdetails']['otherid_no']) ? $data['customerdetails']['otherid_no'] :'' ,
            $data['shop_image'] = isset($data['profile_image']) ? $data['profile_image'] :'',
            // $data['status_name'] = isset($data['statusname']['status_name']) ? $data['statusname']['status_name'] : '',
          
            //isset($data['userdetails']['employee_codes']) ? $data['userdetails']['employee_codes'] : '',
            implode(',', $empcode_arr),
            // $data['employee_name'] = isset($data['employeename']['name']) ? $data['employeename']['name'] : '',
            implode(',',$employee),
            implode(',',$getdesignation_arr),
            implode(',',$branch_arr),
            implode(',',$division_arr),
            // isset($data['userdetails']['getdesignation']['designation_name']) ? $data['userdetails']['getdesignation']['designation_name'] : '',
            // isset($data['userdetails']['getbranch']['branch_name']) ? $data['userdetails']['getbranch']['branch_name'] : '',
            // isset($data['userdetails']['getdepartment']['division_name']) ? $data['userdetails']['getdepartment']['division_name'] : '',
             $data['latitude'],
             $data['longitude'],
             // $data['executive_id']??NULL,
             // $data['parent_id']??NULL,
             implode(',',$employee_id),
             implode(',',$parent_id),
             isset($data['customeraddress']['pincode_id']) ? $data['customeraddress']['pincode_id'] : '',
             isset($data['customeraddress']['city_id']) ? $data['customeraddress']['city_id'] : '',
             isset($data['customeraddress']['district_id']) ? $data['customeraddress']['district_id'] : '',
             isset($data['customeraddress']['state_id']) ? $data['customeraddress']['state_id'] : '',
             $data['customertype'],
             $data['working_status'],
             $data['creation_date'],
        ];
    }




}