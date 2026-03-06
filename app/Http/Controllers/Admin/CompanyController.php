<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::with('user')->get();
        return response()->json($companies);
    }

    public function activate($id)
    {
        $company = Company::findOrFail($id);
        $company->update(['is_active' => true]);

        return response()->json(['message' => 'Company activated successfully']);
    }
}