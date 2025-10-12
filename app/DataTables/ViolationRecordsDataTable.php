<?php

namespace App\DataTables;

use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class ViolationRecordsDataTable extends DataTable
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
        ->of($query)
        ->addIndexColumn()
        ->addColumn('action', function($row) {
            $buttons = '';

            // Verify Address button
            if ($row->is_address_verify == 0) {
                $buttons .= '<button class="btn btn-sm btn-primary verify-address" data-id="'.$row->id.'">Verify Address</button> ';
            }

            // Send Mail button
            if ($row->is_address_verify == 2 && $row->is_send_mail == 0) {
                $buttons .= '<button class="btn btn-sm btn-success send-mail" data-id="'.$row->id.'">Send Mail</button>';
            }

            return $buttons;
        })
        ->editColumn('is_address_verify', function($row) {
            switch ($row->is_address_verify) {
                case '0': return 'Pending';
                case '2': return 'Invalid';
                default: return 'Valid';
            }
        })
        ->editColumn('is_send_mail', function($row) {
            switch ($row->is_send_mail) {
                case '0': return 'Pending';
                case '2': return 'Sent';
                default: return 'Sending Error';
            }
        })
        ->rawColumns(['action', 'is_address_verify', 'is_send_mail']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = DB::table('violation_records')
            ->join('violation_type', 'violation_type.id', '=', 'violation_records.violation_type')
            ->select([
                'violation_records.id',
                'violation_type.title as violation_type',
                'violation_records.issue_date as issue_date',
                'violation_records.created_date as created_date',
                'violation_records.address1',
                'violation_records.address2',
                'violation_records.state',
                'violation_records.is_address_verify',
                'violation_records.is_send_mail'
            ])
            ->where('violation_records.is_active', '!=', 2);

        // 🔎 Filter by date if provided
        if (request()->filled('min_date') && request()->filled('max_date')) {
            $model->whereBetween('violation_records.issue_date', [
                request('min_date'),
                request('max_date'),
            ]);
        } elseif (request()->filled('min_date')) {
            $model->whereDate('violation_records.issue_date', '>=', request('min_date'));
        } elseif (request()->filled('max_date')) {
            $model->whereDate('violation_records.issue_date', '<=', request('max_date'));
        }

        return $this->applyScopes($model->orderBy('violation_records.issue_date', 'DESC'));
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
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false)
                ->width(60)
                ->addClass('text-center hide-search'),
            ['data' => 'violation_type', 'name' => 'violation_type.title', 'title' => 'Violation Type'],
            ['data' => 'issue_date', 'name' => 'violation_records.issue_date', 'title' => 'Issue Date'],
            ['data' => 'address1', 'name' => 'violation_records.address1', 'title' => 'House & Street'],
            ['data' => 'address2', 'name' => 'violation_records.address2', 'title' => 'City'],
            ['data' => 'state', 'name' => 'violation_records.state', 'title' => 'State'],
            ['data' => 'is_address_verify', 'name' => 'violation_records.is_address_verify', 'title' => 'Valid Address'],
            ['data' => 'is_send_mail', 'name' => 'violation_records.is_send_mail', 'title' => 'Send Mail'],
            ['data' => 'created_date', 'name' => 'violation_records.created_date', 'title' => 'Fetched At']
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
