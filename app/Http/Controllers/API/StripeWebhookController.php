<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
   
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Handle Stripe webhooks
     */
    
    public function handleWebhook(Request $request)
    {

        $payload        = $request->getContent();
        $sigHeader      = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);

            // Handle the event
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    
                    $uuid = $paymentIntent->metadata->order_uuid;
                    $user_id = $paymentIntent->metadata->user_id;
                    // Update your database
                    $order = Order::where('uuid', $uuid)->where('user_id', $user_id)->first();
                    $order->update([
                        'status' => 'success',
                    ]);
                    
                    return response()->json([
                        'message' => "order payment success"
                    ]);
                    break;

                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                     
                    $uuid = $paymentIntent->metadata->order_uuid;
                    $user_id = $paymentIntent->metadata->user_id;
                    // Update your database
                    $order = Order::where('uuid', $uuid)->where('user_id', $user_id)->first();
                    $order->update([
                        'status' => 'failed',
                    ]);
                    return response()->json([
                        'message' => "order payment failed"
                    ]);
                    break;
            }
            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Webhook signature verification failed: ' . $e->getMessage()], 400);
        }
    }
}
