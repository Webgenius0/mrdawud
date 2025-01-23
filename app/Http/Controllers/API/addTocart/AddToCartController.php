<?php

namespace App\Http\Controllers\API\addTocart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Traits\apiresponse;
use App\Models\AddToCart;
use Exception;
class AddToCartController extends Controller
{
    use apiresponse;
    public function addToCart(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $user = auth()->user();
    if (!$user) {
        return response()->json([
           'message' => 'User not authenticated or not authorized.',
        ], 401);
    }
   
    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422);
    }
    

    try {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated.',
            ], 401);
        }

        $product = Product::find($request->product_id);

        // Check if the product already exists in the cart
        $cartItem = AddToCart::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // If product already exists in the cart, update the quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // If product is not in the cart, add it
            AddToCart::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart successfully.',
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'An error occurred while adding the product to the cart.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
//show cart
public function cartList()
{
    $user = auth()->user();

    if (!$user) {
        return response()->json([
           'message' => 'User not authenticated.',
        ], 401);
    }

   try {
    $cartItems = AddToCart::where('user_id', $user->id)
    ->with('product:id,title,image,price') // Include only necessary fields from Product
    ->get();

// Map to extract only product details
$products = $cartItems->map(function ($item) {
    return [
        'id' => $item->product->id,
        'product_id' => $item->product->id ?? null,
        'quantity' => $item->quantity,
        'price' => $item->product->price ?? null,
        'title' => $item->product->title ?? null,
        'image' => $item->product->image ?? null,
      
        


    ];
});

return response()->json([
    'status' => 200,
    'message' => 'Cart items fetched successfully.',
    'products' => $products,
], 200);

   } catch (Exception $e) {
    return response()->json([
       'message' => 'An error occurred while fetching the cart items.',
        'error' => $e->getMessage(),
    ], 500);
   }
}       

}
