@extends('backend.app')
@push('style')
<link rel="stylesheet" href="{{ asset('backend/assets/datatable/css/datatables.min.css') }}">
@endpush
@section('title', 'Users')
@section('content')
<div class="app-content content ">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Order List</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive mt-4 p-4 card-datatable table-responsive pt-0">
                <table class="table table-hover" id="data-table">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-checkbox">
                                    <input type="checkbox" class="form-check-input" id="select_all" onclick="select_all()">
                                    <label class="form-check-label" for="select_all"></label>
                                </div>
                            </th>
                            <th>Order Id</th>
                            <th>Customer Name</th>

                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
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
                                        </div>`
                },

                scroller: {
                    loadingIndicator: false
                },
                pagingType: "full_numbers",
                dom: "<'row justify-content-between table-topbar'<'col-md-2 col-sm-4 px-0'l><'col-md-2 col-sm-4 px-0'f>>tipr",
                ajax: {
                    url: "{{ route('order.list') }}",
                    type: "get",
                },

                columns: [{
                        data: 'bulk_check',
                        name: 'bulk_check',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'uuid',
                        name: 'uuid',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'username',
                        name: 'username',
                        orderable: false,
                        searchable: false
                    },

                    {
                data: 'product_name', 
                name: 'product_name', 
                orderable: false, 
                searchable: false
            },
            {
                data: 'quantity', 
                name: 'quantity',
                orderable: false, 
                searchable: false
            },
                    {
                        data: 'total_price',
                        name: 'total_price',
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

    function showDeleteConfirm(id, deleteUrl) {
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
                deleteArticle(id, deleteUrl);
            }
        });
    }

    function deleteArticle(id, deleteUrl) {
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
                        'Your article has been deleted.',
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


   

    // Status Change
   



 // Function to handle the status change and show confirmation modal
function showStatusChangeAlert(orderId, selectedStatus) {
    // Prevent default action
    event.preventDefault();

    // Show SweetAlert2 confirmation modal
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to update the status?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No',
    }).then((result) => {
        // If the user clicks "Yes", then update the status
        if (result.isConfirmed) {
            // Call the function to change the status
            statusChange(orderId, selectedStatus);
        }
    });
}

// Function to handle the actual status change (AJAX request)
function statusChange(orderId, newStatus) {
    // Make sure the newStatus is valid
    if (!newStatus) {
        Swal.fire({
            title: 'Error',
            text: 'Please select a valid status.',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
        return;
    }

    // Make the AJAX request to update the status
    $.ajax({
        url: "{{ route('orders.updateStatus') }}",  // Ensure this route matches the actual one in your routes
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',  // CSRF token for security
            order_id: orderId,            // The ID of the order
            status: newStatus             // The new status selected from the dropdown
        },
        success: function(response) {
            if (response.status) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Order status updated successfully.',
                    icon: 'success',
                    confirmButtonText: 'Ok'
                });
                // Optionally, reload the page after success (you could also update the row or the status dropdown if needed)
                location.reload();  // This will reload the page after success (optional)
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: response.message,
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });
            }
        },
        error: function() {
            Swal.fire({
                title: 'Error',
                text: 'There was an error while updating the status.',
                icon: 'error',
                confirmButtonText: 'Ok'
            });
        }
    });
}



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
                let deleteUrl = '{{ route('order.destroy', ':id') }}'.replace(':id', id);
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


</script>
@endpush
@endsection