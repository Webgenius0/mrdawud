@extends('backend.app')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />
@endpush
@section('title', 'General Setting')
@section('content')
    <div class="app-content content ">
        <!-- General setting Form section start -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Admin Panel Setting</h3>
            </div>
            <div class="card-body">
                <form class="form" method="POST" action="{{ route('admin.settingupdate') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="first-name-column">Title</label>
                                <input type="text" id="title" class="form-control"
                                    value="{{ $setting->admin_title ?? '' }}" placeholder="System title" name="title" />
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="last-name-column">System Short Name</label>
                                <input type="text" id="system_short_name" class="form-control"
                                    value="{{ $setting->system_short_name ?? '' }}" placeholder="System short Name"
                                    name="system_short_name" />
                                @error('system_short_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="country">System Logo</label>
                                <input class="form-control dropify" type="file" name="logo"
                                    @isset($setting->admin_logo)
                                                   data-default-file="{{ asset($setting->admin_logo) }}"
                                               @endisset>
                                @error('logo')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="country">Mini Logo</label>
                                <input class="form-control dropify" type="file" name="mini_logo"
                                    @isset($setting->admin_mini_logo)
                                                   data-default-file="{{ asset($setting->admin_mini_logo) }}"
                                               @endisset>
                                @error('mini_logo')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="country">System Favicon</label>
                                <input class="form-control dropify" type="file" name="favicon"
                                    @isset($setting->admin_favicon)
                                                    data-default-file="{{ asset($setting->admin_favicon) }}"
                                                @endisset>
                                @error('favicon')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="country">Copyright text</label>
                                <input type="text" class="form-control" name="copyright" id="copyright"
                                    value="{{ $setting->copyright_text ?? '' }}" placeholder="Copyright Text">
                                @error('copyright')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary mr-1">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>

        <script>
            $('.dropify').dropify();
        </script>
    @endpush
@endsection
