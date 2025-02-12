@extends('backend.app')
@push('style')
    <link rel="stylesheet" href="{{ asset('backend/assets/datatable/css/datatables.min.css') }}">
@endpush
@section('title', 'Category List')
@section('content')
    <div class="app-content content ">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Category List</h3>
                <div>
                    <button type='button' style='min-width: 115px;' class='btn btn-danger delete_btn d-none'
                        onclick='multi_delete()'>Bulk Delete</button>
                    <a href="{{ route('admin.category.create') }}" class="btn btn-primary" type="button">
                        <span>Add Category</span>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive mt-4 p-4 card-datatable table-responsive pt-0">
                    <table class="table table-hover" id="data-table">
                        <thead>
                            <tr>
                                <th>
                                    <div class="form-checkbox">
                                        <input type="checkbox" class="form-check-input" id="select_all"
                                            onclick="select_all()">
                                        <label class="form-check-label" for="select_all"></label>
                                    </div>
                                </th>
                                <th>Title</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="{{ asset('backend/assets/datatable/js/datatables.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            let table = document.getElementById("data-table");
            $(document).ready(function() {
                var searchable = [];
                var selectable = [];
                $.ajaxSetup({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    }
                });
                if (!$.fn.DataTable.isDataTable('#data-table')) {
                    let dTable = $('#data-table').DataTable({
                        order: [],
                        lengthMenu: [
                            [25, 50, 100, 200, 500, -1],
                            [25, 50, 100, 200, 500, "All"]
                        ],
                        processing: true,
                        responsive: true,
                        serverSide: true,

                        language: {
                            processing: `<div class="text-center">
                                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>`,
                        },

                        scroller: {
                            loadingIndicator: false
                        },
                        pagingType: "full_numbers",
                        dom: "<'row justify-content-between table-topbar'<'col-md-2 col-sm-4 px-0'l><'col-md-2 col-sm-4 px-0'f>>tipr",
                        ajax: {
                            url: "{{ route('admin.category.index') }}",
                            type: "get",
                        },

                        columns: [{
                                data: 'bulk_check',
                                name: 'bulk_check',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'title',
                                name: 'title',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'image',
                                name: 'image',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'status',
                                name: 'status',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            },
                        ],
                    });

                    new DataTable('#example', {
                        responsive: true
                    });
                }
            });

            function showDeleteAlert(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteArticle(id);
                    }
                });
            }

            function deleteArticle(id) {
                let deleteUrl = '{{ route('admin.category.destroy', ':id') }}'.replace(':id', id);
                fetch(deleteUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Deleted!',
                                'Your item has been deleted.',
                                'success'
                            );

                            var table = $('#data-table').DataTable();

                            table.row('#article-' + id).remove().draw();
                        } else {
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting the article.',
                                'error'
                            );
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error!',
                            'An unexpected error occurred.',
                            'error'
                        );
                    });
            }


            // Status Change Confirm Alert
            function showStatusChangeAlert(id) {
                event.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You want to update the status?',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                }).then((result) => {
                    if (result.isConfirmed) {
                        statusChange(id);
                    }
                });
            }

            // Status Change
            function statusChange(id) {
                let url = '{{ route('admin.category.status', ':id') }}';
                $.ajax({
                    type: "GET",
                    url: url.replace(':id', id),
                    success: function(resp) {
                        console.log(resp);
                        // Reloade DataTable
                        $('#data-table').DataTable().ajax.reload();
                        if (resp.success === true) {
                            // show toast message
                         
                            toastr.success(resp.message);
                            
                        } else if (resp.errors) {
                            toastr.error(resp.errors[0]);
                        } else {
                            toastr.error(resp.message);
                        }
                    },
                    error: function(error) {
                        // location.reload();
                    }
                })
            }




            function multi_delete() {
                let ids = [];
                let rows;
                // Use the DataTable instance
                let dTable = $('#data-table').DataTable();

                $('.select_data:checked').each(function() {
                    ids.push($(this).val());
                    rows = dTable.rows($(this).parents('tr')); // Use DataTable rows() method
                });

                if (ids.length == 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Error',
                        text: 'Please check at least one row of the table!',
                    });
                } else {
                    let url = "{{ route('admin.category.bulk-delete') }}";
                    bulk_delete(ids, url, rows, dTable);
                }
            }
        </script>
    @endpush
@endsection
