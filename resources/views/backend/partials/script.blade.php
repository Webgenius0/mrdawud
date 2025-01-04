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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.0/dist/sweetalert2.all.min.js" integrity="sha256-BpyIV7Y3e2pnqy8TQGXxsmOiQ4jXNDTOTBGL2TEJeDY=" crossorigin="anonymous"></script>
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
</script>

@stack('script')
