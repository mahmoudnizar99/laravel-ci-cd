<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function show()
    {
        $company = auth()->user()->company;
        return response()->json($company);
    }

    public function update(Request $request)
    {
        $company = auth()->user()->company;

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'type' => 'sometimes|string|max:255',
        ]);

        $company->update($request->all());

        return response()->json($company);
    }
}