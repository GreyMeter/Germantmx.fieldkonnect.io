<?php

namespace App\Exports;
use App\Models\BeatSchedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class AdherenceDetailReportExport implements FromCollection,WithHeadings,ShouldAutoSize,WithMapping
{
    public function __construct($request)
    {
        
        $this->start_date = $request->start_date;
        $this->end_date = $request->end_date;
    }
    public function collection()
    {
        return BeatSchedule::with('users:id,name','beats:id,beat_name','beatcustomers','beatcheckininfo','beatscheduleorders')
                            ->where(function ($query)  {
                                if($this->start_date)
                                {
                                    $query->whereDate('beat_date', '>=', date('Y-m-d',strtotime($this->start_date)));
                                }
                                if($this->end_date)
                                {
                                    $query->whereDate('beat_date', '<=', date('Y-m-d',strtotime($this->end_date)));
                                }
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))

                                {
                                    $query->whereIn('user_id', getUsersReportingToAuth());
                                }
                            })
                        ->select('id','beat_date','user_id','beat_id')
                        ->latest()
                        ->get();    
    }

    public function headings(): array
    {
        return ['User ID', 'User Name', 'Beat Date', 'Beat Name', 'Total Beat Counter', 'Total Visited Counter', 'Beat Adherance %', 'Total Order Counter', 'Beat Productivity %' , 'New Counter Add', 'Order Qty', 'Unique SKU Count', 'Order Value'];
    }

    public function map($data): array
    {
        $beatcounters = !empty($data['beatcustomers']) ? $data['beatcustomers']->count() : 0 ;
        $visitedcounter = !empty($data['beatcheckininfo']) ? $data['beatcheckininfo']->unique('customer_id','checkin_date')->count() : 0 ;
        $totalorder = !empty($data['beatscheduleorders']) ? $data['beatscheduleorders']->count() : 0 ;
        return [
            $data['user_id'],
            isset($data['users']['name']) ? $data['users']['name'] :'',
            isset($data['beat_date']) ? $data['beat_date'] :'',
            isset($data['beats']['beat_name']) ? $data['beats']['beat_name'] :'',
            isset($beatcounters) ? $beatcounters :'',
            isset($visitedcounter) ? $visitedcounter :'',
            ($beatcounters === 0) ? 0 : number_format((float)($visitedcounter * 100) / $beatcounters, 1, '.', '').' %',
            isset($totalorder) ? $totalorder :'',
            ($visitedcounter === 0) ? 0 :  number_format((float)($totalorder * 100) / $visitedcounter, 1, '.', '').' %',
            !empty($data['beatschedulecustomer']) ? $data['beatschedulecustomer']->count() : 0,
            !empty($data['beatscheduleorders']) ? $data['beatscheduleorders']->sum('total_qty') : 0,
            !empty($data['beatscheduleorders']['orderdetails']) ? $data['beatscheduleorders']['orderdetails']->sum('total_qty') : 0,
            !empty($data['beatscheduleorders']) ? $data['beatscheduleorders']->sum('grand_total') : 0,
        ];
    }
}