<?php

namespace App\Http\Controllers;

use App\DataTables\LeaveDataTable;
use App\Models\Attendance;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use DateTime;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LeaveDataTable $dataTable)
    {
        abort_if(Gate::denies('leave_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $all_reporting_user_ids = getUsersReportingToAuth();
        $all_user_details = User::with('getbranch')->whereIn('id', $all_reporting_user_ids)->orderBy('branch_id')->get();
        $all_users = array();
        foreach ($all_user_details as $k => $val) {
            $users[$k]['id'] = $val->id;
            $users[$k]['name'] = $val->name;
        }
        return $dataTable->render('leave.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'from_date' => 'required|before_or_equal:to_date',
                'to_date' => 'required|after_or_equal:from_date',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
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
            Leave::create([
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
            return Redirect::to('leaves')->with('message_success', 'Leave Added Successfully');

            return redirect()->back()->with('message_danger', 'Error in add leave')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function show(Leave $leave)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function edit(Leave $leave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leave $leave)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leave  $leave
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $leave = Leave::find($id);
            $fromDate = new DateTime($leave->from_date);
            $toDate = new DateTime($leave->to_date);
            $dates = [];
            $currentDate = clone $fromDate;
            while ($currentDate <= $toDate) {
                $dates[] = $currentDate->format('Y-m-d');
                $currentDate->modify('+1 day');
            }

            foreach ($dates as $date) {
                Attendance::where(['user_id' => $leave->user_id, 'punchin_date' => date('Y-m-d', strtotime($date))])->delete();
            }
            if ($leave->delete()) {
                return response()->json(['status' => 'success', 'message' => 'Leave deleted successfully!']);
            }
            return response()->json(['status' => 'error', 'message' => 'Error in Attendance Delete!']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }

    public function approveLeave(Request $request)
    {
        try {
            if (Leave::where('id', '=', $request['id'])->update([
                'status' => 1,
                'remark_status' => null
            ])) {
                return redirect()->back()->with('message_success', 'Leave Approved Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Leave Approved')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }


    public function rejectLeave(Request $request)
    {
        $remark_status  = $request['remark_status'] ?? null;
        try {
            if (Leave::where('id', '=', $request['leave_id'])->update([
                'status' => 2,
                'remark_status' => $remark_status ?? null,
            ])) {
                return Redirect::to('leaves')->with('message_success', 'Leave Rejected Successfully');
            }
            return redirect()->back()->with('message_danger', 'Error in Leave Rejected')->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage())->withInput();
        }
    }
}
