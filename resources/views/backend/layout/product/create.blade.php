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
                <h3 class="card-title">Product Create</h3>
                <div>
                    <a href="{{ route('admin.product.index') }}" class="btn btn-primary" type="button">
                        <span>Product List</span>
                    </a>
                </div>

            </div>
            <div class="card-body">
                <form class="form" method="POST" action="{{ route('admin.product.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" id="title" class="form-control"
                                    value="{{ old('title') }}" placeholder="Product Title" name="title" />
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                               <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="category_id">Category</label>
                                        <select id="category_id" class="form-control" name="category_id">
                                            <option value="" disabled selected>Select Product Category</option>
                                            
                                            <!-- Loop through categories and display titles -->
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->title }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('category_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>




                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="location">Price</label>
                                <input type="text" id="price" class="form-control"
                                    value="{{ old('price') }}" placeholder="Product Price"
                                    name="price" />
                                @error('location')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="location">Stock</label>
                                <input type="text" id="stock" class="form-control"
                                    value="{{ old('stock') }}" placeholder="Produc Stock"
                                    name="stock" />
                                @error('location')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        

                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label for="image">Product Image</label>
                                <input class="form-control dropify" accept="image/*" type="file" name="image">
                                @error('image')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary mr-1">Submit</button>
                            <a href="{{ route('admin.product.index') }}" class="btn btn-outline-danger">Cancel</a>
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
