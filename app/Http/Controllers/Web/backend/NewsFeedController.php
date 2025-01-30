<?php

namespace App\Http\Controllers\Web\backend;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\NewsFeed;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;


class NewsFeedController extends Controller
{
    public function index(Request $request)
    {
        
      
        
        if ($request->ajax()) {
            $data = NewsFeed::latest();
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
                    $viewRoute = route('newsfeed.edit', ['category' => $data->id]);
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

        return view('backend.layout.newsFeed.index');
    }

    //create news feed
    public function create()
    {
        return view('backend.layout.newsFeed.create');
    }


    //store news feed
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'location' => 'required|string|max:255',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Create a new category (NewsFeed)
            $category = new NewsFeed();
            $category->title = $request->input('title');
            $category->description = $request->input('description');


            $category->status = '1';

            // Handle image file upload if present
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = $request->input('title') . '-' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = Helper::fileUpload($image, 'category', $imageName);
                $category->image = $imagePath;
            }

            $category->location = $request->input('location');
            $category->save();

            flash()->success('News Feed created successfully');

            return redirect()->route('news.feed');
        } catch (\Exception $exception) {
            flash()->error('Error: ' . $exception->getMessage());
            return redirect()->route('news.feed');
        }
    }

    //edit news feed
   
    //delete news feed
    public function destroy(NewsFeed $category)
    {
        Log::info('Category deleted', ['category_id' => $category->id]);
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
        $data = NewsFeed::findOrFail($id);
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

    //news feed edit
    public function edit(NewsFeed $category)
    {
        $newsFeed = NewsFeed::find($category->id);
        return view('backend.layout.newsFeed.edit', compact('newsFeed'));
    }

    //news feed update
   

public function update(Request $request, NewsFeed $newsfeed)
{
    try {
        // Validate the incoming request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|in:1,0',
        ]);

        // Update category attributes
        $newsfeed->title = $validatedData['title'];
        $newsfeed->description = $validatedData['description'];
        $newsfeed->location = $validatedData['location'];
        $newsfeed->status = $validatedData['status'] ?? 1; // Default to 'inactive' if no status is provided

       
        if ($request->hasFile('image')) {
            
            if ($newsfeed->image && File::exists(public_path($newsfeed->image))) {
                unlink(public_path($newsfeed->image));  // Use unlink here
            }

            // Use the helper method to handle file upload
            $image = $request->file('image');
            $imageName = $newsfeed->title . '-' . time() . '.' . $image->getClientOriginalExtension();
            $imagePath = Helper::fileUpload($image, 'newsfeed', $imageName);  // Use your custom helper method
            $newsfeed->image = $imagePath;  // Update the image path
        }

        // Save the category
        $newsfeed->save();

        // Log the update action
        Log::info('Category updated', ['newsfeed_id' => $newsfeed->id, 'title' => $newsfeed->title]);

        // Return a success response
        flash()->success('News Feed updated successfully');
        return redirect()->route('news.feed');
    } catch (\Exception $exception) {
        // Log the error
        flash()->error($exception->getMessage());
        return redirect()->route('news.feed');

      
        
    }
}





}
