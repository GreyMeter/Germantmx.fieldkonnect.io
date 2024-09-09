<?php

namespace App\DataTables;

use App\Models\Order;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class OrderDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('total_gst', function ($data) {
                return $data->gst5_amt + $data->gst12_amt + $data->gst18_amt + $data->gst28_amt;
            })
            ->editColumn('statusname.status_name', function ($data) {
                return $data->status_id ? $data->statusname->status_name : 'Pending';
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['order_edit'])) {
                    $btn = $btn . '<a href="' . url("orders/" . encrypt($query->id) . '/edit') . '" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.order.title_singular') . '">
                                    <i class="material-icons">edit</i>
                                </a>';
                }
                if (auth()->user()->can(['order_show'])) {
                    $btn = $btn . '<a href="' . url("orders/" . encrypt($query->id)) . '" class="btn btn-theme btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.order.title_singular') . '">
                                    <i class="material-icons">visibility</i>
                                </a>';
                }
                if (auth()->user()->can(['order_delete'])) {
                    $btn = $btn . ' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.order.title_singular') . '">
                                <i class="material-icons">clear</i>
                              </a>';
                }
                if (auth()->user()->can(['order_active'])) {
                    $active = ($query->active == 'Y') ? 'checked="" value="' . $query->active . '"' : 'value="' . $query->active . '"';
                    $activebtn = '<div class="togglebutton">
                                <label>
                                  <input type="checkbox"' . $active . ' id="' . $query->id . '" class="activeRecord">
                                  <span class="toggle"></span>
                                </label>
                              </div>';
                }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>' . $activebtn;
            })
            ->rawColumns(['action', 'total_gst']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public function query(Order $model)
    // {
    //     $userids = getUsersReportingToAuth() ;

    //     return $model->with('sellers','buyers','statusname', 'createdbyname')->whereHas('buyers', function($query) use($userids){
    //                             if(!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin'))
    //                             {
    //                                 $query->whereIn('executive_id',$userids);
    //                             }
    //                         })->latest()->newQuery();
    // }

    public function query(Order $model)
    {
        $userids = getUsersReportingToAuth();

        $query = $model->with('sellers', 'buyers', 'statusname', 'createdbyname');

        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin') && !Auth::user()->hasRole('Sub_Admin') && !Auth::user()->hasRole('Sub billing')) {
            $query->where(function ($subQuery) use ($userids) {
                $subQuery->whereIn('executive_id', $userids)
                    ->orWhereIn('created_by', $userids);
            });
        }
        $query->whereHas('buyers', function ($query) {
            if (request()->has('customer_type_id') && request()->get('customer_type_id') != '') {
                $query->where('customertype', request()->get('customer_type_id'));
            }
        });


        if (request()->get('pending_status') != '' && request()->get('pending_status') != NULL) {
            if (request()->get('pending_status') == '0') {
                $query->where('status_id', NULL);
            } else {
                $query->where('status_id', request()->get('pending_status'));
            }
        }

        $query->newQuery();


        // dd($query->toSql());

        // Apply filters
        if (request()->has('retailers_id') && request()->get('retailers_id') != '') {
            $query->where('buyer_id', request()->get('retailers_id'));
        }

        if (request()->has('retailers_id') && request()->get('retailers_id') != '') {
            $query->where('buyer_id', request()->get('retailers_id'));
        }

        return $query->latest();
    }


    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('order-table')
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
