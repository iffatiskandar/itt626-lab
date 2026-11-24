@extends('layouts.template')

@section('title', 'Timetable')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Timetable</p>
            <h1 id="timetable-title">Weekly schedule</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('timetables.create') }}">Add entry</a>
    </div>

    @if ($columns === [])
        <div class="panel">
            <p class="empty">No entries yet. <a href="{{ route('timetables.create') }}">Add the first one</a>.</p>
        </div>
    @else
        <div class="panel table-wrap">
            <table class="timetable" aria-labelledby="timetable-title">
                <thead>
                    <tr>
                        <th scope="col">Day</th>
                        @foreach ($columns as $column)
                            <th scope="col">
                                <time datetime="{{ $column['from'] }}">{{ $column['from'] }}</time>–<time
                                    datetime="{{ $column['to'] }}">{{ $column['to'] }}</time>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        @php $isToday = $row['day']->id == now()->dayOfWeekIso; @endphp
                        <tr @class(['is-today' => $isToday])>
                            <th scope="row">
                                {{ $row['day']->day_name }}
                                @if ($isToday)<span class="day-flag">Today</span>@endif
                            </th>
                            @foreach ($row['cells'] as $cell)
                                <td @if ($cell['span'] > 1) colspan="{{ $cell['span'] }}" @endif>
                                    @foreach ($cell['entries'] as $entry)
                                        <a class="slot" href="{{ route('timetables.edit', $entry) }}"
                                            aria-label="Edit {{ $entry->subject?->subject_code ?? 'entry' }} on {{ $row['day']->day_name }} at {{ substr($entry->time_from, 0, 5) }}">
                                            <strong>{{ $entry->subject?->subject_code ?? '—' }}</strong>
                                            <span class="slot-name">{{ $entry->subject?->subject_name ?? 'Untitled' }}</span>
                                            <span class="slot-meta">{{ $entry->hall?->lecture_hall_name ?? '—' }}@if ($entry->group) ·
                                            {{ $entry->group->name }}@endif</span>
                                        </a>
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection