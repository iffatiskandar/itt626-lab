<?php

namespace App\Http\Controllers;

use App\Models\Halls;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HallController extends Controller
{
    public function index()
    {
        return view('master.index', [
            'title' => 'Halls',
            'route' => 'halls',
            'columns' => ['hall_code' => 'Code', 'hall_name' => 'Hall', 'lecturer_name' => 'Lecturer'],
            'rows' => Hall::orderBy('hall_code')->get(),
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        Hall::create($request->validate($this->rules()));

        return redirect()->route('halls.index')->with('success', 'Hall created.');
    }

    public function edit(Hall $hall)
    {
        return $this->form($hall);
    }

    public function update(Request $request, Hall $hall)
    {
        $hall->update($request->validate($this->rules($hall)));

        return redirect()->route('halls.index')->with('success', 'Hall updated.');
    }

    public function destroy(Hall $hall)
    {
        if (Timetable::where('hall_id', $hall->id)->exists()) {
            return back()->with('error', 'Hall is used by a timetable entry.');
        }

        $hall->delete();

        return redirect()->route('halls.index')->with('success', 'Hall deleted.');
    }

    private function rules(?Hall $hall = null): array
    {
        return [
            'hall_code' => ['required', 'string', 'max:20', Rule::unique('halls', 'hall_code')->ignore($hall)],
            'hall_name' => ['required', 'string', 'max:255'],
            'lecturer_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function form(?Hall $hall = null)
    {
        return view('master.form', [
            'title' => $hall ? 'Edit hall' : 'New hall',
            'route' => 'halls',
            'action' => $hall ? route('halls.update', $hall) : route('halls.store'),
            'method' => $hall ? 'PUT' : 'POST',
            'record' => $hall,
            'fields' => [
                ['name' => 'hall_code', 'label' => 'Hall code', 'required' => true],
                ['name' => 'hall_name', 'label' => 'Hall name', 'required' => true],
                ['name' => 'lecturer_name', 'label' => 'Lecturer'],
            ],
        ]);
    }
}
