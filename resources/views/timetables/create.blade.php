@extends('layouts.template')

@section('title', 'New timetable entry')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Timetable</p>
            <h1>New entry</h1>
        </div>
    </div>

    @include('timetables._form', ['entry' => null])
@endsection