<?php

namespace App\Http\Controllers\API\category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShowCategory;
use Spatie\Permission\Commands\Show;

class DemoCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showDemoCategory()
{
    // Assume you are fetching the category from the database
    $category = ShowCategory::all(); // Fetch the category (example using id 1)

    if (!$category) {
        return response()->json([
            'status' => 404,
            'message' => 'Category not found'
        ]);
    }

    // Check status and prepare the status message
    $formattedCategories = $category->map(function ($category) {
        $statusMessage = $category->status === 0 ? 'Upcoming' : 'Available';
        
        return [
            'title' => $category->title,
            'location' => $category->short_description,
            'image' => $category->image,
            'status_message' => $statusMessage,
        ];
    });
    
    return response()->json([
        'status' => 200,
        'message' => 'Categories fetched successfully',
        'categories' => $formattedCategories,
    ]);
}

}
