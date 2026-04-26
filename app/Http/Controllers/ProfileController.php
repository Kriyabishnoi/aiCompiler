<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index() {
        $user = auth()->user();
        $projects = $user->projects;

        $stats = [
    'total' => $projects->count(),
    'c' => $projects->where('language', 'c')->count(),
    'cpp' => $projects->where('language', 'cpp')->count(),
    'java' => $projects->where('language', 'java')->count(),
    'python' => $projects->where('language', 'python')->count(),
    'scala' => $projects->where('language', 'scala')->count(),
    'ruby' => $projects->where('language', 'ruby')->count(),
    'kotlin' => $projects->where('language', 'kotlin')->count(),
];

        return view('profile', compact('user', 'stats'));
    }

    public function update(Request $request) {
        $user = auth()->user();

        if($request->hasFile('avatar')){
            $path = $request->file('avatar')->store('avatars','public');
            $user->avatar = $path;
        }

        $user->name = $request->name;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Profile updated successfully!');
    }
}