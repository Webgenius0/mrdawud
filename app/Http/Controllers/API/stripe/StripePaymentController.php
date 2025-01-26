<?php

namespace App\Http\Controllers\API\stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Token;
use Stripe\StripeClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class StripePaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function orderChockout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            //table join for information purposes
            'product_id' => 'required|integer',
            'uuid' => 'required|string',
            'user_id' => 'required|integer',
            'category_id' => 'required|integer',
            'billing_address_id' => 'required|integer',            
            'payment_method_id' => 'required|string',
            
            'product_name' => 'required',
            'title' => 'required',           
            'quantity' => 'required',           
            'email' => 'required',

            //amount
            'price' => 'required|numeric',
            'subtotal' => 'required',
            'tax' => 'nullable|numeric',


            
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ]);
        }
        DB::beginTransaction();
        
        try {
            $validateData=$validator->validated();

            $productPrice= Product::find($request->product_id);
        } catch (\Throwable $th) {
            //throw $th;
        }
            
    }
   

     /**
     *  store order information
     */

     private function storeOrder(Request $request)
     {
         $order = Order::create([
             'user_id' => $request->user_id,
             'product_id' => $request->product_id,
             'uuid' => $request->uuid,
             'category_id' => $request->category_id,
             'billing_address_id' => $request->billing_address_id,
             'payment_method_id' => $request->payment_method_id,
             'product_name' => $request->product_name,
             'title' => $request->title,
             'quantity' => $request->quantity,
             'email' => $request->email,
             'price' => $request->price,
            'subtotal' => $request->subtotal,
             'tax' => $request->tax,
         ]);

         return $order;
     }
}
