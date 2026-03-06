<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductOrder;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController extends Controller
{
    public function createOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'order_id' => 'required|exists:product_orders,id'
        ]);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        
        $order = ProductOrder::findOrFail($request->order_id);
        
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "purchase_units" => [
                0 => [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $request->amount
                    ],
                    "reference_id" => "order_" . $order->id
                ]
            ]
        ]);
        
        if (isset($response['id']) && $response['id'] != null) {
            $order->update(['payment_id' => $response['id']]);
            
            return response()->json([
                'status' => 'success',
                'paypal_order_id' => $response['id'],
                'approve_url' => collect($response['links'])
                    ->where('rel', 'approve')
                    ->first()['href']
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create PayPal order'
        ], 500);
    }

    public function captureOrder(Request $request)
    {
        $request->validate([
            'paypal_order_id' => 'required',
            'order_id' => 'required|exists:product_orders,id'
        ]);

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $token = $provider->getAccessToken();
        
        $response = $provider->capturePaymentOrder($request->paypal_order_id);
        
        $order = ProductOrder::findOrFail($request->order_id);
        
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            $order->update([
                'payment_status' => 'paid',
                'payment_data' => json_encode($response)
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Payment completed successfully',
                'order' => $order
            ]);
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Payment capture failed',
            'details' => $response
        ], 400);
    }
}