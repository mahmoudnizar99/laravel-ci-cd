<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class ProductOrderController extends Controller
{

    public function index()
    {
        $products = Product::whereHas('company', function($query) {
            $query->where('is_active', true);
        })->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'payment_id' => 'required|string',
        ]);

        // Verify payment with Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json(['message' => 'Payment not completed'], 400);
            }

            $order = ProductOrder::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'name' => $request->name,
                'location' => $request->location,
                'payment_status' => 'paid',
                'payment_id' => $request->payment_id,
            ]);

            return response()->json($order, 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Payment verification failed'], 400);
        }
    }
}