<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'rate' => 'required|integer|between:1,5',
            'comment' => 'sometimes|string|max:500',
        ]);

        $existingRating = Rating::where('user_id', auth()->id())
            ->where('company_id', $request->company_id)
            ->first();

        if ($existingRating) {
            return response()->json(['message' => 'You have already rated this company'], 400);
        }

        $rating = Rating::create([
            'user_id' => auth()->id(),
            'company_id' => $request->company_id,
            'rate' => $request->rate,
            'comment' => $request->comment ?? null,
        ]);

        return response()->json($rating, 201);
    }
}