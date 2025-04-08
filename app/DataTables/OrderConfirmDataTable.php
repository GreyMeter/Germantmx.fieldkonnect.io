<?php

namespace App\DataTables;

use App\Models\EmployeeDetail;
use App\Models\OrderConfirm;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class OrderConfirmDataTable extends DataTable
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
                if (auth()->user()->can(['order_show'])) {
                    $btn = $btn . '<a href="' . url("orders_confirm/" . encrypt($query->id)) . '" class="btn btn-theme btn-just-icon btn-sm" title="' . trans('panel.global.show') . ' ' . trans('panel.order.title_singular') . '">
                                    <i class="material-icons">visibility</i>
                                </a>';
                }
                if (auth()->user()->can(['final_order_revised_size'])) {
                    $btn = $btn . '<a href="' . url("orders_confirm/" . encrypt($query->id)) . '/edit" class="btn btn-theme btn-just-icon btn-sm ml-2" title="Revised Size">
                                    <i class="material-icons">straighten</i>
                                </a>';
                }
                if (auth()->user()->can(['order_activeee'])) {
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
            ->rawColumns(['action', 'created_at']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Order $model
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function query(OrderConfirm $model, Request $request)
    {
        $userids = getUsersReportingToAuth();

        $query = $model->with('order.customer', 'createdbyname')->selectRaw('*, SUM(qty) as total_qty')
            ->groupBy('confirm_po_no');

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
