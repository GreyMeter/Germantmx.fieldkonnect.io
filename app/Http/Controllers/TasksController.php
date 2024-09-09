<?php

namespace App\Http\Controllers;

use App\Models\Tasks;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Models\Customers;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Excel;
use DataTables;
use Validator;
use Gate;
use App\DataTables\TasksDataTable;
use App\Imports\TasksImport;
use App\Exports\TasksExport;
use App\Exports\TasksTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class TasksController extends Controller
{
    public function __construct() 
    {     
        $this->middleware('auth');   
        
        $this->tasks = new Tasks();
    }

    public function index(TasksDataTable $dataTable)
    {
        //abort_if(Gate::denies('tasks_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return $dataTable->render('tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //abort_if(Gate::denies('tasks_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();

        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->get();
        $customers = Customers::where('active','=','Y')->where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('executive_id',$userids);
                                }
                            })
        ->select('id', 'name','mobile')
        ->get();
        return view('tasks.create',compact('users','customers'))->with('tasks',$this->tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        //abort_if(Gate::denies('tasks_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $request['active'] = 'Y';
        $request['created_by'] = Auth::user()->id;
        if($task = Tasks::create($request->except(['_token'])))
        {
            // $toemail = User::where('id',$request['user_id'])->pluck('email')->first();
            // Mail::send('emails.tasks.assign', ['task' => $task ], function ($message) use($task, $toemail) {
            //       $message->to($toemail)->subject($task['task_title']);
            // });

            return Redirect::to('tasks')->with('message_success', 'Tasks Store Successfully');
        }
        return redirect()->back()->with('message_danger', 'Error in Tasks Store')->withInput(); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //abort_if(Gate::denies('tasks_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $id = decrypt($id);
        $task = Tasks::find($id);
        $task['user_name'] = isset($task['users']['name']) ? $task['users']['name'] :'';
        return response()->json($task);
        //return view('tasks.show')->with('tasks',$task);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //abort_if(Gate::denies('tasks_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $userids = getUsersReportingToAuth();
        $id = decrypt($id);
        $task = Tasks::find($id);
        $task['user_name'] = isset($task['users']['name']) ? $task['users']['name'] :'';
        $users = User::where(function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->select('id','name')->get();
        return view('tasks.create',compact('users'))->with('tasks',$task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, $id)
    {
        abort_if(Gate::denies('tasks_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if(Tasks::where('id',$id)->update($request->except(['_token','id','image','_method','action','associated'])) )
        {
            if(!empty($request['associated']))
            {
                foreach ($request['associated'] as $key => $user) {
                   TaskUsers::updateOrCreate(
                    ['task_id' => $id, 'user_id' => $user],
                    [   'task_id' => $id,
                        'user_id' => $user,
                    ]);
                }
            }
            return Redirect::to('tasks')->with('message_success', 'Tasks Update Successfully');
        }
         return redirect()->back()->with('message_danger', 'Error in Tasks Update')->withInput(); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tasks  $tasks
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //abort_if(Gate::denies('tasks_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        TaskUsers::where('task_id',$id)->delete();
        if(Tasks::where('id',$id)->delete())
        {
            return response()->json(['status' => 'success','message' => 'Task deleted successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Task Delete!']);
    }

    public function active(Request $request)
    {
        if(Tasks::where('id',$request['id'])->update(['active' => ($request['active'] == 'Y') ? 'N' :'Y']))
        {
            $message = ($request['active'] == 'Y') ? 'Inactive' :'Active';
            return response()->json(['status' => 'success','message' => 'Task '.$message.' Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function completed(Request $request)
    {
        if(Tasks::where('id',$request['id'])->update(['completed' => '1','completed_at' => getcurentDateTime() ]))
        {
            return response()->json(['status' => 'success','message' => 'Task Completed  Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }
    public function done(Request $request)
    {
        if(Tasks::where('id',$request['id'])->update(['is_done' => '1']))
        {
            return response()->json(['status' => 'success','message' => 'Task Done  Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }
    public function reopen(Request $request)
    {
        if(Tasks::where('id',$request['id'])->update(['is_done' => '0','done_by' => null ]))
        {
            return response()->json(['status' => 'success','message' => 'Task Done  Successfully!']);
        }
        return response()->json(['status' => 'error','message' => 'Error in Status Update']);
    }

    public function upload(Request $request) 
    {
      abort_if(Gate::denies('tasks_upload'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        Excel::import(new TasksImport,request()->file('import_file'));
        return back();
    }
    public function download()
    {
      abort_if(Gate::denies('tasks_download'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TasksExport, 'tasks.xlsx');
    }
    public function template()
    {
      abort_if(Gate::denies('tasks_template'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (ob_get_contents()) ob_end_clean();
        ob_start();
        return Excel::download(new TasksTemplate, 'tasks.xlsx');
    }

    public function tasksInfo(Request $request)
    {
        if ($request->ajax()) {
            $data = Tasks::with('priorities','statusname')
                            ->orWhere(function($query) use ($request) {
                                $query->where('user_id', $request['user_id'])
                                      ->where(function ($query) use ($request) {
                                        if($request['due_at'] == 'Today')
                                        {
                                            $query->whereDate('datetime', date('Y-m-d'));
                                        }
                                        if($request['due_at'] == 'Week')
                                        {
                                            $query->whereDate('datetime','>' ,date('Y-m-d'));
                                            
                                            $query->whereDate('datetime','<=' ,Carbon::now()->endOfWeek()->format('Y-m-d'));
                                        }
                                        if($request['due_at'] == 'overdue')
                                        {
                                            $query->whereDate('datetime','>', date('Y-m-d'));
                                            $query->where('completed',0);
                                        }
                                        if($request['due_at'] == 'Completed')
                                        {
                                            $query->where('completed',1);
                                        }
                                    });
                            })
                            ->latest();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->editColumn('start_date', function($data)
                    {
                        return showdateformat($data->start_date).' '.$data->start_day.' '.showdateformat($data->start_time);
                    })
                    ->editColumn('datetime', function($data)
                    {
                        return showdateformat($data->datetime).' '.$data->due_day.' '.showdateformat($data->due_time);
                    })
                    ->editColumn('status_id', function($data)
                    {
                        $status = '';
                        if($data['is_done'] == 1)
                        {
                            $status = 'Done';
                        }
                        elseif($data['completed'] == 1)
                        {
                            $status = 'Completed';
                        }
                        else
                        {
                            $status = 'Open';
                        }
                        return $status;
                    })
                    ->make(true);
        }
    }
}
