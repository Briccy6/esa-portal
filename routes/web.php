<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\AcademicYearController; 
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\CourseController;// Adjust path as per your structure

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
});

Route::get('/courses/search', [App\Http\Controllers\CourseController::class, 'search'])->name('courses.search');

// Admin routes
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Academic Year resource routes with correct prefix and names
        Route::resource('academic_year', AcademicYearController::class)->except(['show']);
          Route::get('/course/create', [CourseController::class, 'create'])->name('course.create');
Route::get('/courses', [CourseController::class, 'index'])->name('course.index');
Route::get('/course/{id}/edit', [CourseController::class, 'edit'])->name('course.edit'); 
        Route::put('/course/{id}', [CourseController::class, 'update'])->name('course.update');
        Route::delete('/course/{id}', [CourseController::class, 'destroy'])->name('course.destroy');

    Route::post('/course/store', [CourseController::class, 'store'])->name('course.store');
    });

