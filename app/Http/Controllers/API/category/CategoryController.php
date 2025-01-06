<?php

namespace App\Http\Controllers\API\category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helper\Helper;
use App\Models\Category;
use App\Traits\apiresponse;
use Exception;

class CategoryController extends Controller
{
    use apiresponse;

    public function addCategory(Request $request)
    {
    return response($request->all());
        // Validate the incoming data
        $validator = validator::make($request->all(), [
            'title' => 'required|string',
            'slug' => 'required|string',
            'location' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // If validation fails, return error
        if ($validator->fails()) {
            return $this->error('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            dd($request->all());
            $title = $request->title;
            $slug = $request->slug;
            $location = $request->location;
            $image = $request->file('image');

           // $existingCategory = Category::where('title', $title)->first();

            
            $imagePath = Helper::fileUpload($image, 'categories', $title);

            $category = new Category();
            $category->title = $title;
            $category->slug = $slug;
            $category->location = $location;
            $category->image = $imagePath;
            $category->save();

            DB::commit();

            return $this->success([
                'message' => 'Category added successfully!',
                'category' => $category
            ], 200);
        } catch (Exception $e) {
            DB::rollback();
            return $this->error('An error occurred while adding the category.', $e->getMessage());
        }
    }
}
