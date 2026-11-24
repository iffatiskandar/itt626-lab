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

    <form class="actions" method="POST" action="{{ route('timetables.destroy', $timetable) }}"
        onsubmit="return confirm('Delete this entry?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger" type="submit">Delete entry</button>
    </form>

@endsection