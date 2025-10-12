<?php

namespace App\DataTables;

use App\Models\ViolationApi;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ViolationAPiDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
        ->eloquent($query)
        ->addIndexColumn()
        ->editColumn('is_active', function($query) {
            switch ($query->is_active) {
                case '0': return 'In-Active';
                case '2': return 'Delete';
                default:  return 'Active';
            }
        })
        ->addColumn('action', 'violation_api.action')
        ->rawColumns(['action', 'is_active']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = ViolationApi::query()
        ->join('violation_type', 'violation_type.id', '=', 'violation_api.violation_type')
        ->select([
            'violation_api.id',
            'violation_type.title as violation_type',
            'violation_api.last_fetched_at',
            'violation_api.last_fetch_count',
            'violation_api.is_active',
        ])
        ->where('violation_api.is_active', '!=', 2);

        return $this->applyScopes($model);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('dataTable')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">')
            
                    ->parameters([
                        "processing" => true,
                        "autoWidth" => false,
                    ]);
    }

    /**
     * Get columns.
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::computed('DT_RowIndex')
                ->title('#')
                ->searchable(false)
                ->orderable(false),
            ['data' => 'violation_type', 'name' => 'violation_type.title', 'title' => 'Violation Type'],
            ['data' => 'last_fetched_at', 'name' => 'violation_api.last_fetched_at', 'title' => 'Last Fetched Time'],
            ['data' => 'last_fetch_count', 'name' => 'violation_api.last_fetch_count', 'title' => 'Last Fetch Record Count'],
            ['data' => 'is_active', 'name' => 'violation_api.is_active', 'title' => 'Status'],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return date('YmdHis');
    }
}
