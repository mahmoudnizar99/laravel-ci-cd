<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobController extends Controller
{

    public function index()
    {
        $jobs = Job::whereHas('company', function($query) {
            $query->where('is_active', true);
        })->get();

        return response()->json($jobs);
    }

    public function apply(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $job = Job::findOrFail($id);

        $cvPath = $request->file('cv')->store('cv', 'public');
        $imagePath = $request->file('image')->store('images', 'public');

        $application = JobApplication::create([
            'user_id' => auth()->id(),
            'job_id' => $job->id,
            'name' => $request->name,
            'experience_years' => $request->experience_years,
            'cv' => $cvPath,
            'image' => $imagePath,
        ]);

        return response()->json($application, 201);
    }
}