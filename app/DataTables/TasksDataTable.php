<?php

namespace App\DataTables;

use App\Models\Tasks;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;


class TasksDataTable extends DataTable
{
    
    
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function($data)
            {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->addColumn('action', function ($query) {
                  $btn = '';
                //   if(auth()->user()->can(['tasks_edit']))
                //   {
                    $btn = $btn.'<a href="'.url("tasks/".encrypt($query->id).'/edit') .'" class="btn btn-info btn-just-icon btn-sm" title="'.trans('panel.global.show').' '.trans('panel.orders.title_singular').'">
                                    <i class="material-icons">edit</i>
                                </a>';
                    if($query->completed == 0)
                    {
                      $btn = $btn.' <a href="javascript:void(0)" class="btn btn-sm btn-warning btn-just-icon taskcomplete" value="'.$query->id.'" title="'.trans('panel.global.complete').' '.trans('panel.task.title_singular').'">
                                <i class="material-icons">add_task</i>
                              </a>';
                    }
                    if($query->completed == 1 && $query->is_done == 0)
                    {
                      $btn = $btn.' <a href="javascript:void(0)" class="btn btn-sm btn-success btn-just-icon taskdone" value="'.$query->id.'" title="'.trans('panel.global.done').' '.trans('panel.task.title_singular').'">
                                <i class="material-icons">verified</i>
                              </a>';
                    }
                //   }
                //   if(auth()->user()->can(['tasks_show']))
                //   {
                    $btn = $btn.'<a href="javascript:void(0)" class="btn btn-just-icon btn-sm show" value="'.encrypt($query->id).'" title="'.trans('panel.global.show').' '.trans('panel.task.title_singular').'">
                                    <i class="material-icons">visibility</i>
                                </a>';
                //   }
                //   if(auth()->user()->can(['tasks_delete']))
                //   {
                    $btn = $btn.' <a href="" class="btn btn-danger btn-just-icon btn-sm taskdelete" value="'.$query->id.'" title="'.trans('panel.global.delete').' '.trans('panel.task.title_singular').'">
                                <i class="material-icons">clear</i>
                              </a>';
                //   }
                  return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                '.$btn.'
                            </div>';
            })
            ->editColumn('descriptions', function($data)
            {
                return isset($data->descriptions) ? $data->descriptions : '';
            })
            ->rawColumns(['action','descriptions']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Task $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Tasks $model)
    {
        $userids = getUsersReportingToAuth();
        return $model->with('users','statusname')->whereHas('users', function($query) use($userids){
                                if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
                                {
                                    $query->whereIn('id',$userids);
                                }
                            })->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('tasks-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(1)
                    ->buttons(
                        Button::make('create'),
                        Button::make('export'),
                        Button::make('print'),
                        Button::make('reset'),
                        Button::make('reload')
                    );
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->addClass('text-center'),
            Column::make('id'),
            Column::make('add your columns'),
            Column::make('created_at'),
            Column::make('updated_at'),
        ];
    }
}
