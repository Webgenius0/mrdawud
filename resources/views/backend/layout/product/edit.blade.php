@extends('backend.app')
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css" />
@endpush
@section('title', 'Category Edit')
@section('content')
    <div class="app-content content ">
        <!-- General setting Form section start -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Edit</h3>
                <div>
                    <a href="{{ route('admin.product.index') }}" class="btn btn-primary" type="button">
                        <span>Product List</span>
                    </a>
                </div>

            </div>
            <div class="card-body">
                <form class="form" method="POST" action="{{ route('admin.product.update', $product->id) }}" 
                    enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{{ $product->id }}">
                    @csrf
                    @method('PUT')
                     <div class="row"> 
                        <div class="col-md-6 col-12 w-100">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" id="title" class="form-control"
                                    value="{{ old('title', $product->title) }}" placeholder="Product Title" name="title" />
                                @error('title')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                                <div class="col-md-6 col-12">
                                    <div class="form-group">
                                        <label for="category_id">Category</label>
                                        <select id="category_id" class="form-control" name="category_id">
                                          
                                            <!-- Loop through categories and display titles -->
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id , old('category_id') == $category->id ? 'selected' : ''}}" >
                                                    {{ $category->title, old('title') == $category->title ? 'selected' : ''}}
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
                                    value="{{ old('price', $product->price) }}" placeholder="Product Price"
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
                                    value="{{ old('stock' , $product->stock) }}" placeholder="Produc Stock"
                                    name="stock" />
                                @error('location')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="location">Taxes</label>
                                <input type="text" id="taxes" class="form-control"
                                    value="{{ old('taxes' , $product->taxes) }}" placeholder="Produc taxes"
                                    name="taxes" />
                                @error('taxes')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4 col-12 w-100">
                            <div class="form-group">
                                <label for="image">Product Image</label>
                                <input class="form-control dropify" accept="image/*" data-default-file="{{ asset($product->image) }}" type="file" name="image">
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
