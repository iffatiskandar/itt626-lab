<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subject;
use App\Models\Day;
use App\Models\Hall;
use App\Models\Group;

class Timetable extends Model
{
    use HasFactory;

    public $table = 'timetables';
    protected $fillable = [
        'user_id',
        'subject_id',
        'day_id',
        'hall_id',
        'group_id',
        'time_from',
        'time_to',
    ];

    public function index()
    {
        $timetables = Timetable::with('day', 'subject', 'hall')
        ->get();

        
        return view('timetables.index',compact('timetables'));
    }

    public function hall(){
        return $this->belongsTo('App\Models\Hall', 'hall_id');
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function day(){
        return $this->belongsTo(Day::class);
    }
}
