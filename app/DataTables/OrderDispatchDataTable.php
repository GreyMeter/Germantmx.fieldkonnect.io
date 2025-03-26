<?php

namespace App\DataTables;

use App\Models\EmployeeDetail;
use App\Models\OrderDispatch;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class OrderDispatchDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->editColumn('qty', function ($data) {
                return isset($data->total_qty) ? $data->total_qty : '';
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                // if (auth()->user()->can(['order_edit'])) {
                //     $btn = $btn . '<a href="' . url("orders/" . encrypt($query->id) . '/edit') . '" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.order.title_singular') . '">
                //                     <i class="material-icons">edit</i>
                //                 </a>';
                // }
                // if (auth()->user()->can(['order_show'])) {
                    $btn = $btn . '<a href="' . url("orders_dispatch/" . encrypt($query->id)) . '" class="btn btn-theme btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.order.title_singular') . '">
                                    <i class="material-icons">visibility</i>
                                </a>';
                // }
                // if (auth()->user()->can(['order_delete'])) {
                //     $btn = $btn . ' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . ' ' . trans('panel.order.title_singular') . '">
                //                 <i class="material-icons">clear</i>
                //               </a>';
                // }
                // if (auth()->user()->can(['order_active'])) {
                //     $active = ($query->active == 'Y') ? 'checked="" value="' . $query->active . '"' : 'value="' . $query->active . '"';
                //     $activebtn = '<div class="togglebutton">
                //                 <label>
                //                   <input type="checkbox"' . $active . ' id="' . $query->id . '" class="activeRecord">
                //                   <span class="toggle"></span>
                //                 </label>
                //               </div>';
                // }
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
            })
            ->rawColumns(['action', 'created_at']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function query(OrderDispatch $model)
    {
        $userids = getUsersReportingToAuth();

        $query = $model->with('order_confirm','brands', 'sizes', 'grades', 'order.customer', 'createdbyname', 'plant')->selectRaw('*, SUM(qty) as total_qty')->groupBy('dispatch_po_no');

        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $customerIds = EmployeeDetail::where('user_id', Auth::user()->id)->pluck('customer_id');
            $query->whereHas('order', function ($query) use ($customerIds) {
                $query->whereIn('customer_id', $customerIds);
            });
        }

        
        $query->newQuery();

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
