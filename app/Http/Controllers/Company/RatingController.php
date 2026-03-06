<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{

    public function index()
    {
        $company = auth()->user()->company;
        $ratings = $company->ratings()->with('user')->get();
        return response()->json($ratings);
    }

    public function average()
    {
        $company = auth()->user()->company;
        $average = $company->ratings()->avg('rate');
        $count = $company->ratings()->count();

        return response()->json([
            'average' => round($average, 2),
            'count' => $count,
        ]);
    }
}