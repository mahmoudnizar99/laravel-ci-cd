<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FixedOrder;
use Illuminate\Http\Request;

class FixedOrderController extends Controller
{
    public function index()
    {
        $fixedOrders = FixedOrder::with(['user', 'company'])->get();
        return response()->json($fixedOrders);
    }

    public function destroy($id)
    {
        $fixedOrder = FixedOrder::findOrFail($id);
        $fixedOrder->delete();

        return response()->json(null, 204);
    }
}