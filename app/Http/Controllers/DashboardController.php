<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        // Nested eager loading: Project -> ProjectFiles -> Executions
        $projects = Project::with(['files.executions'])->get();

        return view('dashboard.index', compact('projects'));
    }

    public function show($id)
    {
        $project = Project::with(['files.executions'])->findOrFail($id);

        return view('dashboard.show', compact('project'));
    }
}
