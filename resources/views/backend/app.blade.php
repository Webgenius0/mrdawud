<!DOCTYPE html>
<html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Admin panel for managing products.">
    <meta name="author" content="PIXINVENT">
    <title>@yield('title')</title>

    <!-- ✅ Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
    /* ✅ Custom Toastr Colors */
    .toast-success {
        background-color: #28a745 !important; /* Green */
        color: white !important;
    }

    .toast-warning {
        background-color: #ffc107 !important; /* Yellow */
        color: black !important;
    }

    .toast-error {
        background-color: #dc3545 !important; /* Red */
        color: white !important;
    }

    .toast-info {
        background-color: #17a2b8 !important; /* Blue */
        color: white !important;
    }
</style>

    @include('backend.partials.style')
</head>

<body class="vertical-layout vertical-menu-modern navbar-floating footer-static" data-open="click"
    data-menu="vertical-menu-modern" data-col="">

    <!-- BEGIN: Header-->
    @include('backend.partials.header')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @include('backend.partials.sidebar')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="content-wrapper">
        @yield('content')
    </div>
    <!-- END: Content-->

    <!-- BEGIN: Footer-->
    @include('backend.partials.footer')
    <!--END: Footer-->

    @include('backend.partials.script')

    
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "timeOut": "5000",
    };

    @if(session()->has('t-success'))
        toastr.success("{{ session('t-success') }}");
    @endif

    @if(session()->has('t-warning'))
        toastr.warning("{{ session('t-warning') }}");
    @endif

    @if(session()->has('t-error'))
        toastr.error("{{ session('t-error') }}");
    @endif

    @if(session()->has('t-info'))
        toastr.info("{{ session('t-info') }}");
    @endif
</script>


</body>
</html>
