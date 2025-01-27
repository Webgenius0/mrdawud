<?php

namespace App\Http\Controllers\API\stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
use App\Models\StripeCard;
class StripeCardController extends Controller
{
    use apiresponse;

    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ]);
        }
        $card = StripeCard::where('user_id', $user->id)->select('id', 'card_name', 'card_number', 'card_expiry', 'card_cvc')->get();
        return response()->json([
            'status' => 200,
            'message' => 'Card fetched successfully',
            'data' => [
                'card' => $card,
            ]
        ], 200);
    }


    // store card
    public function storeCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_name' => 'required',
            'card_number' => 'required',
            'card_expiry' => 'required',
            'card_cvc' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ]);
        }
        DB::beginTransaction();
        try {
        
            $infromation = new StripeCard();
            $infromation->user_id = $user->id;
            $infromation->card_name = $request->card_name;
            $infromation->card_number = $request->card_number;
            $infromation->card_expiry = $request->card_expiry;
            $infromation->card_cvc = $request->card_cvc;
            $infromation->save();
            DB::commit();
            return response()->json([   
                'status' => 200,
                'message' => 'Card added successfully',
                'data' => $infromation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }





















    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
    public function orderChockout(Request $request)
    {
        //dd($request->all());
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'uuid' => 'required|string',
            'user_id' => 'required|integer',
            'category_id' => 'required|integer',
            'billing_address_id' => 'required|integer',
            'payment_method_id' => 'required|string',
            'product_name' => 'required|string',
            'title' => 'required|string',
            'quantity' => 'required|integer',
            'email' => 'required|email',
            'price' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'tax' => 'nullable|numeric',
        ]);

        // Return validation errors if validation fails
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ]);
        }

        DB::beginTransaction();
        
        try {
            // Get validated data
            $validatedData = $validator->validated();

            // Store order information
            $order = $this->storeOrder($validatedData);


            // Create payment intent
            $paymentIntent = $this->createPaymentIntent($validatedData, $order);

            // Fetch product price (if needed)
            $productPrice = Product::find($request->product_id);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Order created successfully',
                'client_secret' => $paymentIntent->client_secret,
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Store order information in the database.
     */
    private function storeOrder($validatedData)
    {
        return  Order::create([
            'user_id' => $validatedData['user_id'],
            'product_id' => $validatedData['product_id'],
            'uuid' => (string) Str::uuid(),
            'category_id' => $validatedData['category_id'],
            'billing_address_id' => $validatedData['billing_address_id'],
            'payment_method_id' => $validatedData['payment_method_id'],
            'product_name' => $validatedData['product_name'],
            'title' => $validatedData['title'],
            'quantity' => $validatedData['quantity'],
            'email' => $validatedData['email'],
            'price' => $validatedData['price'],
            'subtotal' => $validatedData['subtotal'],
            'tax' => $validatedData['tax'],
        ]);

    }

    /**
     * Create payment intent with Stripe.
     */
    private function createPaymentIntent($validatedData, $order)
    {
        // Set up metadata for the payment intent
        $metadata = [
            'order_id' => $order->id,
            'product_id' => $order->product_id,
            'user_id' => $order->user_id,
            'category_id' => $order->category_id,
            'billing_address_id' => $order->billing_address_id,
            'payment_method_id' => $order->payment_method_id,
            'product_name' => $order->product_name,
            'title' => $order->title,
            'quantity' => $order->quantity,
            'email' => $order->email,
            'price' => $order->price,
            'subtotal' => $order->subtotal,
            'tax' => $order->tax,
        ];

        // Create Stripe Payment Intent (price is in cents)
        return PaymentIntent::create([
            'amount' => $order->price * 100, // Price in cents
            'currency' => 'usd',
            'metadata' => $metadata,
        ]);

        
    }
}
