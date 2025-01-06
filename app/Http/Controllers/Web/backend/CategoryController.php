<?php

namespace App\Http\Controllers\Web\backend;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    $image = $data->image;
                    return '<img class="rounded-circle" src="' . asset($image) . '" width="50px" height="50px">';
                })
                ->addColumn('status', function ($data) {
                    $status = $data->status;
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" ' . ($status == 'active' ? 'checked' : '') . '>
                            </div>';

                })
                ->addColumn('bulk_check', function ($data) {
                    return Helper::tableCheckbox($data->id);

                })
                ->addColumn('action', function ($data) {
                    $viewRoute = route('admin.category.edit', ['category' => $data->id]);
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

        return view('backend.layout.category.index');
    }

    /**
     * Store a newly created resource in storage.
     * @return View
     */
    public function create()
    {
        return view('backend.layout.category.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        try {
        $category = new Category();
        $category->title = $request->validated('title');
        $category->slug = Str::slug($request->validated('title'));

        if ($request->hasFile('image')) {
            $url = Helper::fileUpload($request->file('image'), 'category', $request->validated('title') . "-" . time());
            $category->image = $url;
        }

        $category->location = $request->validated('location');
        $category->save();
        return redirect()->route('admin.category.index')->with('t-success', 'Category created successfully');
        } catch (\Exception $exception) {
            return redirect()->route('admin.category.index')->with('t-error', 'Something went wrong');
        }
    }

    /**
     * Edit the specified resource in storage.
     * @return View
     */
    public function edit(Category $category)
    {
        return view('backend.layout.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $category->title = $request->validated('title');
            $category->slug = Str::slug($request->validated('title'));
            if ($request->hasFile('image')) {
                if ($category->image && file_exists(public_path($category->image))) {
                    File::delete(public_path($category->image));
                }
                $url = Helper::fileUpload($request->file('image'), 'category', $request->validated('title') . "-" . time());
                $category->image = $url;
            }
            $category->location = $request->validated('location');
            $category->save();
            return redirect()->route('admin.category.index')->with('t-success', 'Category updated successfully');
        } catch (\Exception $exception) {
            return redirect()->route('admin.category.index')->with('t-error', 'Something went wrong');
        }
    }

    /**
     * Delete the specified resource from storage.
     * @param  Category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        try {
            if ($category->image) {
                File::delete(public_path($category->image));
            }
            $category->delete();
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (\Exception $exception) {
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
    public function bulkDelete(Request $request){
        if ($request->ajax()) {
                $result = Category::whereIn('id',$request->ids)->get();

                if($result){
                    foreach($result as $value){
                        if(!empty($value->image)){
                            if(File::exists(public_path($value->image))){
                                File::delete(public_path($value->image));
                            }
                        }
                    }
                    Category::destroy($request->ids);
                    return response()->json([
                        'success' => true,
                        'message' => 'Categories deleted successfully',
                    ]);
                }else{
                    return response()->json([
                        'success' => false,
                        'message' => 'Categories not found',
                    ]);
                }
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong',
                ]);
            }
       
    }

}
