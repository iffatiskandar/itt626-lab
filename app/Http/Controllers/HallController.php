<?php

namespace App\Http\Controllers;

use App\Models\Hall;
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
            'columns' => ['lecture_hall_name' => 'Code', 'lecture_hall_place' => 'Place'],
            'rows' => Hall::orderBy('lecture_hall_name')->get(),
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
            'lecture_hall_name' => ['required', 'string', 'max:255', Rule::unique('halls', 'lecture_hall_name')->ignore($hall)],
            'lecture_hall_place' => ['required', 'string', 'max:255'],
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
                ['name' => 'lecture_hall_name', 'label' => 'Hall code', 'required' => true],
                ['name' => 'lecture_hall_place', 'label' => 'Place', 'required' => true],
            ],
        ]);
    }
}