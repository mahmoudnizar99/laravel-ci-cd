<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::where('is_active', true)
            ->with(['user', 'ratings'])
            ->get();

        return response()->json($companies);
    }
}