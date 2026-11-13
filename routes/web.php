<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DayController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TimetableController;

Route::redirect('/', '/login');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('students', StudentController::class)->except('show');
    Route::resource('timetables', TimetableController::class)->except('show');
    Route::resource('subjects', SubjectController::class)->except('show');
    Route::resource('halls', HallController::class)->except('show');
    Route::resource('days', DayController::class)->except('show');
    Route::resource('groups', GroupController::class)->except('show');
});