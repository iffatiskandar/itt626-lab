<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Group;
use App\Models\Hall;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $today = now()->dayOfWeekIso;

        return view('home', [
            'counts' => [
                'Students' => User::count(),
                'Subjects' => Subject::count(),
                'Halls' => Hall::count(),
                'Groups' => Group::count(),
                'Entries' => Timetable::count(),
            ],
            'today' => Timetable::with(['subject', 'hall', 'group'])
                ->where('day_id', $today)
                ->orderBy('time_from')
                ->get(),
            'todayName' => Day::find($today)?->day_name ?? 'Today',
        ]);
    }
}
