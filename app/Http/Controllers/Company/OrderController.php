<?php
namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index()
    {
        $company = auth()->user()->company;
        $orders = $company->orders()->with('user')->get();

        return response()->json($orders);
    }

    public function update(Request $request, $id)
    {
        $company = auth()->user()->company;
        $order = $company->orders()->findOrFail($id);

        $request->validate([
            'status' => 'required|in:accepted,rejected,process,success',
        ]);

        $order->update(['status' => $request->status]);

        return response()->json($order);
    }
}