<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Permission
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permission.index');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permission.store');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permission.create');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
    Route::post('/permissions/{id}', [PermissionController::class, 'update'])->name('permission.update');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->name('permission.destroy');

    // Role
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::post('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');


    // Jobs
    Route::get('/jobs', [JobController::class, 'index'])->name('jobPost.index');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobPost.store');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobPost.create');
    Route::get('/jobs/{id}', [JobController::class, 'show'])->name('jobPost.show');
    Route::get('/jobs/{id}/edit', [JobController::class, 'edit'])->name('jobPost.edit');
    Route::post('/jobs/{id}', [JobController::class, 'update'])->name('jobPost.update');
    Route::delete('/jobs/{id}', [JobController::class, 'destroy'])->name('jobPost.destroy');
    Route::get('/jobs/{id}/analytics', [JobController::class, 'viewAnalytics'])->name('jobPost.analytics');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Applications
    Route::get('/jobs/{job}/apply', [ApplicationController::class, 'create'])->name('jobs.apply');
    Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('jobs.apply.store');
    Route::get('/employer/jobs/{job}/applicants', [ApplicationController::class, 'showApplicants'])
        ->name('jobs.applicants')
        ->middleware('role:Employer');
    // Serve CV files securely
    Route::get('/applications/{application}/cv', [ApplicationController::class, 'downloadCV'])->name('applications.cv');
    // Update application status
    Route::patch('/applications/{application}/status', [ApplicationController::class, 'updateStatus'])->name('applications.updateStatus');
    // Show application for candidate
     Route::get('/my-applications', [ApplicationController::class, 'showApplicationForCandidate'])->name('jobs.application.show');
});

require __DIR__ . '/auth.php';
