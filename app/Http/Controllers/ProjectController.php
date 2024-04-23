<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['']]);
    }
    public function index()
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'priority' => 'required|in:low,medium,high',
            'client' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'progress' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $project = Project::create($request->all());
        return response()->json($project, 201);
    }

    public function show($id)
    {
        $project = Project::findOrFail($id);
        return response()->json($project);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'string|max:255',
            'priority' => 'in:low,medium,high',
            'client' => 'string|max:255',
            'price' => 'integer|min:0',
            'start_date' => 'date',
            'end_date' => 'date|after_or_equal:start_date',
            'progress' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $project = Project::findOrFail($id);
        $project->update($request->all());
        return response()->json($project, 200);
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();
        return response()->json([
            'message' => 'project deleted'
        ], 200);
    }
}
