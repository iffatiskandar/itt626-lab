@extends('layouts.template')

@section('title', 'Dashboard')

@section('content')
  <div class="page-head">
    <div>
      <p class="mono">Dashboard</p>
      <h1>{{ $todayName }}</h1>
    </div>
    <a class="btn btn-primary" href="{{ route('timetables.create') }}">Add entry</a>
  </div>

  <div class="stat-row">
    @foreach ($counts as $label => $count)
      <div class="stat">
        <span class="mono">{{ $label }}</span>
        <strong>{{ $count }}</strong>
      </div>
    @endforeach
  </div>

  <section class="panel">
    <div class="panel-head">
      <h2>Today's classes</h2>
      <a href="{{ route('timetables.index') }}">Week view</a>
    </div>

    @forelse ($today as $entry)
      <article class="entry">
        <time>{{ $entry->time_from }}–{{ $entry->time_to }}</time>
        <div>
          <strong>{{ $entry->subject?->subject_name ?? '—' }}</strong>
          <span>
            {{ $entry->subject?->subject_code ?? '—' }}
            · {{ $entry->hall?->lecture_hall_name ?? '—' }}
            @if ($entry->group) · {{ $entry->group->name }} @endif
          </span>
        </div>
        <a class="btn btn-small" href="{{ route('timetables.edit', $entry) }}">Edit</a>
      </article>
    @empty
      <p class="empty">No classes scheduled today.</p>
    @endforelse
  </section>
@endsection
