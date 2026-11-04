<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class StudentController extends Controller
{
    public function index()
    {
        $students = User::all();

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }
}


