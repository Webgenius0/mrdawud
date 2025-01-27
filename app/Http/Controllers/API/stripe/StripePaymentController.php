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


class StripePaymentController extends Controller
{
    public  function addMethodToCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation error:'.$validator->errors()->first(),[], 422);
        }

        // Retrieve validated data
        $validatedData = $validator->validated();

        try {
            $paymentMethod = PaymentMethod::retrieve($validatedData["payment_method_id"]);

            // Check if the PaymentMethod is attached or reusable
            if ($paymentMethod->attached) {
                return  $this->sendError('This payment method is already attached to a customer or is not reusable',[], 422);
            }

            // Create a new customer if it doesn't exist
            $customer = $this->createCustomerIfNotExist();
            // Attach the payment method to the customer
            $paymentMethod->attach([
                'customer' => $customer->id,
            ]);

            Customer::update($customer->id,[
                'invoice_settings' => [
                    'default_payment_method' => $request->payment_method_id,
                ]
            ]);

            return $this->sendResponse([], 'Payment method attached successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error attaching payment method: ' . $e->getMessage(), [], 500);
        }
    }

    private function createCustomerIfNotExist()
    {
        $user=auth()->user();
        $customerData = [
            'name' => $user->name,
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
            if (!$customer) {
                $customer = Customer::create($customerData);
                $user->stripe_customer_id = $customer->id;
                $user->save();
            }
        }
        return $customer;
    }
   /**
     * Retrieve customer payment methods with auth user stripe customer ID
     */
    
     public function getCustomerPaymentMethods()
     {
        try {
            $user= auth()->user();
            if($user->stripe_customer_id=== null||empty($user->stripe_customer_id)){
                return $this->sendError('No valid Stripe Customer Id found. Please create a Stripe account to add payment methods.',(object)[],404);

                //Retrieve the Stripe customer

                $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);

                //if customer retrieval fails 

                if(!$customer || empty ($customer->id)){
                    return $this->sendError('Customer not found in Stripe.',(object)[],404);
                }

                //Retrive the cusstomer's Payment methods

                $paymentMethods= \Stripe\PaymentMethod::all([
                    'customer'=>$customer->id,
                    'type'=>'card',
                ]);
                //check if no payment methods are found

                if(empty($paymentMethods->data)){
                    return $this->sendError('No payment methods found.',(object)[],404);
                }

                 // Convert Stripe\Collection data into a Laravel Collection
            $paymentMethodsCollection = collect($paymentMethods->data);

            // Return the first payment method
            return $this->sendResponse(StripeCardResource::collection($paymentMethodsCollection), 'Payment methods retrieved successfully.');

                
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle specific Stripe API errors
            return $this->sendError('Stripe API error: ' . $e->getMessage(), [], 500);
        } catch (\Exception $e) {
            // Handle general exceptions
            return $this->sendError( 'An error occurred: ' . $e->getMessage(), [], 500);
        }
     }

}

