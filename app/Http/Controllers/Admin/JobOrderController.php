<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobOrderController extends Controller
{
    public function index()
    {
        $jobApplications = JobApplication::with(['user', 'job.company'])->get();
        return response()->json($jobApplications);
    }
}