<?php

namespace App\Http\Controllers\Web\backend;

use Exception;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\ProductRequest;
use App\Models\Category;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        if($request->ajax())
        {
            $data=Product::with('category')->latest();
            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('image',function($data){
                $image=$data->image;
                return '<img class="rounded-circle" src="'.asset($image).'" width="50px" height="50px">';
            })
           ->addColumn('status', function ($data) {
                    $status = $data->status;
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" ' . ($status == 'active' ? 'checked' : '') . '>
                            </div>';
                })

                ->addColumn('category_title', function ($data){
                    return $data->category ? $data->category->title : 'N/A';
                })

                ->addColumn('bulk_check', function ($data) {
                    return Helper::tableCheckbox($data->id);
                })
                ->addColumn('action', function ($data) {
                    $viewRoute = route('admin.product.edit', ['product' => $data->id]);
                    return '<div>
                         <a class="btn btn-sm btn-primary" href="' . $viewRoute . '">
                             <i class="fa-solid fa-pen"></i>
                         </a>
                         <button type="button" onclick="showDeleteAlert(' . $data->id . ')" class="btn btn-sm btn-danger">
                             <i class="fa-regular fa-trash-can"></i>
                         </button>
                     </div>';
                })
            ->rawColumns(['bulk_check', 'image', 'status', 'action'])
            ->make(true);
        }
        return view('backend.layout.product.index');
    }

    /**
     * Store a newly created resource in storage.
     * @return View
     */

     public function create()
        {
            $categories=Category::all();
            return view('backend.layout.product.create', compact('categories'));
        }


    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function store(ProductRequest $request)
     {
       //dd($request->validated());
        
        try {
            $product = new Product();
            $product->title=$request->validated('title');
            $product->category_id = $request->validated('category_id');
            $product->slug=Str::slug($request->validated('title'));
            $product->price=$request->validated('price');
            $product->stock=$request->validated('stock');
            if($request->hasFile('image'))
            {
                $url = Helper::fileUpload($request->file('image'), 'product', $request->validated('title') . "-" . time());
                $product->image = $url;
            }
           

            $product->save();
            return redirect()->route('admin.product.index')->with('t-success','Product created successfully');
        } catch (Exception $e) {
            return redirect()->route('admin.product.index')->with('t-error','Something went wrong');
        }
     }

    /**
     * Edit the specified resource in storage.
     * @return View
     */

     public function edit(Product $product)
     {
        $categories=Category::all();
        return view('backend.layout.product.edit',compact('product','categories'));
     }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */


     public function update(ProductRequest $request, Product $product)
     {
         try {
            
            if(!$product)
            {
                return redirect()->route('admin.product.index')->with('t-error','Product not found');
            }
            $product=Product::find($product->id);

             $product->title = $request->validated()['title'];  // Get validated data from the request
             $product->slug = Str::slug($request->validated()['title']);
             $product->price = $request->validated()['price'];
             $product->stock = $request->validated()['stock'];
             $product->category_id = $request->validated()['category_id'];
             
             // Handle image file upload if an image is provided
             if ($request->hasFile('image')) {
                 if ($product->image && file_exists(public_path($product->image))) {
                     
                     File::delete(public_path($product->image));
                 }
               
                 $url = Helper::fileUpload($request->file('image'), 'product', $request->validated()['title'] . "-" . time());
                 $product->image = $url;
             }
     
             
             $product->save();  
            
             return redirect()->route('admin.product.index')->with('t-success', 'Product updated successfully');
         } catch (Exception $e) {
             
             dd($e->getMessage());  
             return redirect()->route('admin.product.index')->with('t-error', 'Something went wrong');
         }
     }
     
    /**
     * Delete the specified resource from storage.
     * @param  Category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        try {
            if ($product->image) {
                File::delete(public_path($product->image));
            }
            $product->delete();
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }




    /**
     * multiple user destroy resource
     *
     * @return \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function bulkDelete(Request $request)
    {
        if ($request->ajax()) {
            $result = Product::whereIn('id', $request->ids)->get();

            if ($result) {
                foreach ($result as $value) {
                    if (!empty($value->image)) {
                        if (File::exists(public_path($value->image))) {
                            File::delete(public_path($value->image));
                        }
                    }
                }
                Category::destroy($request->ids);
                return response()->json([
                    'success' => true,
                    'message' => 'Categories deleted successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Categories not found',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }

}
