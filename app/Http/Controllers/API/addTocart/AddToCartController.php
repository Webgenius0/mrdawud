<?php

namespace App\Http\Controllers\API\addTocart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Traits\apiresponse;
use App\Models\AddToCart;
use App\Models\Category;
use Exception;
class AddToCartController extends Controller
{
    use apiresponse;
    public function addToCart($product_id, Request $request)
    {
        // Check if the product exists
       
        $product = Product::find($product_id);
        $category=Category::where(['id' => $product->category_id])->first();

        if (!$product) {
            return response()->json([
                'message' => 'Product not found.',
            ], 404);
        }

        $userId = auth()->check() ? auth()->id() : null; // Use user_id for logged-in users
        $sessionId = session()->getId(); // Use session_id for guests

        // Check if the product is already in the cart
        $cartItem = AddToCart::where(function ($query) use ($userId, $sessionId) {
            $query->where('session_id', $sessionId);
            if ($userId) {
                $query->orWhere('user_id', $userId);
            }
        })->where('product_id', $product_id)->first();

        if ($cartItem) {
            // Update the quantity if the product exists in the cart
            $cartItem->update([
                'quantity' => $cartItem->quantity + 1,
                //'price'=>$cartItem->price+$cartItem->product->price,
            ]);
        } else {
            // Add the product to the cart
            AddToCart::create([
                'user_id' => $userId,
                'product_id' => $product_id,
                'quantity' => 1, // Default quantity
                'size' => $request->size ?? null, // Optional
                'price' => $product->price, // Assume price is a column in your products table
                //'product_category' => $category->title ?? null, // Optional
                'product_category' => $category->title ?? null, // Optional
                'product_name' => $product->title ?? null, // Optional   
                'product_code' => $product->code ?? null, // Optional
                'session_id' => $sessionId,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart successfully',
        ]);
    }

//show cart
public function cartList()
{
    $sessionId = session()->getId();
    $userId = auth()->check() ? auth()->id() : null;
    
    // Fetch all cart items for the session or logged-in user
    $cartItems = AddToCart::with('product')->where(function ($query) use ($userId, $sessionId) {
        $query->where('session_id', $sessionId);
        if ($userId) {
            $query->orWhere('user_id', $userId);
        }
    })->get();

    $transformedCartItems = $cartItems->map(function ($item) {
        return [
            'id' => $item->id,
            'product_name' => $item->product_name ?? null, // Use the product's name if available
            'categore'=>$item->product_category ?? null,
            'quantity' => $item->quantity,
            'price' => $item->price,
            'image' => $item->product->image ?? null, // Use the product's image if available
            'total_price' => $item->quantity * $item->price, // Calculate total price
        ];
    });

    return response()->json([
        'message' => 'Cart items retrieved successfully',
        'data' => $transformedCartItems,
    ]);
}
//remove quantity from cart
public function decreaseQuantity($cart_id)
{
    // Find the cart item using cart ID
    $cartItem = AddToCart::find($cart_id);

    if (!$cartItem) {
        return response()->json([
            'message' => 'Cart item not found',
        ], 404);
    }

    // Check if user is authenticated
    $user = auth()->user(); // Get the authenticated user
    if (!$user) {
        return response()->json(['message' => 'User not authenticated.'], 401);
    }

    // Decrement the quantity or remove the item if quantity is 1
    if ($cartItem->quantity > 1) {
        $cartItem->quantity -= 1;
        $cartItem->save();

        return response()->json([
            'message' => 'Quantity decreased successfully.',
        ]);
    } else {
        $cartItem->delete();  // Remove the cart item if quantity is 1
        return response()->json([
            'message' => 'Cart item removed.',
        ]);
    }
}
//remove cart with id
public function removeCart($cart_id)
{
    $cartItem = AddToCart::find($cart_id);
    if (!$cartItem) {
        return response()->json([
            'message' => 'Cart item not found',
        ], 404);
    }
    $cartItem->delete();
    return response()->json([
        'message' => 'Cart item removed.',
    ]);
}


//delete cart
public function clearCart()
{
    $sessionId = session()->getId();
    $userId = auth()->check() ? auth()->id() : null;

    // Find and delete all cart items for the session or user
    $query = AddToCart::where('session_id', $sessionId);
    
    if ($userId) {
        // If user is logged in, also remove the user's cart items
        $query->orWhere('user_id', $userId);
    }

    // Delete all cart items
    $query->delete();

    return response()->json([
        'message' => 'All cart items have been removed successfully.',
    ]);
}


}


