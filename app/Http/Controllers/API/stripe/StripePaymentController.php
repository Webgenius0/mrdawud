<?php

namespace App\Http\Controllers\API\stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Token;
use App\Http\Resources\StripeCardResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Stripe\PaymentMethod;
use Stripe\Customer;
use App\Traits\apiresponse;


class StripePaymentController extends Controller
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
    * Add payment method to customer
    */
    public function addMethodToCustomer(Request $request)
    {
       // dd($request->all());
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation error: ' . $validator->errors()->first(), [], 422);
        }

        // Retrieve validated data
        $validatedData = $validator->validated();

        try {
            $paymentMethod = PaymentMethod::retrieve($validatedData["payment_method_id"]);

            // Check if the PaymentMethod is attached or reusable
            if ($paymentMethod->attached) {
                return $this->sendError('This payment method is already attached to a customer or is not reusable', [], 422);
            }

            // Create a new customer if it doesn't exist
            $customer = $this->createCustomerIfNotExist();

            // Attach the payment method to the customer
            $paymentMethod->attach([
                'customer' => $customer->id,
            ]);

            // Update the customer's default payment method
            Customer::update($customer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method_id,
                ]
            ]);

            // Respond with success
            return $this->sendResponse([], 'Payment method attached successfully');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return $this->sendError('Stripe API error: ' . $e->getMessage(), [], 500);
        } catch (\Exception $e) {
            return $this->sendError('Error attaching payment method: ' . $e->getMessage(), [], 500);
        }
    }
    private function createCustomerIfNotExist()
    {
        $user=auth()->user();
        $customerData = [
            'name' => $user->username,
            'email' => $user->email
        ];

        if (!empty($user->email)) {
            $customerData['email'] = $user->email;
        }
        if (empty($user->stripe_customer_id)) {
            $customer = Customer::create($customerData);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        } else {
            $customer = Customer::retrieve($user->stripe_customer_id);
            $user->stripe_customer_id = $customer->id;
            $user->save();
        }
        return $customer;
    }
   /**
     * Retrieve customer payment methods with auth user stripe customer ID
     */
    
     public function getCustomerPaymentMethods()
     {
         try {
             $user = auth()->user();
     
             // Check if Stripe customer ID is not present
             if (empty($user->stripe_customer_id)) {
                 return $this->sendError('No valid Stripe Customer Id found. Please create a Stripe account to add payment methods.', (object)[], 404);
             }
     
             // Retrieve the Stripe customer
             $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);
     
             // Check if customer retrieval fails
             if (!$customer || empty($customer->id)) {
                 return $this->sendError('Customer not found in Stripe.', (object)[], 404);
             }
     
             // Retrieve the customer's payment methods
             $paymentMethods = \Stripe\PaymentMethod::all([
                 'customer' => $customer->id,
                 'type' => 'card',
             ]);
     
             // Check if no payment methods are found
             if (empty($paymentMethods->data)) {
                 return $this->sendError('No payment methods found.', (object)[], 404);
             }
     
             // Convert Stripe\Collection data into a Laravel Collection
             $paymentMethodsCollection = collect($paymentMethods->data);
     
             // Return the list of payment methods
             return $this->sendResponse(StripeCardResource::collection($paymentMethodsCollection), 'Payment methods retrieved successfully.');
     
         } catch (\Stripe\Exception\ApiErrorException $e) {
             // Handle specific Stripe API errors
             return $this->sendError('Stripe API error: ' . $e->getMessage(), [], 500);
         } catch (\Exception $e) {
             // Handle general exceptions
             return $this->sendError('An error occurred: ' . $e->getMessage(), [], 500);
         }
     }
     
    /**
     * Remove payment method
     */
public function removeCustomerPaymentMethod($paymentMethodID)
{
   try {
    $user=auth()->user();

    if($user->stripe_customer_id === null || empty($user->stripe_customer_id)){
        return $this->sendError('No valid Stripe customer Id found.Please create a Stripe account to add payment methods.',(object)[],404);
    }

    $customer= Customer::retrieve($user->stripe_customer_id);
    if(!$customer || empty($customer->id)){
        return $this->sendError('Customer not found in Stripe.',(object)[],404);
    }
    //Retrieve the payment method by Id

    $paymentMethod= \Stripe\PaymentMethod::retrieve($paymentMethodID);
    //check if the payment method exists
    if(!$paymentMethod || empty($paymentMethod->id)){
        return $this->sendError('Payment method not found.',(object)[],404);
    }

   // $this->deleteSubscriptionAndCustomer();
    $paymentMethod->detach();
    return $this->sendResponse([],'Payment method removed Successfully.');
   } catch (\Exception $e) {
    return $this->sendError('An error occurredf:' .$e->getMessage(), [], 500);
   }

}
}
