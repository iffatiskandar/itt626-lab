@extends('layouts.template')

@section('title', 'Timetable')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Timetable</p>
            <h1>Weekly schedule</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('timetables.create') }}">Add entry</a>
    </div>

    <div class="week">
        @foreach ($days as $day)
            <section class="day">
                <h2>{{ $day->day_name }}</h2>

                @forelse ($timetables->get($day->id, collect()) as $entry)
                    <article class="slot">
                        <time>{{ $entry->time_from }}–{{ $entry->time_to }}</time>
                        <strong>{{ $entry->subject?->subject_code ?? '—' }}</strong>
                        <span>{{ $entry->subject?->subject_name ?? '—' }}</span>
                        <span class="mono">
                            {{ $entry->hall?->lecture_hall_name ?? '—' }}
                            @if ($entry->group) · {{ $entry->group->name }} @endif
                        </span>
                        <div class="slot-actions">
                            <a href="{{ route('timetables.edit', $entry) }}">Edit</a>
                            <form method="POST" action="{{ route('timetables.destroy', $entry) }}"
                                onsubmit="return confirm('Delete this entry?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="empty">Free</p>
                @endforelse
            </section>
        @endforeach
    </div>
@endsection