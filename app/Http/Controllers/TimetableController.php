<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Group;
use App\Models\Hall;
use App\Models\Subject;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TimetableController extends Controller
{
    public function index()
    {
        return view('timetables.index', [
            'days' => Day::orderBy('id')->get(),
            'timetables' => Timetable::with(['subject', 'hall', 'group', 'user'])
                ->orderBy('time_from')
                ->get()
                ->groupBy('day_id'),
        ]);
    }

    public function create()
    {
        return view('timetables.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $this->assertFree($data);
        $data['user_id'] = $request->user()->id;

        Timetable::create($data);

        return redirect()->route('timetables.index')->with('success', 'Timetable entry created.');
    }

    public function edit(Timetable $timetable)
    {
        return view('timetables.edit', ['timetable' => $timetable] + $this->formData());
    }

    public function update(Request $request, Timetable $timetable)
    {
        $data = $request->validate($this->rules());
        $this->assertFree($data, $timetable);
        $timetable->update($data);

        return redirect()->route('timetables.index')->with('success', 'Timetable entry updated.');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();

        return redirect()->route('timetables.index')->with('success', 'Timetable entry deleted.');
    }

    private function rules(): array
    {
        return [
            'day_id' => ['required', 'exists:days,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'hall_id' => ['required', 'exists:halls,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'time_from' => ['required', 'date_format:H:i'],
            'time_to' => ['required', 'date_format:H:i', 'after:time_from'],
        ];
    }

    // shortcut: clash check is not transactional; upgrade to a DB constraint under concurrent writes.
    private function assertFree(array $data, ?Timetable $ignore = null): void
    {
        $clash = Timetable::query()
            ->where('day_id', $data['day_id'])
            ->where('time_from', '<', $data['time_to'])
            ->where('time_to', '>', $data['time_from'])
            ->where(function ($query) use ($data) {
                $query->where('hall_id', $data['hall_id']);

                if (! empty($data['group_id'])) {
                    $query->orWhere('group_id', $data['group_id']);
                }
            })
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->getKey()))
            ->exists();

        if ($clash) {
            throw ValidationException::withMessages([
                'time_from' => 'That hall or group is already booked for this time.',
            ]);
        }
    }

    private function formData(): array
    {
        return [
            'days' => Day::orderBy('id')->pluck('day_name', 'id'),
            'subjects' => Subject::orderBy('subject_code')->get()
                ->mapWithKeys(fn ($subject) => [$subject->id => "{$subject->subject_code} — {$subject->subject_name}"]),
            'halls' => Hall::orderBy('lecture_hall_name')->get()
                ->mapWithKeys(fn ($hall) => [$hall->id => "{$hall->lecture_hall_name} — {$hall->lecture_hall_place}"]),
            'groups' => Group::orderBy('name')->pluck('name', 'id'),
        ];
    }
}
