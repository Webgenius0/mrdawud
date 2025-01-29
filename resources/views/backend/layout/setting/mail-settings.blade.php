@extends('backend.app')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />
@endpush
@section('title', 'General Setting')
@section('content')
<div class="app-content content">
    <!-- Mail Setting Form section start -->
    <section id="multiple-column-form">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Mail Setting</h4>
                    </div>
                    <div class="card-body">
                        
                        @if (session()->has('message'))
                            <div class="alert alert-success" id="successAlert">
                                {{ session('message') }}
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger" id="errorAlert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form id="mailSettingForm" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Mail Mailer -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mail_mailer">Mail Mailer</label>
                                        <input type="text" id="mail_mailer" class="form-control"
                                               value="{{ env('MAIL_MAILER') }}" name="mail_mailer" />
                                        @error('mail_mailer')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Mail Host -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mail_host">Mail Host</label>
                                        <input type="text" id="mail_host" class="form-control"
                                               value="{{ env('MAIL_HOST') }}" name="mail_host" />
                                        @error('mail_host')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Mail Port -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mail_port">Mail Port</label>
                                        <input type="text" id="mail_port" class="form-control"
                                               value="{{ env('MAIL_PORT') }}" name="mail_port" />
                                        @error('mail_port')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Mail Username -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mail_username">Mail Username</label>
                                        <input type="text" id="mail_username" class="form-control"
                                               value="{{ env('MAIL_USERNAME') }}" name="mail_username" />
                                        @error('mail_username')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Mail Password -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mail_password">Mail Password</label>
                                        <input type="text" id="mail_password" class="form-control"
                                               value="{{ env('MAIL_PASSWORD') }}" name="mail_password" />
                                        @error('mail_password')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Mail Encryption -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mail_encryption">Mail Encryption</label>
                                        <input type="text" id="mail_encryption" class="form-control"
                                               value="{{ env('MAIL_ENCRYPTION') }}" name="mail_encryption" />
                                        @error('mail_encryption')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Mail From Address -->
                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="mail_from_address">Mail From Address</label>
                                        <input type="text" id="mail_from_address" class="form-control"
                                               value="{{ env('MAIL_FROM_ADDRESS') }}" name="mail_from_address" />
                                        @error('mail_from_address')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


    @push('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>

        <script>
            $('.dropify').dropify();



            $(document).ready(function() {
        const flasher = new Flasher({
            selector: '[data-flasher]',
            duration: 3000,
            options: {
                position: 'top-center',
            },
        });
    });


    $(document).ready(function() {
    $('#mailSettingForm').submit(function(e) {
        e.preventDefault(); // Prevent form from submitting the traditional way

        var formData = new FormData(this); // Get form data

        $.ajax({
            url: "{{ route('mail.update') }}",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                // Check if the response has a success flag
                if (response.success === true) {
                    flasher.success(response.message); // Show success message
                    setTimeout(function() {
                        window.location.href = "{{ route('mail.settings') }}"; // Redirect after 2 seconds
                    }, 2000);
                } else {
                    flasher.error(response.message); // Show error message if success is false
                }
            },
            error: function(xhr, status, error) {
                // If the AJAX request fails (network error, etc.), show a generic error
                flasher.error('An error occurred. Please try again later.', 'Error', {timeOut: 5000});
            }
        });
    });
});
        </script>
    @endpush
@endsection
