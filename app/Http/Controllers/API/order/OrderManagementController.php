<?php

namespace App\Http\Controllers\API\order;

use App\Http\Controllers\Controller;
use App\Models\AddToCart;
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
            'name' => 'nullable|string|max:255',  // Ensure 'name' is required
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:15',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'zip_code' => 'nullable|string|max:20',
            'billing_address_id' => 'nullable',
            'payment_method_id' => 'required|string',  // Add validation for payment_method_id
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
    
            // Get products from the cart
            $products = AddToCart::where('user_id', $user->id)->get();
           // Log::info('Products: ' . $products);
            $totalPrice = 0;
            $totalTaxes = 0;
    
            foreach ($products as $product) {
                $productRecord = Product::find($product['product_id']);
                //Log::info('Product Record: ' . $productRecord->price);
                if ($productRecord) {
                    // Check for sufficient stock
                    if ($productRecord->stock < $product['quantity']) {
                        return $this->sendError('Insufficient stock for product ' . $productRecord->name, []);
                    }
                    $productRecord->stock -= $product['quantity'];
                    $productRecord->save(); // Update stock
    
                    // Calculate taxes and total price
                    
                    $totalPrice += $product['price']*$product['quantity'];
                    $taxes = $totalPrice* $productRecord['taxes'] / 100;
                    //Log::info('Taxes: ' . $taxes);
                    $totalTaxes += $taxes;
                   // Log::info('Total Amount: ' . $totalPrice);
                } else {
                    return $this->sendError('Product not found with ID ' . $product['product_id'], []);
                }
            }
    
            // Ensure total price meets the Stripe minimum charge amount
            $minimumAmount = 50; // 50 cents, Stripe minimum is $0.50 USD
            if ($totalPrice * 100 < $minimumAmount) {
                return $this->sendError('The total amount is below the minimum allowed charge for your account.', [], 422);
            }
    
            // Store the order data
            $order = $this->storeOrderData($validateData, $request, $totalPrice, $totalTaxes);
    
            // Store the billing information
            $this->storeBillingInfo($validateData, $order);
    
            // Store order items
            $this->storeOrderItems($validateData, $order);
            Log::info('Order Items: ' . $order);

    
            // Create payment intent
            $this->createPaymentIntent($validateData, $order);
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'code' => 200,
                'message' => 'Your order has been successfully placed and is currently being processed.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            //Log::error("Order creation failed: " . $e->getMessage());  // Log the error
            return $this->sendError('Something went wrong while processing your order. Please try again later.', $e->getMessage(), 422);
        }
    }
    
    
    /**
     * Store order data
     */
    private function storeOrderData($validateData, $request,$totalPrice,$totalTaxes)
    {
        return Order::create([
            'uuid' => substr((string) Str::uuid(), 0, 8),
            'user_id' => auth()->id(),
            'sub_total' => $totalPrice, //without taxes
            'taxes'     => $totalTaxes , // taxes amount
            'total_price' => $totalPrice + $totalTaxes,  // Store the total price provided in the request
        ]);
    }

    /**
     * Store billing info
     */
    private function storeBillingInfo($validateData, $order)
    {
        $user = auth()->user();
        
        // Check if 'billing_address_id' is provided in the request
        if (!empty($validateData['billing_address_id'])) {
            // If billing_address_id is passed, use it if it exists
            $billingAddress = BillingAddress::find($validateData['billing_address_id']);
            
            if ($billingAddress && $billingAddress->user_id === $user->id) {
                // If the billing address exists for the user, associate it with the order
                $order->billing_address_id = $billingAddress->id;
                $order->save();
                Log::info('Billing address assigned to order:', [$order]);
            } else {
                // If the billing address does not exist or doesn't belong to the user, return an error
                return $this->sendError('Invalid billing address.', [], 422);
            }
        } else {
            // If 'billing_address_id' is not provided, ensure that the required fields are present to create a new address
            $requiredFields = ['name', 'address', 'phone_number', 'city', 'state', 'zip_code'];
            
            foreach ($requiredFields as $field) {
                if (empty($validateData[$field])) {
                    return $this->sendError('Missing required billing address fields: ' . $field, [], 422);
                }
            }
            
            // If no 'billing_address_id' is provided, create a new billing address
            $billingAddress = BillingAddress::create([
                'user_id' => $user->id,
                'order_id' => $order->id,  // Link the order to this new address
                'name' => $validateData['name'],
                'address' => $validateData['address'],
                'phone_number' => $validateData['phone_number'],
                'city' => $validateData['city'],
                'state' => $validateData['state'],
                'zip_code' => $validateData['zip_code'],
            ]);
    
            Log::info('New billing address created:', [$billingAddress]);
    
            // Now associate the billing address with the order
            $order->billing_address_id = $billingAddress->id;
            
            // Log before saving the order
            Log::info('Before saving order with billing_address_id:', [$order->billing_address_id]);
    
            $order->save();
            
            // Log after saving the order
            Log::info('Order saved successfully:', [$order]);
        }
    }
    

    
    /**
     * Store order items
     */
    private function storeOrderItems($validateData, $order)
    {
        // Get the add to cart products for the logged-in user
        $products = AddToCart::where('user_id', auth()->id())->get();
        
        foreach ($products as $product) {
            // Get the product details from the Product table
            $productRecord = Product::find($product->product_id);
            Log::info('Product Record: ' . $productRecord);
            // Ensure the product exists in the database
            if (!$productRecord) {
                continue;  // Skip this item if the product doesn't exist
            }
    
            // Check if the AddToCart quantity exceeds the stock available in the Product table
            $quantityToOrder = $product->quantity;
            if ($productRecord->stock < $product->quantity) {
                // If AddToCart quantity exceeds stock, use the available stock
                $quantityToOrder = $productRecord->stock;
            }
    
            // Calculate the total price for the item
            $totalPrice = $productRecord->price * $quantityToOrder;
            
            // Calculate taxes based on the product's tax rate
            $taxes = ($productRecord->taxes / 100) * $totalPrice;
           
           
            // Create the order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->product_id,
                'quantity' => $quantityToOrder,
                'price' => $productRecord->price,
                'taxes' => $taxes,
                'total_price' => $totalPrice ,  // Total price including taxes
            ]);
    
            // If the quantity in AddToCart was greater than the available stock, update the stock of the product
            if ($quantityToOrder < $product->quantity) {
                // Update the stock of the product to reflect the quantity that was actually processed
                $productRecord->stock -= $quantityToOrder;
                $productRecord->save();
                //Log::info('Product stock updated: ' . $productRecord->stock);
            }
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
