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
                        <h4 class="card-title">Stripe Setting</h4>
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

                        <form id="mailSettingForm" action="{{ route('stripe.update') }}" method="get" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Mail Mailer -->
                                <div class="col-md-12 col-12">
                                    <div class="form-group">
                                    <label for="STRIPE_KEY" class="text-lg font-medium mb-2 md:w-1/4">STRIPE PUBLIC KEY</label>
                                <input class="form-control @error('STRIPE_KEY') is-invalid @enderror md:w-3/4" type="text" name="STRIPE_KEY" id="STRIPE_KEY" value="{{ old('STRIPE_KEY', env('STRIPE_KEY')) }}" placeholder="Stripe Public Key">
                                @error('STRIPE_KEY')
                                <span class="text-red-500 block mt-1 text-sm">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                    </div>
                                </div>

                                <!-- Mail Host -->
                                <div class="col-md-12 col-12">
                                    <div class="form-group">
                                    <label for="STRIPE_SECRET" class="text-lg font-medium mb-2 md:w-1/4">STRIPE SECRET KEY</label>
                                <input class="form-control @error('STRIPE_SECRET') is-invalid @enderror md:w-3/4" id="STRIPE_SECRET"
                                    name="STRIPE_SECRET" placeholder="Enter your Stripe Secret" type="text"
                                    value="{{ env('STRIPE_SECRET') }}">
                                @error('STRIPE_SECRET')
                                <span class="text-red-500 block mt-1 text-sm">
                                    <strong>{{ $message }}</strong>
                                </span>
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
            $(document).ready(function() {
        const flasher = new Flasher({
            selector: '[data-flasher]',
            duration: 3000,
            options: {
                position: 'top-center',
            },
        });
    });


    
        </script>
    @endpush
@endsection
