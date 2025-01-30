<?php

namespace App\Http\Controllers\Web\backend\admin;

use App\Http\Controllers\Controller;
use App\Models\ShowCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Spatie\Permission\Commands\Show;
use App\Helper\Helper;

class ShowCategoryController extends Controller
{


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ShowCategory::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('image', function ($data) {
                    $image = $data->image;
                    return '<img class="rounded-circle" src="' . asset($image) . '" width="50px" height="50px">';
                })
                ->addColumn('status', function ($data) {
                    $status = $data->status;
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" ' . ($status == '1' ? 'checked' : '') . '>
                            </div>';

                })
                ->addColumn('bulk_check', function ($data) {
                    return Helper::tableCheckbox($data->id);
                })
                ->addColumn('action', function ($data) {
                    $viewRoute = route('demo.category.edit', ['category' => $data->id]);
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
        return view('backend.layout.shoWcategory.index');
    }

//create categore
    public function create()
    {
        return view('backend.layout.shoWcategory.create');
    }
//demo category edit
    public function edit(ShowCategory $category)
    {
        return view('backend.layout.shoWcategory.edit', compact('category'));
    }
    //store Demo category
    public function store(Request $request)
{
    try {
        // Validate the incoming request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation
           
        ]);

        // Create a new category instance
        $category = new ShowCategory();
        $category->title = $validatedData['title'];     
        $category->status ='1'; // Save the status (true or false)

        // Handle the image upload using Helper::fileUpload
        if ($request->hasFile('image')) {
            $category->image = Helper::fileUpload($request->file('image'), 'democategory', $validatedData['title'] . '-' . time());
        }

        // Store the location
        $category->short_description = $validatedData['location'];

        // Save the category to the database
        $category->save();

        // Flash success message and redirect
        flash()->success('Category created successfully');
        return redirect()->route('demo.category.list');  // Redirect to the category list page

    } catch (\Exception $exception) {
        // Handle any exceptions
        flash()->error('Error creating category: ' . $exception->getMessage());
        return redirect()->route('demo.category.list');  // Redirect back in case of error
    }
}


//update demo category
public function update(Request $request, ShowCategory $category)
{
    try {
        // Validate the incoming request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Image validation (nullable for updates)
        ]);

        // Update the category's title and short_description (location)
        $category->title = $validatedData['title'];
        $category->short_description = $validatedData['location'];

        // Check if a new image has been uploaded
        if ($request->hasFile('image')) {
            // Check if the current category has an image and delete it from the filesystem if it exists
            if ($category->image && file_exists(public_path($category->image))) {
                // Delete the old image file using unlink()
                unlink(public_path($category->image));
            }

            // Handle the new image upload using Helper::fileUpload
            $category->image = Helper::fileUpload($request->file('image'), 'democategory', $validatedData['title'] . '-' . time());
        }

        // Save the updated category to the database
        $category->save();

        // Flash success message and redirect
        flash()->success('Category updated successfully');
        return redirect()->route('demo.category.list');  // Redirect to the category list page

    } catch (\Exception $exception) {
        // Handle any exceptions that occur during the update
        flash()->error('Error updating category: ' . $exception->getMessage());
        return redirect()->route('demo.category.list');  // Redirect back in case of error
    }
}

    //delete demo category
    public function destroy(ShowCategory $category)
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

    //status change
    public function status(int $id): JsonResponse
    {
        $data = ShowCategory::findOrFail($id);
        if ($data->status == '1') {
            $data->status = '0';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data' => $data,
            ]);
        } else {
            $data->status = '1';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data' => $data,
            ]);
        }
    }

}
