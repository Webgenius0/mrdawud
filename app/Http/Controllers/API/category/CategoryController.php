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

    public function categoryShow()
    {
        try {
            $category = Category::all();
            return $this->success([
                'category'=>$category,
                'message'=>'Category has been fetched successfully'
            ], 200);
        } catch (Exception $e) {
            
            return $this->error('Something went wrong', 500);
        }      
    }
}
