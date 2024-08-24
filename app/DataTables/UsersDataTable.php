<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{

    private $route = 'users.';

    private $blade = 'users.';
    private $can = 'users-';

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($query) {
                $encryptedId = Crypt::encryptString($query->id);
                return view($this->blade . 'action', [
                    'permission_edit' => $this->can . 'update',
                    'permission_delete' => $this->can . 'delete',
                    'data' => $encryptedId,
                    'name' => $query->name,
                    'url_edit' => route($this->route . 'edit', $encryptedId),
                    'url_delete' => route($this->route . 'destroy', $encryptedId),
                ]);
            })

            ->editColumn('status', function ($query) {
                $statusLabel = $query->status == 1 ? __('Active') : __('Non Aktif');
                $badgeClass = $query->status == 1 ? 'text-bg-success' : 'text-bg-danger';

                return '<h6><span class="badge ' . $badgeClass . '">' . $statusLabel . '</span></h6>';
            })
            ->rawColumns(['name', 'status'])

            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->parameters($this->getTableParameters())
            ->buttons($this->getButtons());
    }

    /**
     * Get the parameters for the DataTable.
     */
    protected function getTableParameters(): array
    {
        return [
            'select' => [
                'style' => 'multi',
                'selector' => 'td:first-child',
            ],
            'responsive' => true,
            'processing' => true,
            'serverSide' => true,
            'autoWidth' => false,
            'pagingType' => 'simple',
            'lengthMenu' => [[10, 25, 50, 100, 200, 300, 400, 500, -1], [10, 25, 50, 100, 200, 300, 400, 500, "All"]],
            'initComplete' => $this->initComplete(),
        ];
    }

    /**
     * Get the buttons for the DataTable.
     */
    protected function getButtons(): array
    {
        $buttons = [
            'add',
            'excel',
            'csv',
            'pdf',
            'print',
            'reset',
            'reload',
            ['extend' => 'selectAll', 'text' => 'Select All'],
            ['extend' => 'selectNone', 'text' => 'Deselect All'],
            ['text' => 'Delete', 'extend' => 'selected', 'attr' => ['id' => 'massDelete'], 'action' => $this->bulkDeleteActionCallback()],
            ['text' => 'Activate', 'extend' => 'selected', 'attr' => ['id' => 'massActivate'], 'action' => $this->bulkActivateActionCallback()],
            ['text' => 'Deactivate', 'extend' => 'selected', 'attr' => ['id' => 'massDeactivate'], 'action' => $this->bulkDeactivateActionCallback()],
        ];

        return array_map(function($button) {
            return is_array($button) ? Button::make($button) : Button::make($button);
        }, $buttons);
    }


    public function initComplete()
    {
        return 'function() {
            let data = this.api().data();
            window.selected = [];

            $("#users-table tbody").on("click", "tr", function() {
                $(this).toggleClass("selected");
                let id = $(this).data("id");
                if ($(this).hasClass("selected")) {
                    window.selected.push(id);
                } else {
                    window.selected = window.selected.filter(item => item !== id);
                }
            });
        }';
    }

    public function bulkDeleteActionCallback()
    {
        return 'function(e, dt, node, config) {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
                }
            });

            let selectedIds = dt.rows({ selected: true }).data().map(entry => entry.id).toArray();

            if (selectedIds.length > 0) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won\'t be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Call the delete function via AJAX
                        $.ajax({
                            url: "' . route($this->route . "bulkdelete") . '",
                            type: "DELETE",
                            data: { ids: selectedIds },
                            success: function(response) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success"
                                });
                                dt.rows({ selected: true }).remove().draw();
                            },
                            error: function(xhr) {
                                let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "There was an error deleting the users.";
                                Swal.fire({
                                    title: "Error!",
                                    text: errorMessage,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: "No Selection",
                    text: "No users selected for deletion.",
                    icon: "info"
                });
            }
        }';
    }

    public function bulkActivateActionCallback()
    {
        return 'function(e, dt, node, config) {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
                }
            });

            const selectedIds = dt.rows({ selected: true }).data().map(entry => entry.id).toArray();

            if (selectedIds.length > 0) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won\'t be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, update it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Call the delete function via AJAX
                        $.ajax({
                            url: "' . route($this->route . "bulkaktif") . '",
                            type: "POST",
                            data: { ids: selectedIds },
                            success: function(response) {
                                Swal.fire({
                                    title: "Updated!",
                                    text: response.message,
                                    icon: "success"
                                });
                                dt.rows({ selected: true }).remove().draw();
                            },
                            error: function(xhr) {
                                const errorMessage = xhr.responseJSON?.message || "There was an error deleting the users.";
                                Swal.fire({
                                    title: "Error!",
                                    text: errorMessage,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: "No Selection",
                    text: "No users selected for deletion.",
                    icon: "info"
                });
            }
        }';
    }

    public function bulkDeactivateActionCallback()
    {
        return 'function(e, dt, node, config) {
            $.ajaxSetup({
                headers: {
                    "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
                }
            });

            const selectedIds = dt.rows({ selected: true }).data().map(entry => entry.id).toArray();

            if (selectedIds.length > 0) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "You won\'t be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, update it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Call the delete function via AJAX
                        $.ajax({
                            url: "' . route($this->route . "bulknonaktif") . '",
                            type: "POST",
                            data: { ids: selectedIds },
                            success: function(response) {
                                Swal.fire({
                                    title: "Updated!",
                                    text: response.message,
                                    icon: "success"
                                });
                                dt.rows({ selected: true }).remove().draw();
                            },
                            error: function(xhr) {
                                const errorMessage = xhr.responseJSON?.message || "There was an error deleting the users.";
                                Swal.fire({
                                    title: "Error!",
                                    text: errorMessage,
                                    icon: "error"
                                });
                            }
                        });
                    }
                });
            } else {
                Swal.fire({
                    title: "No Selection",
                    text: "No users selected for deletion.",
                    icon: "info"
                });
            }
        }';
    }


    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id'),
            Column::make('name'),
            Column::make('email'),
            Column::make('status'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Users_' . date('YmdHis');
    }
}
