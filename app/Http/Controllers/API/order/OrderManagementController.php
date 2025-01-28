<?php

namespace App\Http\Controllers\API\order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\apiresponse;
use Stripe\Stripe;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\BillingAddress;
use Illuminate\Support\Facades\Log;
use Stripe\Customer;
use Stripe\PaymentIntent;

class OrderManagementController extends Controller
{
    use apiresponse;

    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function sendResponse($data, $message)
    {
        return response()->json([
            'status' => 200,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    // Method to send an error response
    public function sendError($message, $data = [], $code = 400)
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Create order
     */
    public function orderCheckout(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'sub_total' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'name' => 'required|string|max:255',  // Ensure 'name' is required
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'payment_method_id' => 'required|string',  // Add validation for payment_method_id
            'products' => 'required|array',
            'products.*.product_id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.unite_price' => 'required|numeric',
            'products.*.total_price' => 'required|numeric',  // Total price field validation
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 422);
        }

        DB::beginTransaction();
        try {
            $validateData = $validator->validated();

            $user = auth()->user();
            if (empty($user->stripe_customer_id)) {
                return $this->sendError('No Stripe customer ID found. Please add a payment method and try again.', []);
            }

            // Retrieve the customer from Stripe
            $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);
            if (!$customer || empty($customer->id)) {
                return $this->sendError('Customer not found in Stripe.', []);
            }

            // Calculate the total price for the products and update stock quantity
            $totalPrice = 0;
            foreach ($validateData['products'] as $product) {
                $productRecord = Product::find($product['product_id']);
                if ($productRecord) {
                    if ($productRecord->stock < $product['quantity']) {
                        return $this->sendError('Insufficient stock for product ' . $productRecord->name, []);
                    }
                    $productRecord->stock -= $product['quantity'];
                    $productRecord->save(); // Don't forget to save the updated stock
                } else {
                    return $this->sendError('Product not found with ID ' . $product['product_id'], []);
                }

                // Add total price based on the product's total price from the request
                $totalPrice += $product['total_price'];  // Use total_price from the request
            }

            // Store the order data
            $order = $this->storeOrderData($validateData, $request->input('sub_total'), $request);

            // Store the billing information
            $this->storeBillingInfo($validateData, $order);

            // Store order items
            $this->storeOrderItems($validateData, $order);

            // Create payment intent
            $this->createPaymentIntent($validateData, $order);

            DB::commit();
            return response()->json([
                'status' =>  true,
                'code' => 200,
                'message' => 'Your order has been successfully placed and is currently being processed.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error("Order creation failed: " . $e->getMessage());  // Log the error
            return $this->sendError('Something went wrong while processing your order. Please try again later.', $e->getMessage(), 422);
        }
    }

    /**
     * Store order data
     */
    private function storeOrderData($validateData, $sub_total, $request)
    {
        $product = Product::find($validateData['products'][0]['product_id']);
        $taxes = $product ? $product->taxes : 0; 
        return Order::create([
            'uuid' => substr((string) Str::uuid(), 0, 8),
            'user_id' => auth()->id(),
            'sub_total' => $sub_total,
            'tax' => $taxes,
            'status' => 'ongoing',
            'total_price' => $request->input('total'),  // Store the total price provided in the request
        ]);
    }

    /**
     * Store billing info
     */
    private function storeBillingInfo($validateData, $order)
    {
        $user = auth()->user();
        $billingAddress = BillingAddress::where('user_id', $user->id)->first();

        if ($billingAddress) {
            // Update the existing billing address
            $billingAddress->update([
                'name' => $validateData['name'],
                'address' => $validateData['address'],
                'phone_number' => $validateData['phone_number'],
                'city' => $validateData['city'],
                'state' => $validateData['state'],
                'zip_code' => $validateData['zip_code'],
            ]);

        } else {
            // Create a new billing address
            BillingAddress::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'name' => $validateData['name'],
                'address' => $validateData['address'],
                'phone_number' => $validateData['phone_number'],
                'city' => $validateData['city'],
                'state' => $validateData['state'],
                'zip_code' => $validateData['zip_code'],
            ]);
      
        }
    }

    /**
     * Store order items
     */
    private function storeOrderItems($validateData, $order)
    {
        foreach ($validateData['products'] as $product) {
            $taxes = $product['total_price'] * 0.10; 
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'price' => $product['unite_price'],
                'taxes' => $taxes,
            ]);
        }
    }

    /**
     * Create payment intent
     */
    private function createPaymentIntent($validateData, $order)
    {
        $orderData = Order::find($order->id);
        $user = Auth::user();
        $metadata = [
            "order_uuid" => $orderData->uuid,
            'user_id' => $user->id,
        ];

        PaymentIntent::create([
            'amount' => $orderData->total_price * 100,  // Convert dollars to cents
            'currency' => 'usd',
            'metadata' => $metadata,
            'payment_method' => $validateData['payment_method_id'],  // Use the payment_method_id from the request
            'customer' => $user->stripe_customer_id,
            'confirm' => true,
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ],
        ]);
    }
}
