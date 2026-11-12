<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        return view('master.index', [
            'title' => 'Subjects',
            'route' => 'subjects',
            'columns' => ['subject_code' => 'Code', 'subject_name' => 'Subject', 'lecturer_name' => 'Lecturer'],
            'rows' => Subject::orderBy('subject_code')->get(),
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        Subject::create($request->validate($this->rules()));

        return redirect()->route('subjects.index')->with('success', 'Subject created.');
    }

    public function edit(Subject $subject)
    {
        return $this->form($subject);
    }

    public function update(Request $request, Subject $subject)
    {
        $subject->update($request->validate($this->rules($subject)));

        return redirect()->route('subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        if (Timetable::where('subject_id', $subject->id)->exists()) {
            return back()->with('error', 'Subject is used by a timetable entry.');
        }

        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted.');
    }

    private function rules(?Subject $subject = null): array
    {
        return [
            'subject_code' => ['required', 'string', 'max:20', Rule::unique('subjects', 'subject_code')->ignore($subject)],
            'subject_name' => ['required', 'string', 'max:255'],
            'lecturer_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function form(?Subject $subject = null)
    {
        return view('master.form', [
            'title' => $subject ? 'Edit subject' : 'New subject',
            'route' => 'subjects',
            'action' => $subject ? route('subjects.update', $subject) : route('subjects.store'),
            'method' => $subject ? 'PUT' : 'POST',
            'record' => $subject,
            'fields' => [
                ['name' => 'subject_code', 'label' => 'Subject code', 'required' => true],
                ['name' => 'subject_name', 'label' => 'Subject name', 'required' => true],
                ['name' => 'lecturer_name', 'label' => 'Lecturer'],
            ],
        ]);
    }
}
