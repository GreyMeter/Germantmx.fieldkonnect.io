<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Http\Request;
use Validator;
use DateTime;

class LeaveController extends Controller
{

    public function __construct()
    {   
        $this->successStatus = 200;
        $this->created = 201;
        $this->accepted = 202;
        $this->noContent = 204;
        $this->badrequest = 400;
        $this->unauthorized = 401;
        $this->notFound = 404;
        $this->notactive = 406;
        $this->internalError = 500;
    }

    public function addLeaves(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'from_date' => 'required|before_or_equal:to_date',
                'to_date' => 'required|after_or_equal:from_date',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }
    
            $fromDate = new DateTime($request->from_date);
            $toDate = new DateTime($request->to_date);
    
            $dates = [];
            $currentDate = clone $fromDate;
            while ($currentDate <= $toDate) {
                $dates[] = $currentDate->format('Y-m-d');
                $currentDate->modify('+1 day');
            }
    
            foreach ($dates as $date) {
                Attendance::updateOrCreate(['user_id' => $request['user_id'], 'punchin_date' => date('Y-m-d', strtotime($date))], [
                    'user_id' => $request['user_id'],
                    'active' => 'Y',
                    'punchin_date' => date('Y-m-d', strtotime($date)),
                    'punchin_time' => date('G:i', strtotime('10:00:00')),
                    'punchin_summary' => !empty($request['reason']) ? $request['reason'] : '',
                    'working_type' => !empty($request['type']) ? $request['type'] : '',
                    'created_at' => getcurentDateTime(),
                    'updated_at' => getcurentDateTime(),
                ]);
            }
           $leave = Leave::create([
                'user_id' => $request['user_id'],
                'active' => 'Y',
                'from_date' => date('Y-m-d', strtotime($request['from_date'])),
                'to_date' => date('Y-m-d', strtotime($request['to_date'])),
                'reason' => !empty($request['reason']) ? $request['reason'] : '',
                'type' => !empty($request['type']) ? $request['type'] : '',
                'created_by' => auth()->user()->id,
                'created_at' => getcurentDateTime(),
                'updated_at' => getcurentDateTime(),
            ]);
            return response()->json(['status'=>'success', 'message'=>'Leave Added Successfully', 'data'=>$leave], 200);
    
        } catch (\Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }
    }

    public function getLeaves(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error','message' =>  $validator->errors()], $this->badrequest); 
            }
            $data = Leave::with('users', 'createdbyname')->where('user_id', $request['user_id'])->get();
            return response()->json(['status'=>'success', 'message'=>'Data retrieved successfully', 'data'=>$data], 200);
    
        } catch (\Exception $e) {
            return response()->json(['status' => 'error','message' => $e->getMessage() ], $this->internalError);
        }
    }
}
