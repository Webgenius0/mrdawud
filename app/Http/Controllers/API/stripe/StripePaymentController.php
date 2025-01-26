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
            'product_id' => 'required',
            'user_id' => 'required',
            'category_id' => 'required',
            'title' => 'required',
            'price' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',           
            'email' => 'required',
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ]);
        }
        DB::beginTransaction();
        
            
    }
    public function StripePayment(Request $request)
    {
        try {

          $resp=$stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $stripe->tokens->create([
                'card' => [
                    'number' => $request->number,
                    'exp_month' => $request->exp_month,
                    'exp_year' => $request->exp_year,
                    'cvc' => $request->cvc,
                ],
            ]);


           
          $response=  $stripe->charges->create([
                'amount' => $request->amount,
                'currency' => 'usd',
                'source' => $resp->id,
            ]);
            return response()->json([
                'status' => 200,
                'data' => $response
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                dd($th->getMessage()),
                'status' => 500,
                'data' => $th
            ]);
        }
    }
}
