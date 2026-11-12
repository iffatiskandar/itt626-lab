<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        return view('students.index', ['students' => User::orderBy('name')->get()]);
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return redirect()->route('students.index')->with('success', 'Student created.');
    }

    public function edit(User $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $data = $this->validated($request, $student);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student updated.');
    }

    public function destroy(User $student)
    {
        if ($student->is(auth()->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if (Timetable::where('user_id', $student->id)->exists()) {
            return back()->with('error', 'Delete this student\'s timetable entries first.');
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }

    private function validated(Request $request, ?User $student = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'between:1,150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student)],
            'password' => [$student ? 'nullable' : 'required', 'string', 'min:8'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);
    }
}
