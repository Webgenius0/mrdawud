@extends('backend.app')

@section('title', 'FAQ Page')

@push('style')
    <style>
        /* DataTable Custom Styling */
        .dataTables_wrapper {
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .dataTables_length label {
            font-weight: bold;
            margin-right: 10px;
            font-size: 14px;
        }

        .dataTables_length select {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 6px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        .dataTables_filter label {
            font-weight: bold;
        }

        .dataTables_filter input {
            font-size: 14px;
            padding: 5px;
            margin-left: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 200px;
        }

        .dataTables_paginate {
            font-size: 14px;
        }

        /* Custom Pagination */
        .pagination-container a,
        .pagination-container span {
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
            cursor: pointer;
            font-weight: bold;
        }

        .pagination-container a.active {
            color: #343a40;
            background-color: #f1f1f1;
        }

        .pagination-container .ellipsis {
            pointer-events: none;
            cursor: default;
        }

        /* Action Buttons */
        .action-wrapper {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            /* padding: 8px 12px; */
            font-size: 14px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .outline-action-btn {
            background-color: transparent;
            border: 2px solid #ddd;
        }

        .outline-action-btn:hover {
            background-color: #f0f0f0;
        }

        .edit-btn {
            color: #fff;
            background-color: #28a745;
        }

        .edit-btn:hover {
            background-color: #218838;
        }

        .delete-btn {
            color: #fff;
            background-color: #dc3545;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        /* Status Switch */
        .form-check-input {
            border-radius: 25rem;
        }

        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
    </style>
@endpush

@section('content')
    <main class="app-content content">
        <h2 class="section-title">FAQ Page</h2>

        <div class="card p-3 border rounded shadow-sm bg-white">
            <div class="card-body">
                <div class="table-responsive p-4">
                    <!-- Button Positioned at Top Right -->
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('faq.create') }}" class="btn btn-primary" type="button">
                            <span>Add FAQ Page</span>
                        </a>
                    </div>
                    <table id="basic_tables" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Short Description</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let dTable = $('#basic_tables').DataTable({
                order: [],
                destroy: true,
                lengthMenu: [
                    [25, 50, 100, 200, 500, -1],
                    [25, 50, 100, 200, 500, "All"]
                ],
                processing: true,
                responsive: true,
                serverSide: true,
                paging: true,
                language: {
                    lengthMenu: "Show _MENU_ entries",
                    processing: `<div class="text-center">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>`
                },
                ajax: {
                    url: "{{ route('faq.index') }}",
                    type: "get",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'ttile',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'short_description',
                        name: 'short_description',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return data.length > 30 ? data.substr(0, 30) + '...' : data;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {
                    const totalPages = Math.ceil(settings._iRecordsDisplay / settings._iDisplayLength);
                    const currentPage = settings._iDisplayStart / settings._iDisplayLength + 1;
                    updateCustomPagination(totalPages, currentPage);
                }
            });

            $('#customSearchBox').on('keyup', function() {
                dTable.search(this.value).draw();
            });

            $('#pageLength').on('change', function() {
                dTable.page.len(this.value).draw();
            });

            function updateCustomPagination(totalPages, currentPage) {
                const paginationContainer = $('#customPagination');
                paginationContainer.empty();

                const maxVisiblePages = 5;
                let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
                let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

                if (startPage > 1) {
                    paginationContainer.append('<a href="#" class="pagination-item" data-page="1">1</a>');
                    if (startPage > 2) {
                        paginationContainer.append('<span class="ellipsis">...</span>');
                    }
                }

                for (let i = startPage; i <= endPage; i++) {
                    paginationContainer.append(
                        `<a href="#" class="pagination-item ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</a>`
                    );
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        paginationContainer.append('<span class="ellipsis">...</span>');
                    }
                    paginationContainer.append(
                        `<a href="#" class="pagination-item" data-page="${totalPages}">${totalPages}</a>`
                    );
                }

                $('.pagination-item').on('click', function(e) {
                    e.preventDefault();
                    const page = $(this).data('page');
                    dTable.page(page - 1).draw('page');
                });
            }
        });
    </script>
    <script>
        // Use the status change alert
        function changeStatus(event, id) {
            event.preventDefault();
            let statusUrl = '{{ route('faq.status', ':id') }}'.replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: "You want to change the status of this dynamic page.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, change it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: statusUrl,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Status Updated!',
                                response.success,
                                'success'
                            );
                            $('#basic_tables').DataTable().ajax.reload(); // Reload DataTable
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                response.responseJSON.error || 'An error occurred.',
                                'error'
                            );
                        }
                    });
                }
            });
        }


        // Use the delete confirm alert
        function deleteRecord(event, id) {
            event.preventDefault();
            let deleteUrl = '{{ route('faq.destroy', ':id') }}'.replace(':id', id);

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.success,
                                'success'
                            );
                            $('#basic_tables').DataTable().ajax.reload(); // Reload DataTable
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                response.responseJSON.error || 'An error occurred.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endpush
