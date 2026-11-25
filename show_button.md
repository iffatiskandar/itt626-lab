# Show Button Plan

Goal: every record menu gets a `Show` action before `Edit`. `Show` opens a read-only detail page.

Menus covered: Dashboard, Timetable, Subjects, Halls, Days, Groups, Students.

No CSS change. Reuse `panel`, `form-grid`, `field`, `row-actions`, `btn btn-small`.

Line numbers below are current file lines before editing.

## 1. Routes

File: `routes/web.php`, lines 19-24.

Remove `->except('show')` from all six resources:

```php
Route::resource('students', StudentController::class);
Route::resource('timetables', TimetableController::class);
Route::resource('subjects', SubjectController::class);
Route::resource('halls', HallController::class);
Route::resource('days', DayController::class);
Route::resource('groups', GroupController::class);
```

This creates `students.show`, `timetables.show`, `subjects.show`, `halls.show`, `days.show`, `groups.show`.

## 2. Controllers

### `app/Http/Controllers/StudentController.php`

Insert after line 15, before `create()` at line 17:

```php
    public function show(User $student)
    {
        return view('students.show', compact('student'));
    }
```

### `app/Http/Controllers/TimetableController.php`

Insert after `index()` closes at line 65, before `create()` at line 68:

```php
    public function show(Timetable $timetable)
    {
        return view('timetables.show', [
            'timetable' => $timetable->load(['subject', 'hall', 'group', 'day', 'user']),
        ]);
    }
```

### `app/Http/Controllers/SubjectController.php`

Insert after line 20, before `create()` at line 22:

```php
    public function show(Subject $subject)
    {
        return view('master.show', [
            'title' => 'Subject details',
            'route' => 'subjects',
            'columns' => ['subject_code' => 'Code', 'subject_name' => 'Subject', 'lecturer_name' => 'Lecturer'],
            'record' => $subject,
        ]);
    }
```

### `app/Http/Controllers/HallController.php`

Insert after line 20, before `create()` at line 22:

```php
    public function show(Hall $hall)
    {
        return view('master.show', [
            'title' => 'Hall details',
            'route' => 'halls',
            'columns' => ['lecture_hall_name' => 'Code', 'lecture_hall_place' => 'Place'],
            'record' => $hall,
        ]);
    }
```

### `app/Http/Controllers/DayController.php`

Insert after line 20, before `create()` at line 22:

```php
    public function show(Day $day)
    {
        return view('master.show', [
            'title' => 'Day details',
            'route' => 'days',
            'columns' => ['day_name' => 'Day'],
            'record' => $day,
        ]);
    }
```

### `app/Http/Controllers/GroupController.php`

Insert after line 20, before `create()` at line 22:

```php
    public function show(Group $group)
    {
        return view('master.show', [
            'title' => 'Group details',
            'route' => 'groups',
            'columns' => ['name' => 'Group', 'part' => 'Part'],
            'record' => $group,
        ]);
    }
```

## 3. New Views

### New file: `resources/views/master/show.blade.php`

```blade
@extends('layouts.template')

@section('title', $title)

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Master data</p>
            <h1>{{ $title }}</h1>
        </div>
        <a class="btn btn-ghost" href="{{ route($route . '.index') }}">Back</a>
    </div>

    <div class="panel form">
        <div class="form-grid">
            @foreach ($columns as $key => $label)
                <div class="field">
                    <span>{{ $label }}</span>
                    <p>{{ data_get($record, $key) ?? '-' }}</p>
                </div>
            @endforeach
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="{{ route($route . '.edit', $record) }}">Edit</a>
            <a class="btn btn-ghost" href="{{ route($route . '.index') }}">Back</a>
        </div>
    </div>
@endsection
```

### New file: `resources/views/students/show.blade.php`

```blade
@extends('layouts.template')

@section('title', 'Student details')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Students</p>
            <h1>{{ $student->name }}</h1>
        </div>
        <a class="btn btn-ghost" href="{{ route('students.index') }}">Back</a>
    </div>

    <div class="panel form">
        <div class="form-grid">
            <div class="field"><span>Name</span><p>{{ $student->name }}</p></div>
            <div class="field"><span>Email</span><p>{{ $student->email }}</p></div>
            <div class="field"><span>Age</span><p>{{ $student->age ?? '-' }}</p></div>
            <div class="field"><span>Phone</span><p>{{ $student->phone_number ?? '-' }}</p></div>
            <div class="field"><span>Address</span><p>{{ $student->address ?? '-' }}</p></div>
            <div class="field"><span>Joined</span><p>{{ $student->created_at?->format('d M Y') ?? '-' }}</p></div>
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="{{ route('students.edit', $student) }}">Edit</a>
            <a class="btn btn-ghost" href="{{ route('students.index') }}">Back</a>
        </div>
    </div>
@endsection
```

### New file: `resources/views/timetables/show.blade.php`

```blade
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
            <div class="field"><span>Day</span><p>{{ $timetable->day?->day_name ?? '-' }}</p></div>
            <div class="field"><span>Time</span><p>{{ substr($timetable->time_from, 0, 5) }} to {{ substr($timetable->time_to, 0, 5) }}</p></div>
            <div class="field"><span>Subject code</span><p>{{ $timetable->subject?->subject_code ?? '-' }}</p></div>
            <div class="field"><span>Subject name</span><p>{{ $timetable->subject?->subject_name ?? '-' }}</p></div>
            <div class="field"><span>Lecturer</span><p>{{ $timetable->subject?->lecturer_name ?? '-' }}</p></div>
            <div class="field"><span>Hall</span><p>{{ $timetable->hall?->lecture_hall_name ?? '-' }}</p></div>
            <div class="field"><span>Place</span><p>{{ $timetable->hall?->lecture_hall_place ?? '-' }}</p></div>
            <div class="field"><span>Group</span><p>{{ $timetable->group?->name ?? '-' }}</p></div>
            <div class="field"><span>Booked by</span><p>{{ $timetable->user?->name ?? '-' }}</p></div>
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="{{ route('timetables.edit', $timetable) }}">Edit</a>
            <a class="btn btn-ghost" href="{{ route('timetables.index') }}">Back</a>
        </div>
    </div>
@endsection
```

## 4. Show Links Before Edit

### `resources/views/home.blade.php`, line 40

Replace:

```blade
        <a class="btn btn-small" href="{{ route('timetables.edit', $entry) }}">Edit</a>
```

with:

```blade
        <div class="row-actions">
          <a class="btn btn-small" href="{{ route('timetables.show', $entry) }}">Show</a>
          <a class="btn btn-small" href="{{ route('timetables.edit', $entry) }}">Edit</a>
        </div>
```

### `resources/views/master/index.blade.php`, line 32

Insert before the existing `Edit` link:

```blade
                                <a class="btn btn-small" href="{{ route($route . '.show', $row) }}">Show</a>
```

### `resources/views/students/table.blade.php`, line 21

Insert before the existing `Edit` link:

```blade
                            <a class="btn btn-small" href="{{ route('students.show', $student) }}">Show</a>
```

### `resources/views/timetables/index.blade.php`, lines 43-44

Timetable grid has no `Edit` button; each slot is a link. Change the slot to open details:

Replace:

```blade
                                        <a class="slot" href="{{ route('timetables.edit', $entry) }}"
                                            aria-label="Edit {{ $entry->subject?->subject_code ?? 'entry' }} on {{ $row['day']->day_name }} at {{ substr($entry->time_from, 0, 5) }}">
```

with:

```blade
                                        <a class="slot" href="{{ route('timetables.show', $entry) }}"
                                            aria-label="View {{ $entry->subject?->subject_code ?? 'entry' }} on {{ $row['day']->day_name }} at {{ substr($entry->time_from, 0, 5) }}">
```

Edit stays reachable from the timetable show page.

## 5. Check

- `php artisan route:list --name=show` lists six show routes.
- Open each menu: Show appears before Edit.
- Open each Show page: details render, Edit opens the existing edit form.
- Timetable grid slot opens details; Edit button on that page opens the edit form.
- No changes to `storage/framework/views/*`; those are compiled files.
