<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{

    public function index()
    {
        $orders = auth()->user()->orders()
            ->with('company')
            ->latest()
            ->get();

        return response()->json($orders);
    }

    /**
     * Create a new order
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $company = Company::findOrFail($request->company_id);
        if (!$company->is_active) {
            return response()->json(['message' => 'This company is not active'], 400);
        }

        $imagePath = $request->file('image')->store('orders', 'public');

        $order = Order::create([
            'user_id' => auth()->id(),
            'company_id' => $request->company_id,
            'image' => $imagePath,
            'name' => $request->name,
            'location' => $request->location,
            'phone' => $request->phone,
            'status' => 'wait',
        ]);

        return response()->json($order, 201);
    }


    public function show($id)
    {
        $order = auth()->user()->orders()
            ->with('company')
            ->findOrFail($id);

        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $order = auth()->user()->orders()->findOrFail($id);

        if ($order->status !== 'wait') {
            return response()->json(['message' => 'You can only update orders in "wait" status'], 400);
        }

        $request->validate([
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($order->image);
            
            $data['image'] = $request->file('image')->store('orders', 'public');
        }

        $order->update($data);

        return response()->json($order);
    }


    public function destroy($id)
    {
        $order = auth()->user()->orders()->findOrFail($id);

        if ($order->status !== 'wait') {
            return response()->json(['message' => 'You can only cancel orders in "wait" status'], 400);
        }

        $order->update(['status' => 'rejected']);

        return response()->json(['message' => 'Order cancelled successfully']);
    }
}