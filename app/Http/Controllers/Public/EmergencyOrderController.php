<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\FixedOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EmergencyOrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'location' => 'required|string|max:255',
        ]);

        $emergencyCompany = Company::where('type', 'emergency')
            ->where('is_active', true)
            ->first();

        $fixedOrder = FixedOrder::create([
            'user_id' => Auth::guard('sanctum')->user()->id ,
            'company_id' => $emergencyCompany ? $emergencyCompany->id : 1,
            'description' => $request->description,
            'location' => $request->location,
        ]);

        return response()->json($fixedOrder, 201);
    }
}