@extends('backend.app')

@section('title', 'Faq page')

@push('style')
    <style>
        /* CKEditor styles */
        .ck-editor__editable_inline {
            min-height: 300px;
        }
    </style>
@endpush

@section('content')
    <main class="app-content content">
        <h2 class="section-title">Create Terms And Conditions</h2>

        <nav aria-label="breadcrumb tm-breadcumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item tm-breadcumb-item">
                    <a href="{{ route('terms.and.conditions') }}">Terms And Conditions Pages</a>
                </li>
                <li class="breadcrumb-item tm-breadcumb-item active" aria-current="page">
                    Create
                </li>
            </ol>
        </nav>

        <div class="addbooking-form-area">

            <form action="{{ route('terms.and.conditions.update') }}" method="POST" class="tm-form">
                @csrf

                <div class="form-field-wrapper">
                    {{-- Short Description Field --}}
                    <div class="form-group">
                        <label for="shord_description">Terms:</label>
                        <textarea name="shord_description" class="ck-editor form-control @error('shord_description') is-invalid @enderror">{{ old('shord_description') ??$termsAndConditions->shord_description }}</textarea>
                        @error('shord_description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-field-wrapper">
                    {{-- Full Terms Field --}}
                    <div class="form-group">
                        <label for="terms"> Conditions:</label>
                        <textarea name="terms" class="ck-editor form-control @error('terms') is-invalid @enderror">{{ old('terms') ??$termsAndConditions->terms }}</textarea>
                        @error('terms')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="button-group" style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Add</button>
                    <a href="{{ route('terms.and.conditions') }}" class="btn btn-danger">Cancel</a>
                </div>

            </form>

        </div>

    </main>
@endsection

@push('script')
    <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('textarea[name="shord_description"]'), {
                removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed'],
                height: '500px'
            })
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('textarea[name="terms"]'), {
                removePlugins: ['CKFinderUploadAdapter', 'CKFinder', 'EasyImage', 'Image', 'ImageCaption', 'ImageStyle', 'ImageToolbar', 'ImageUpload', 'MediaEmbed'],
                height: '500px'
            })
            .catch(error => {
                console.error(error);
            });
    </script>
@endpush
