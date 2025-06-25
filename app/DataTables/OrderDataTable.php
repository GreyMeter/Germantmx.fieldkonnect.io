<?php

namespace App\DataTables;

use App\Models\EmployeeDetail;
use App\Models\Order;
use App\Models\OrderConfirm;
use Illuminate\Http\Request;
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
            ->editColumn('status', function ($data) {
                $totalOrderConfirmQty = OrderConfirm::where('order_id', $data->id)->sum('qty');
                if ($data->status == 4) {
                    return '<span class="badge badge-danger">Cancelled</span>';
                } else if ($data->status == 5) {
                    return '<span class="badge badge-warning">Squared Off</span>';
                } else if ($totalOrderConfirmQty > 0 && $data->qty <= $totalOrderConfirmQty) {
                    return '<span class="badge badge-success">Completed</span>';
                } else if ($totalOrderConfirmQty > 0 && $data->qty > $totalOrderConfirmQty) {
                    return '<span class="badge badge-warning">Partially Completed</span>';
                } else if ($data->status == 1) {
                    return '<span class="badge badge-primary">Confirmed</span>';
                }else if ($totalOrderConfirmQty == 0) {
                    return '<span class="badge badge-danger">Pending</span>';
                }
            })
            ->editColumn('created_at', function ($data) {
                return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['soda_editt'])) {
                    $btn = $btn . '<a href="' . url("orders/" . encrypt($query->id) . '/edit') . '" class="btn btn-info btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.order.title_singular') . '">
                                    <i class="material-icons">edit</i>
                                </a>';
                }
                if (auth()->user()->can(['soda_show'])) {
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
            ->rawColumns(['action', 'status', 'created_at']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function query(Order $model, Request $request)
    {
        $userids = getUsersReportingToAuth();

        $query = $model->with('customer', 'createdbyname');

        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $customerIds = EmployeeDetail::where('user_id', Auth::user()->id)->pluck('customer_id');
            $query->whereIn('customer_id', $customerIds);
        }

        if ($request->customer_id && !empty($request->customer_id)) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->start_date && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return $query->orderBy("ordering", "asc")->latest();
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
