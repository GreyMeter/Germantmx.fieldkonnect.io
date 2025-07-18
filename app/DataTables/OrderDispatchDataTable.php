<?php

namespace App\DataTables;

use App\Models\EmployeeDetail;
use App\Models\OrderDispatch;
use Illuminate\Http\Request;
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
            ->editColumn('consignee_details', function ($data) {
                return isset($data->order_confirm) ? $data->order_confirm->consignee_details : '';
            })
            ->editColumn('order.customer.name', function ($data) {
                return $data->order->customer->name .
                    ($data->order->customer->customer_po_no ? ' (' . $data->order->customer->customer_po_no . ')' : '');
            })
            ->editColumn('driver_status', function ($data) {
                return ($data->order_dispatch_details &&
                    $data->order_dispatch_details->driver_name &&
                    $data->order_dispatch_details->driver_contact_number &&
                    $data->order_dispatch_details->vehicle_number)
                    ? 'Yes' : 'No';
            })
            ->addColumn('action', function ($query) {
                $btn = '';
                $activebtn = '';
                if (auth()->user()->can(['reverse_dispatch'])) {
                    $btn = $btn . '<button type="button" class="btn btn-theme btn-just-icon btn-sm reverse_dispatch mr-1" value="' . $query->dispatch_po_no . '" title="Reverse This Dispatch"><i class="material-icons">autorenew</i></button>';
                }
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

    public function query(OrderDispatch $model, Request $request)
    {
        $userids = getUsersReportingToAuth();

        $query = $model->with('order_confirm', 'brands', 'sizes', 'grades', 'order.customer', 'createdbyname', 'plant', 'order_dispatch_details')
            ->selectRaw('
                        *,
                        SUM(qty) as total_qty,
                        CASE
                            WHEN EXISTS (
                                SELECT 1 FROM order_dispactch_details odd
                                WHERE odd.order_dispatch_po_no = order_dispatches.dispatch_po_no
                                AND odd.driver_name IS NOT NULL
                                AND odd.driver_contact_number IS NOT NULL
                                AND odd.vehicle_number IS NOT NULL
                            )
                            THEN 1
                            ELSE 0
                        END AS has_driver_info
                        ')->groupBy('dispatch_po_no');

        if (!Auth::user()->hasRole('superadmin') && !Auth::user()->hasRole('Admin')) {
            $customerIds = EmployeeDetail::where('user_id', Auth::user()->id)->pluck('customer_id');
            $query->whereHas('order', function ($query) use ($customerIds) {
                $query->whereIn('customer_id', $customerIds);
            });
        }


        $query->newQuery();

        if ($request->start_date && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->customer_id && !empty($request->customer_id)) {
            $query->whereHas('order', function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            });
        }

        return $query->orderBy('has_driver_info', 'asc')->latest();
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
