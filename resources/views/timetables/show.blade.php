@extends('layouts.template')

@section('title', 'Timetable entry')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Timetable</p>
            <h1>{{ $timetable->subject?->subject_code ?? 'Entry' }}</h1>
        </div>
        <a class="btn btn-ghost" href="{{ route('timetables.index') }}">Back</a>
    </div>

    <div class="panel form">
        <div class="form-grid">
            <div class="field"><span>Day</span>
                <p>{{ $timetable->day?->day_name ?? '-' }}</p>
            </div>
            <div class="field"><span>Time</span>
                <p>{{ substr($timetable->time_from, 0, 5) }} to {{ substr($timetable->time_to, 0, 5) }}</p>
            </div>
            <div class="field"><span>Subject code</span>
                <p>{{ $timetable->subject?->subject_code ?? '-' }}</p>
            </div>
            <div class="field"><span>Subject name</span>
                <p>{{ $timetable->subject?->subject_name ?? '-' }}</p>
            </div>
            <div class="field"><span>Lecturer</span>
                <p>{{ $timetable->subject?->lecturer_name ?? '-' }}</p>
            </div>
            <div class="field"><span>Hall</span>
                <p>{{ $timetable->hall?->lecture_hall_name ?? '-' }}</p>
            </div>
            <div class="field"><span>Place</span>
                <p>{{ $timetable->hall?->lecture_hall_place ?? '-' }}</p>
            </div>
            <div class="field"><span>Group</span>
                <p>{{ $timetable->group?->name ?? '-' }}</p>
            </div>
            <div class="field"><span>Booked by</span>
                <p>{{ $timetable->user?->name ?? '-' }}</p>
            </div>
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="{{ route('timetables.edit', $timetable) }}">Edit</a>
            <a class="btn btn-ghost" href="{{ route('timetables.index') }}">Back</a>
        </div>
    </div>
@endsection