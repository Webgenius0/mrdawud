@extends('backend.app')

@section('title', 'Profile')

@push('style')

    <style>
        {{-- CKEditor CDN --}}

        .ck-editor__editable_inline {
            min-height: 300px;
        }

    </style>
@endpush

@section('content')
    <main class="app-content content">
        <h2 class="section-title">Create Dynamic Page</h2>

        <nav aria-label="breadcrumb tm-breadcumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tm-breadcumb-item">
                    <a href="{{ route('dynamicPages.index') }}">Dynamic Pages</a>
                </li>
                <li class="breadcrumb-item tm-breadcumb-item active" aria-current="page">
                    Create New Dynamic Page
                </li>
            </ol>
        </nav>

        <div class="addbooking-form-area">

            <form action="{{ route('dynamicPages.store') }}" method="POST" class="tm-form">
                @csrf
                <div class="form-field-wrapper">
                    {{-- page title input field --}}
                    <div class="form-group">
                        <label for="page_title">Page Title</label>
                        <input type="text" name="page_title"
                            class="form-control @error('page_title') is-invalid @enderror" required
                            placeholder="Enter first name here" value="{{ old('page_title') }}">
                        @error('page_title')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
                <div class="form-field-wrapper">
                    {{-- page_content input field --}}
                    <div class="form-group">
                        <label for="page_content">Page Content</label>
                        <textarea name="page_content" class="ck-editor form-control @error('page_content') is-invalid @enderror">{{ old('page_content') }}</textarea>
                        @error('page_content')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="button-group" style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <a href="{{ route('dynamicPages.index') }}" class="btn btn-danger">Cancel</a>
                </div>

            </form>

        </div>

    </main>
@endsection

@push('script')
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
        .create(document.querySelector('.ck-editor'), {
            removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle',
                'ImageToolbar', 'ImageUpload', 'MediaEmbed'
            ],
            height: '500px'
        })
        .catch(error => {
            console.error(error);
        });
        $(".single-select").select2({
            theme: "classic"
        });
        $(document).ajaxStart(function() {
            NProgress.start();
        });

        $(document).ajaxComplete(function() {
            NProgress.done();
        });
    </script>
@endpush
