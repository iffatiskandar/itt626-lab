<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DayController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TimetableController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    Route::get('/timetables', [App\Http\Controllers\TimetableController::class, 'index'])->name('timetables.index');
    Route::get('/timetables/create', [App\Http\Controllers\TimetableController::class, 'create'])->name('timetables.create');
    Route::post('/timetables', [App\Http\Controllers\TimetableController::class, 'store'])->name('timetables.store (http://timetables.store/)');
    Route::get('/timetables/{timetable}', [App\Http\Controllers\TimetableController::class, 'show'])->name('timetables.show (http://timetables.show/)');
    Route::get('/timetables/{timetable}/edit', [App\Http\Controllers\TimetableController::class, 'edit'])->name('timetables.edit');
    Route::put('/timetables/{timetable}', [App\Http\Controllers\TimetableController::class, 'update'])->name('timetables.update');
    Route::delete('/timetables/{timetable}', [App\Http\Controllers\TimetableController::class, 'destroy'])->name('timetables.destroy');

    Route::resource('subjects', SubjectController::class);
    Route::resource('halls', HallController::class);
    Route::resource('days', DayController::class);
    Route::resource('groups', GroupController::class);
    Route::resource('timetables', TimetableController::class);
});