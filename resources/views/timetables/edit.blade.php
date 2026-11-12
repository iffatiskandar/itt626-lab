@extends('layouts.template')

@section('title', 'Edit timetable entry')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Timetable</p>
            <h1>Edit entry</h1>
        </div>
    </div>

    @include('timetables._form', ['entry' => $timetable])
@endsection