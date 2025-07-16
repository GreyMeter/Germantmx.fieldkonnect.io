<?php

namespace App\DataTables;

use App\Models\Billet;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

use Illuminate\Support\Facades\Auth;

class BilletDataTable extends DataTable
{

  public function dataTable($query)
  {
    return datatables()
      ->eloquent($query)
      ->addIndexColumn()
      ->editColumn('created_at', function ($data) {
        return isset($data->created_at) ? showdatetimeformat($data->created_at) : '';
      })
      ->addColumn('action', function ($query) {
        $btn = '';
        $activebtn = '';
        if (auth()->user()->can(['billet_edit'])) {
          $btn = $btn . '<a href="' . route("billets.edit", $query->id) . '" class="btn btn-info btn-just-icon btn-sm edit" title="' . trans('panel.global.edit') . ' Billet">
                <i class="material-icons">edit</i>
             </a>';
        }
        if (auth()->user()->can(['billet_delete'])) {
          $btn = $btn . ' <a href="" class="btn btn-danger btn-just-icon btn-sm delete" value="' . $query->id . '" title="' . trans('panel.global.delete') . 'Billet">
                                <i class="material-icons">clear</i>
                              </a>';
        }
        return '<div class="btn-group btn-group-sm" role="group" aria-label="Small button group">
                                ' . $btn . '
                            </div>';
      })
      ->rawColumns(['action', 'created_at']);
  }

  /**
   * Get query source of dataTable.
   *
   * @param \App\Unit $model
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function query(Billet $model)
  {
    return $model->with('createdbyname', 'to_name')->latest()->newQuery();
  }

  /**
   * Optional method if you want to use html builder.
   *
   * @return \Yajra\DataTables\Html\Builder
   */
  public function html()
  {
    return $this->builder()
      ->setTableId('getBillet')
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
