@extends('backend.app')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />
@endpush
@section('title', 'Category Create')
@section('content')
    <div class="app-content content ">
        <!-- General setting Form section start -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Category NewsFeed</h3>
                <div>
                    <a href="{{ route('news.feed') }}" class="btn btn-primary" type="button">
                        <span>NewsFeed List</span>
                    </a>
                </div>

            </div>
            <div class="card-body">
                <form class="form" method="POST" action="{{ route('newsfeed.update', $newsFeed->id) }}"
                    enctype="multipart/form-data">
                    <input type="text" name="id" hidden value="{{ $newsFeed->id }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" id="title" class="form-control"
                                    value="{{ old('title'),$newsFeed->title }}" placeholder="NewsFeed Title" name="title" required/>
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" id="description" class="form-control"
                                    value="{{ old('description'), $newsFeed->description}}" placeholder="NewsFeed Description"
                                    name="description" required />
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="location">Location</label>
                                <input type="text" id="location" class="form-control"
                                    value="{{ old('location') , $newsFeed->location}}" placeholder="NewsFeed Location"
                                    name="location" required />
                                @error('location')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label for="image">NewsFeed Image</label>
                                <input class="form-control dropify" accept="image/*" type="file" name="image" required data-default-file="{{ asset($newsFeed->image) }}">
                                @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary mr-1">Submit</button>
                            <a href="{{ route('news.feed') }}" class="btn btn-outline-danger">Cancel</a>
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
