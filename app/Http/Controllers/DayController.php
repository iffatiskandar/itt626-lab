<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DayController extends Controller
{
    public function index()
    {
        return view('master.index', [
            'title' => 'Days',
            'route' => 'days',
            'columns' => ['day_name' => 'Day'],
            'rows' => Day::orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        Day::create($request->validate($this->rules()));

        return redirect()->route('days.index')->with('success', 'Day created.');
    }

    public function edit(Day $day)
    {
        return $this->form($day);
    }

    public function update(Request $request, Day $day)
    {
        $day->update($request->validate($this->rules($day)));

        return redirect()->route('days.index')->with('success', 'Day updated.');
    }

    public function destroy(Day $day)
    {
        if (Timetable::where('day_id', $day->id)->exists()) {
            return back()->with('error', 'Day is used by a timetable entry.');
        }

        $day->delete();

        return redirect()->route('days.index')->with('success', 'Day deleted.');
    }

    private function rules(?Day $day = null): array
    {
        return [
            'day_name' => ['required', 'string', 'max:20', Rule::unique('days', 'day_name')->ignore($day)],
        ];
    }

    private function form(?Day $day = null)
    {
        return view('master.form', [
            'title' => $day ? 'Edit day' : 'New day',
            'route' => 'days',
            'action' => $day ? route('days.update', $day) : route('days.store'),
            'method' => $day ? 'PUT' : 'POST',
            'record' => $day,
            'fields' => [
                ['name' => 'day_name', 'label' => 'Day name', 'required' => true],
            ],
        ]);
    }
}