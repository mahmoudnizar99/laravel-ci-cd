<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use Illuminate\Http\Request;

class JobController extends Controller
{

    public function index()
    {
        $company = auth()->user()->company;
        $jobs = $company->jobs;
        return response()->json($jobs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
        ]);

        $company = auth()->user()->company;
        $job = $company->jobs()->create($request->all());

        return response()->json($job, 201);
    }

    public function show($id)
    {
        $company = auth()->user()->company;
        $job = $company->jobs()->findOrFail($id);
        return response()->json($job);
    }

    public function update(Request $request, $id)
    {
        $company = auth()->user()->company;
        $job = $company->jobs()->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'requirements' => 'sometimes|string',
        ]);

        $job->update($request->all());

        return response()->json($job);
    }

    public function destroy($id)
    {
        $company = auth()->user()->company;
        $job = $company->jobs()->findOrFail($id);
        $job->delete();

        return response()->json(null, 204);
    }

    public function applications($id)
    {
        $company = auth()->user()->company;
        $job = $company->jobs()->findOrFail($id);
        $applications = $job->applications()->with('user')->get();

        return response()->json($applications);
    }
}