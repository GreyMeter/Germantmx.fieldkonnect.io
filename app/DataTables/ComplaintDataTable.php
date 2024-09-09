<?php

namespace App\DataTables;

use App\Models\Complaint;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class ComplaintDataTable extends DataTable
{

    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('status', function ($query) {
                if($query->complaint_status == '0'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-secondary">Open</span></a>';
                }elseif($query->complaint_status == '1'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-warning">Pending</span></a>';
                }elseif($query->complaint_status == '2'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-info">Work Done</span></a>';
                }elseif($query->complaint_status == '3'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-success">Completed</span></a>';
                }elseif($query->complaint_status == '4'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-primary">Closed</span></a>';
                }elseif($query->complaint_status == '5'){
                    return '<a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint"><span class="badge badge-danger">Canceled</span></a>';
                }
            })
            // ->addColumn('action', function ($query) {
            //     $btn = '';
            //     $activebtn = '';
            //     if (auth()->user()->can(['complaint_edit'])) {
            //         $btn = $btn . '<a href="'.route('complaints.edit', $query->id).'" class="btn btn-success btn-just-icon btn-sm" title="' . trans('panel.global.edit') . ' Complaint">
            //               <i class="material-icons">edit</i>
            //               </a>';
            //     }
            //     if (auth()->user()->can(['complaint_view'])) {
            //         $btn = $btn . ' <a href="'.route('complaints.show', $query->id).'" class="btn btn-info btn-just-icon btn-sm" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint">
            //                     <i class="material-icons">visibility</i>
            //                     </a>';
            //     }
            //     // $btn = '';
            //     return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
            //                     ' . $btn . '
            //                 </div>';
            // })
            ->addColumn('complaint_number', function ($query) {
                if (auth()->user()->can(['complaint_view'])) {
                    $btn = ' <a href="'.route('complaints.show', $query->id).'" value="' . $query->id . '" title="' . trans('panel.global.show') . ' Complaint">
                                '.$query->complaint_number.'
                                </a>';
                }else{
                    $btn = $query->complaint_number;
                }
                // $btn = '';
                return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
            })
            ->rawColumns(['action', 'status','complaint_number']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\City $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Complaint $model)
    {
        return $model->with('party', 'service_center_details', 'customer', 'complaint_type_details')->latest()->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('city-table')
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
