<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\ProfileController; // ✅ IMPORTANT
use App\Http\Controllers\AIController;


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/login', fn () => view('login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', fn () => view('register'))->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // DASHBOARD
    Route::get('/dashboard', [ProjectController::class, 'index'])
        ->name('dashboard');

    // PROJECT
    Route::post('/project/create', [ProjectController::class, 'createViaAjax'])
        ->name('project.create.ajax');

    Route::put('/project/{id}', [ProjectController::class, 'update']);
    Route::delete('/project/{id}', [ProjectController::class, 'destroy']);

    // EDITOR
    Route::get('/editor/{project}', [EditorController::class, 'open'])
        ->name('editor');

    // SAVED PROGRAMS
    Route::get('/saved-programs', [ProjectController::class, 'savedPrograms'])
        ->name('saved.programs');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile');

    Route::post('/profile/update', [ProfileController::class, 'update'])
        ->name('profile.update');

    // COMPILER
    Route::post('/run-code', [ProjectController::class, 'runCode']);
    Route::get('/download/{id}', [ProjectController::class,'download']);
    Route::delete('/delete/{id}', [ProjectController::class,'destroy']);
    Route::get('/editor/{id}', [ProjectController::class,'editor'])
->name('editor.program');
Route::post('/ai/suggest', [AIController::class, 'suggest']);
Route::post('/ai-chat', [AIController::class, 'chat']);
Route::match(['get','post'], '/ai-chat', [AIController::class, 'chat']);


});