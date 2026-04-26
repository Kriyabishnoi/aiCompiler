<?php

namespace App\Http\Controllers;

use App\Models\Project;

class EditorController extends Controller
{
    public function open($id)
    {
        $project = Project::findOrFail($id);

        return view('editor', compact('project'));
    }
}
