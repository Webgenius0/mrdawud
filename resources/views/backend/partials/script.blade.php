<button class="btn btn-primary btn-icon scroll-top" type="button"><i data-feather="arrow-up"></i></button>
<!-- END: Footer-->


<!-- BEGIN: Vendor JS-->
<script src="{{ asset('backend/app-assets/vendors/js/vendors.min.js') }}"></script>
<!-- BEGIN Vendor JS-->

<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('backend/app-assets/vendors/js/charts/apexcharts.min.js') }}"></script>
{{-- <script src="{{ asset('backend/app-assets/vendors/js/extensions/toastr.min.js') }}"></script> --}}
<!-- END: Page Vendor JS-->

<!-- BEGIN: Theme JS-->
<script src="{{ asset('backend/app-assets/js/core/app-menu.js') }}"></script>
<script src="{{ asset('backend/app-assets/js/core/app.js') }}"></script>
<script src="{{ asset('backend/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('backend/app-assets/js/scripts/forms/form-select2.js') }}"></script>
<!-- END: Theme JS-->

<!-- BEGIN: Page JS-->
<script src="{{ asset('backend/app-assets/js/scripts/pages/dashboard-ecommerce.js') }}"></script>
<!-- END: Page JS-->

<script src="{{ asset('backend/assets/js/alert.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.0/dist/sweetalert2.all.min.js"
    integrity="sha256-BpyIV7Y3e2pnqy8TQGXxsmOiQ4jXNDTOTBGL2TEJeDY=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf/notyf.min.js"></script>
<link href="{{ asset('vendor/flasher/flasher.min.js') }}" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="{{ asset('backend/assets/datatable/js/datatables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
{{-- sweetalert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    })
    // bulk checked
    function select_all() {
        if ($('#select_all:checked').length == 1) {
            $('.select_data').prop('checked', true);
            if ($('.select_data:checked').length >= 1) {
                $('.delete_btn').removeClass('d-none');
            }
        } else {
            $('.select_data').prop('checked', false);
            $('.delete_btn').addClass('d-none');
        }
    }

    // single checkbox
    function select_single_item(id) {
        var total = $('.select_data').length; //count total checkbox
        var total_checked = $('.select_data:checked').length; //count total checked checkbox
        (total == total_checked) ? $('#select_all').prop('checked', true): $('#select_all').prop('checked', false);
        (total_checked > 0) ? $('.delete_btn').removeClass('d-none'): $('.delete_btn').addClass('d-none');
    }


    // multi delete
    function bulk_delete(ids, url, rows,table) {
        Swal.fire({
            title: 'Are you sure to delete all checked data?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Cancel',
            confirmButtonText: 'Confirm',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        ids: ids,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: "JSON",
                }).done(function(response) {
                    if (response.success) {
                        Swal.fire("Deleted", response.message, "success").then(function() {
                            table.rows(rows).remove().draw(false);
                            $('#select_all').prop('checked', false);
                            $('.delete_btn').addClass('d-none');
                            table.DataTable().ajax.reload();
                        });
                    }else{
                        Swal.fire('Oops...', response.message, "error");
                    }
                }).fail(function() {
                    Swal.fire('Oops...', "Somthing went wrong with ajax!", "error");
                });
            }
        });
    }
</script>

@stack('script')
