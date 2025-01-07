<?php

namespace App\Http\Controllers\API\product;

use App\Http\Controllers\Controller;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use App\Models\Product;
use Exception;

class ProductController extends Controller
{
    use apiresponse;
    public function shoProduct()
    {
        try {
            $product= Product::where('status','active')->select('id','title','slug','price','stock','image')->get();
            return $this->success([
                'product'=>$product,
                'message'=>'Product has been fetched successfully'
            ], 200);

        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
        }
    }

}
