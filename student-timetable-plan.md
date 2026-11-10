# Student Timetable Management — Plan

Project: `C:\laragon\www\itt626` · Laravel 13 · PHP 8.3 · Blade · MySQL `itt626` · existing models/tables/seeders reused.

## 0. Pre-flight

- **Keep:** Laravel MVC, existing tables (`users`, `subjects`, `halls`, `days`, `groups`, `timetables`), seeders, route names, Blade.
- **Replace:** AdminLTE demo shell, broken student/timetable views, empty CRUD controllers, duplicate timetable routes.
- **Drop:** public registration (`Auth::routes(['register' => false])`), unused `show` screens, AdminLTE demo JS/widgets.
- **No new dependency.** Plain CSS served from `public/css/app.css`; no Vite build required.
- **Assumed brief:** audience = students + one admin; use = create/edit a weekly class timetable and master data; tone = calm, technical, utilitarian.

## 1. Hallmark direction

- **Genre:** modern-minimal (administrative tool, not a marketing page).
- **Theme:** Cobalt — cool near-white paper, cool charcoal ink, one electric cobalt signal, hairline structure, 6–10px radii, one graphite sidebar.
- **Macrostructure:** Workbench for the dashboard, Index-First for list screens, weekly board for the timetable.
- **Type:** Space Grotesk (display) + Inter (body) + JetBrains Mono (labels/meta).
- **Motion:** 160ms ease-out on hover/focus/active only. No reveals, parallax, bounce, or autoplay.
- **Accessibility:** skip link, visible labels, `:focus-visible` ring, flash `role`, native confirm dialogs, reduced-motion support.
- **Mobile:** sidebar becomes a horizontal scrolling top bar; forms collapse to one column; tables scroll inside their own wrapper; no page-level horizontal scroll.

**Hallmark preview (plan-only)**

- Macrostructure · Workbench + Index-First
- Theme · Cobalt (cool light · electric blue · grotesk + mono)
- Enrichment · none (typography + real data)
- Screens · Login · Dashboard · Weekly timetable · Timetable form · Students · Subjects · Halls · Days · Groups
- Motion · hover · focus · active
- Slop test · not run (no code emitted yet)
- Diversification · first Hallmark run (no `.hallmark/log.json`)

## 2. File map

| File | Action |
|---|---|
| `routes/web.php` | replace |
| `.env` | change `APP_URL` |
| `app/Http/Controllers/StudentController.php` | replace |
| `app/Http/Controllers/TimetableController.php` | replace |
| `app/Http/Controllers/HomeController.php` | replace |
| `app/Http/Controllers/SubjectController.php` | replace |
| `app/Http/Controllers/HallController.php` | replace |
| `app/Http/Controllers/DayController.php` | replace |
| `app/Http/Controllers/GroupController.php` | replace |
| `app/Models/Timetable.php` | replace |
| `public/css/app.css` | new |
| `resources/views/layouts/template.blade.php` | replace |
| `resources/views/layouts/app.blade.php` | replace |
| `resources/views/auth/login.blade.php` | replace |
| `resources/views/home.blade.php` | replace |
| `resources/views/students/index.blade.php` | replace |
| `resources/views/students/table.blade.php` | replace |
| `resources/views/students/_form.blade.php` | new |
| `resources/views/students/create.blade.php` | replace |
| `resources/views/students/edit.blade.php` | replace |
| `resources/views/students/show.blade.php` | delete (unused) |
| `resources/views/master/index.blade.php` | new |
| `resources/views/master/form.blade.php` | new |
| `resources/views/timetables/index.blade.php` | new |
| `resources/views/timetables/_form.blade.php` | new |
| `resources/views/timetables/create.blade.php` | new |
| `resources/views/timetables/edit.blade.php` | new |
| `tests/Feature/TimetableTest.php` | new |

Migrations: fix the two empty `down()` methods only if rollback matters (see `backend-repair-plan.md` § P2). Do not run `migrate:fresh`.

## 3. Backend code

### 3.1 `routes/web.php` — replace

```php
<?php

use App\Http\Controllers\DayController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TimetableController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'home' : 'login'));

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::resource('students', StudentController::class)->except('show');
    Route::resource('subjects', SubjectController::class)->except('show');
    Route::resource('halls', HallController::class)->except('show');
    Route::resource('days', DayController::class)->except('show');
    Route::resource('groups', GroupController::class)->except('show');
    Route::resource('timetables', TimetableController::class)->except('show');
});
```

### 3.2 `.env` — change one line

```dotenv
APP_URL=http://itt626.test
```

Then `php artisan config:clear`.

### 3.3 `app/Http/Controllers/StudentController.php` — replace

```php
<?php

namespace App\Http\Controllers;

use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        return view('students.index', ['students' => User::orderBy('name')->get()]);
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return redirect()->route('students.index')->with('success', 'Student created.');
    }

    public function edit(User $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, User $student)
    {
        $data = $this->validated($request, $student);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student updated.');
    }

    public function destroy(User $student)
    {
        if ($student->is(auth()->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if (Timetable::where('user_id', $student->id)->exists()) {
            return back()->with('error', 'Delete this student\'s timetable entries first.');
        }

        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted.');
    }

    private function validated(Request $request, ?User $student = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'age' => ['nullable', 'integer', 'between:1,150'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student)],
            'password' => [$student ? 'nullable' : 'required', 'string', 'min:8'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ]);
    }
}
```

`User` already casts `password => hashed`; pass the plain password once.

### 3.4 `app/Http/Controllers/TimetableController.php` — replace

```php
<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Group;
use App\Models\Hall;
use App\Models\Subject;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TimetableController extends Controller
{
    public function index()
    {
        return view('timetables.index', [
            'days' => Day::orderBy('id')->get(),
            'timetables' => Timetable::with(['subject', 'hall', 'group', 'user'])
                ->orderBy('time_from')
                ->get()
                ->groupBy('day_id'),
        ]);
    }

    public function create()
    {
        return view('timetables.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $this->assertFree($data);
        $data['user_id'] = $request->user()->id;

        Timetable::create($data);

        return redirect()->route('timetables.index')->with('success', 'Timetable entry created.');
    }

    public function edit(Timetable $timetable)
    {
        return view('timetables.edit', ['timetable' => $timetable] + $this->formData());
    }

    public function update(Request $request, Timetable $timetable)
    {
        $data = $request->validate($this->rules());
        $this->assertFree($data, $timetable);
        $timetable->update($data);

        return redirect()->route('timetables.index')->with('success', 'Timetable entry updated.');
    }

    public function destroy(Timetable $timetable)
    {
        $timetable->delete();

        return redirect()->route('timetables.index')->with('success', 'Timetable entry deleted.');
    }

    private function rules(): array
    {
        return [
            'day_id' => ['required', 'exists:days,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'hall_id' => ['required', 'exists:halls,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
            'time_from' => ['required', 'date_format:H:i'],
            'time_to' => ['required', 'date_format:H:i', 'after:time_from'],
        ];
    }

    // shortcut: clash check is not transactional; upgrade to a DB constraint under concurrent writes.
    private function assertFree(array $data, ?Timetable $ignore = null): void
    {
        $clash = Timetable::query()
            ->where('day_id', $data['day_id'])
            ->where('time_from', '<', $data['time_to'])
            ->where('time_to', '>', $data['time_from'])
            ->where(function ($query) use ($data) {
                $query->where('hall_id', $data['hall_id']);

                if (! empty($data['group_id'])) {
                    $query->orWhere('group_id', $data['group_id']);
                }
            })
            ->when($ignore, fn ($query) => $query->whereKeyNot($ignore->getKey()))
            ->exists();

        if ($clash) {
            throw ValidationException::withMessages([
                'time_from' => 'That hall or group is already booked for this time.',
            ]);
        }
    }

    private function formData(): array
    {
        return [
            'days' => Day::orderBy('id')->pluck('day_name', 'id'),
            'subjects' => Subject::orderBy('subject_code')->get()
                ->mapWithKeys(fn ($subject) => [$subject->id => "{$subject->subject_code} — {$subject->subject_name}"]),
            'halls' => Hall::orderBy('lecture_hall_name')->get()
                ->mapWithKeys(fn ($hall) => [$hall->id => "{$hall->lecture_hall_name} — {$hall->lecture_hall_place}"]),
            'groups' => Group::orderBy('name')->pluck('name', 'id'),
        ];
    }
}
```

### 3.5 `app/Models/Timetable.php` — replace

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject_id',
        'day_id',
        'hall_id',
        'group_id',
        'time_from',
        'time_to',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
```

Removed: the misplaced `index()` method and redundant `$table`.

### 3.6 `app/Http/Controllers/HomeController.php` — replace

```php
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
```

### 3.7 Master controllers

All four follow one pattern. `SubjectController` in full:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index()
    {
        return view('master.index', [
            'title' => 'Subjects',
            'route' => 'subjects',
            'columns' => ['subject_code' => 'Code', 'subject_name' => 'Subject', 'lecturer_name' => 'Lecturer'],
            'rows' => Subject::orderBy('subject_code')->get(),
        ]);
    }

    public function create()
    {
        return $this->form();
    }

    public function store(Request $request)
    {
        Subject::create($request->validate($this->rules()));

        return redirect()->route('subjects.index')->with('success', 'Subject created.');
    }

    public function edit(Subject $subject)
    {
        return $this->form($subject);
    }

    public function update(Request $request, Subject $subject)
    {
        $subject->update($request->validate($this->rules($subject)));

        return redirect()->route('subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        if (Timetable::where('subject_id', $subject->id)->exists()) {
            return back()->with('error', 'Subject is used by a timetable entry.');
        }

        $subject->delete();

        return redirect()->route('subjects.index')->with('success', 'Subject deleted.');
    }

    private function rules(?Subject $subject = null): array
    {
        return [
            'subject_code' => ['required', 'string', 'max:20', Rule::unique('subjects', 'subject_code')->ignore($subject)],
            'subject_name' => ['required', 'string', 'max:255'],
            'lecturer_name' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function form(?Subject $subject = null)
    {
        return view('master.form', [
            'title' => $subject ? 'Edit subject' : 'New subject',
            'route' => 'subjects',
            'action' => $subject ? route('subjects.update', $subject) : route('subjects.store'),
            'method' => $subject ? 'PUT' : 'POST',
            'record' => $subject,
            'fields' => [
                ['name' => 'subject_code', 'label' => 'Subject code', 'required' => true],
                ['name' => 'subject_name', 'label' => 'Subject name', 'required' => true],
                ['name' => 'lecturer_name', 'label' => 'Lecturer'],
            ],
        ]);
    }
}
```

Repeat for the other three with these values:

| Controller | `columns` | `fields` | `rules()` | Delete guard |
|---|---|---|---|---|
| `HallController` | `lecture_hall_name => 'Code'`, `lecture_hall_place => 'Place'` | `lecture_hall_name` (required), `lecture_hall_place` (required) | unique `halls.lecture_hall_name`; place required string max 255 | `hall_id` |
| `DayController` | `day_name => 'Day'` | `day_name` (required) | unique `days.day_name`; string max 20 | `day_id` |
| `GroupController` | `name => 'Group'`, `part => 'Part'` | `name` (required), `part` | unique `groups.name`; part nullable string max 10 | `group_id` |

Keep the same ordering, flash messages, `Rule::unique(...)->ignore($model)`, and `Timetable::where(<fk>, $model->id)->exists()` guard.

## 4. UI code

### 4.1 `public/css/app.css` — new

```css
/* Hallmark · genre: modern-minimal · macrostructure: Workbench + Index-First
 * theme: Cobalt · paper oklch(98.5% 0.004 250) · accent oklch(58% 0.20 256)
 * display: Space Grotesk · body: Inter · mono: JetBrains Mono
 * pre-emit critique: P5 H5 E4 S5 R5 V4
 */
:root {
  --paper: oklch(98.5% 0.004 250);
  --paper-2: oklch(96% 0.006 250);
  --ink: oklch(24% 0.02 258);
  --muted: oklch(52% 0.02 258);
  --rule: oklch(88% 0.008 250);
  --accent: oklch(58% 0.20 256);
  --accent-ink: oklch(98% 0.004 250);
  --danger: oklch(55% 0.19 25);
  --ok: oklch(55% 0.14 150);
  --graphite: oklch(22% 0.016 260);
  --font-display: "Space Grotesk", system-ui, sans-serif;
  --font-body: "Inter", system-ui, sans-serif;
  --font-mono: "JetBrains Mono", ui-monospace, monospace;
  --space-1: .25rem;
  --space-2: .5rem;
  --space-3: .75rem;
  --space-4: 1rem;
  --space-5: 1.5rem;
  --space-6: 2rem;
  --space-7: 3rem;
  --radius: 6px;
  --radius-lg: 10px;
  --ease: cubic-bezier(.2, .8, .2, 1);
  --dur: 160ms;
}

*, *::before, *::after { box-sizing: border-box; }
html, body { overflow-x: clip; }
html { scroll-behavior: smooth; }
body {
  margin: 0;
  background: var(--paper);
  color: var(--ink);
  font: 400 16px/1.55 var(--font-body);
  -webkit-font-smoothing: antialiased;
}
a { color: var(--accent); text-underline-offset: .2em; }
a:hover { text-decoration-thickness: 2px; }
:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
h1, h2, h3 { font-family: var(--font-display); letter-spacing: -.03em; line-height: 1.05; margin: 0; }
h1 { font-size: clamp(2rem, 4vw, 3.25rem); font-weight: 600; }
h2 { font-size: 1.1rem; font-weight: 600; }

.skip {
  position: absolute;
  left: -999px;
  top: 0;
  z-index: 100;
  padding: .6rem 1rem;
  background: var(--ink);
  color: var(--paper);
}
.skip:focus { left: .5rem; top: .5rem; }

.mono {
  margin: 0 0 .35rem;
  font: 500 .72rem/1.4 var(--font-mono);
  letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--muted);
}

/* App shell */
.shell { display: grid; grid-template-columns: 240px minmax(0, 1fr); min-height: 100dvh; }
.sidebar {
  position: sticky;
  top: 0;
  height: 100dvh;
  display: flex;
  flex-direction: column;
  gap: var(--space-6);
  padding: 1.25rem;
  overflow-y: auto;
  background: var(--graphite);
  color: var(--paper);
}
.brand {
  display: flex;
  flex-direction: column;
  gap: .15rem;
  color: var(--paper);
  text-decoration: none;
  font: 600 1.1rem/1.1 var(--font-display);
  letter-spacing: -.03em;
}
.brand span {
  font: 500 .68rem/1 var(--font-mono);
  letter-spacing: .08em;
  text-transform: uppercase;
  color: oklch(78% .01 258);
}
.sidebar nav { display: flex; flex-direction: column; gap: .15rem; }
.sidebar nav a {
  padding: .6rem .7rem;
  border-radius: var(--radius);
  color: oklch(82% .01 258);
  text-decoration: none;
  font-size: .92rem;
  white-space: nowrap;
  transition: background var(--dur) var(--ease), color var(--dur) var(--ease);
}
.sidebar nav a:hover { background: oklch(100% 0 0 / .08); color: var(--paper); }
.sidebar nav a.active { background: var(--accent); color: var(--accent-ink); }
.sidebar-foot { margin-top: auto; padding-top: 1rem; border-top: 1px solid oklch(100% 0 0 / .12); }
.sidebar-foot .btn { width: 100%; color: var(--paper); border-color: oklch(100% 0 0 / .25); }
.sidebar-foot .btn:hover { background: oklch(100% 0 0 / .08); }

.main { min-width: 0; padding: 1.5rem clamp(1rem, 4vw, 3rem) 3rem; }
.topbar {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 1rem;
  margin-bottom: var(--space-6);
  padding-bottom: var(--space-5);
  border-bottom: 1px solid var(--rule);
}
.topbar .who { margin: 0; color: var(--muted); font-size: .9rem; }

/* Page furniture */
.page-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: var(--space-4);
  flex-wrap: wrap;
  margin-bottom: var(--space-5);
}
.page-head h1 { max-width: 100%; overflow-wrap: anywhere; }
.panel { background: var(--paper); border: 1px solid var(--rule); border-radius: var(--radius-lg); }
.panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: var(--space-4);
  padding: var(--space-4) 1.1rem;
  border-bottom: 1px solid var(--rule);
}

/* Dashboard */
.stat-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(min(150px, 100%), 1fr));
  gap: 1px;
  margin-bottom: var(--space-6);
  overflow: hidden;
  background: var(--rule);
  border: 1px solid var(--rule);
  border-radius: var(--radius-lg);
}
.stat { display: flex; flex-direction: column; gap: .35rem; padding: var(--space-4) 1.1rem; background: var(--paper); }
.stat strong { font: 600 1.9rem/1 var(--font-display); letter-spacing: -.04em; }
.entry {
  display: grid;
  grid-template-columns: 7.5rem minmax(0, 1fr) auto;
  align-items: center;
  gap: var(--space-4);
  padding: var(--space-4) 1.1rem;
  border-bottom: 1px solid var(--rule);
}
.entry:last-child { border-bottom: 0; }
.entry time { font: 500 .8rem/1.4 var(--font-mono); color: var(--muted); }
.entry div { display: flex; flex-direction: column; min-width: 0; }
.entry span { color: var(--muted); font-size: .85rem; overflow-wrap: anywhere; }

/* Weekly timetable */
.week { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(190px, 100%), 1fr)); gap: var(--space-4); }
.day { min-width: 0; background: var(--paper); border: 1px solid var(--rule); border-radius: var(--radius-lg); }
.day h2 { padding: .8rem 1rem; border-bottom: 1px solid var(--rule); font-size: .95rem; }
.slot {
  display: flex;
  flex-direction: column;
  gap: .25rem;
  padding: 1rem;
  border-bottom: 1px solid var(--rule);
}
.slot:last-child { border-bottom: 0; }
.slot time { font: 500 .75rem/1.4 var(--font-mono); color: var(--accent); }
.slot strong { font-family: var(--font-display); font-size: 1rem; }
.slot span { color: var(--muted); font-size: .82rem; overflow-wrap: anywhere; }
.slot-actions { display: flex; gap: .75rem; margin-top: .5rem; font-size: .8rem; }
.slot-actions form { display: inline; }
.slot-actions button {
  padding: 0;
  border: 0;
  background: none;
  color: var(--danger);
  font: inherit;
  text-decoration: underline;
  text-underline-offset: .2em;
  cursor: pointer;
}

/* Forms */
.form { padding: 1.25rem; }
.form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: var(--space-4); }
.field { display: flex; flex-direction: column; gap: .35rem; min-width: 0; }
.field span {
  font: 500 .72rem/1.4 var(--font-mono);
  letter-spacing: .05em;
  text-transform: uppercase;
  color: var(--muted);
}
.field b { color: var(--danger); }
input, select, textarea {
  width: 100%;
  padding: .6rem .7rem;
  border: 1px solid var(--rule);
  border-radius: var(--radius);
  background: var(--paper);
  color: var(--ink);
  font: 400 .95rem/1.4 var(--font-body);
  transition: border-color var(--dur) var(--ease);
}
input:hover, select:hover, textarea:hover { border-color: oklch(72% .03 258); }
input:focus, select:focus, textarea:focus {
  border-color: var(--accent);
  outline: 2px solid oklch(58% .2 256 / .2);
  outline-offset: 1px;
}
input:disabled, select:disabled, textarea:disabled { opacity: .55; cursor: not-allowed; }
textarea { min-height: 5rem; resize: vertical; }
.error { color: var(--danger); font-size: .8rem; font-style: normal; }
.actions { display: flex; flex-wrap: wrap; gap: .6rem; margin-top: 1.25rem; }
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: .4rem;
  min-height: 2.4rem;
  padding: .5rem .85rem;
  border: 1px solid var(--rule);
  border-radius: var(--radius);
  background: var(--paper);
  color: var(--ink);
  font: 500 .88rem/1 var(--font-body);
  text-decoration: none;
  white-space: nowrap;
  cursor: pointer;
  transition: background var(--dur) var(--ease), border-color var(--dur) var(--ease), color var(--dur) var(--ease);
}
.btn:hover { background: var(--paper-2); border-color: oklch(72% .03 258); }
.btn:active { transform: translateY(1px); }
.btn:disabled { opacity: .5; cursor: not-allowed; }
.btn-primary { background: var(--accent); border-color: var(--accent); color: var(--accent-ink); }
.btn-primary:hover { background: oklch(52% .2 256); border-color: oklch(52% .2 256); }
.btn-ghost { background: transparent; }
.btn-danger { color: var(--danger); border-color: oklch(55% .19 25 / .35); }
.btn-danger:hover { background: oklch(55% .19 25 / .08); border-color: var(--danger); }
.btn-small { min-height: 2rem; padding: .3rem .55rem; font-size: .8rem; }
.alert {
  margin-bottom: var(--space-4);
  padding: .8rem 1rem;
  background: var(--paper);
  border: 1px solid var(--rule);
  border-left: 3px solid var(--ok);
  border-radius: var(--radius);
}
.alert-error { border-left-color: var(--danger); }

/* Tables */
.table-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; font-size: .9rem; }
th, td { padding: .7rem .85rem; border-bottom: 1px solid var(--rule); text-align: left; vertical-align: middle; }
th {
  font: 500 .7rem/1.4 var(--font-mono);
  letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--muted);
}
tbody tr:hover { background: var(--paper-2); }
.row-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: .4rem; }
.row-actions form { display: inline; }
.right { text-align: right; }
.empty { padding: 1.5rem; color: var(--muted); font-size: .9rem; }

/* Auth */
.auth { display: grid; place-items: center; min-height: 100dvh; padding: 1.25rem; background: var(--paper); }
.auth-card {
  width: min(100%, 26rem);
  padding: clamp(1.25rem, 4vw, 2rem);
  background: var(--paper);
  border: 1px solid var(--rule);
  border-top: 3px solid var(--accent);
  border-radius: var(--radius-lg);
}
.auth-card h1 { margin-bottom: 1.25rem; font-size: 2rem; }

/* Fallback for untouched auth views */
.container { width: min(100% - 2rem, 48rem); margin-inline: auto; padding-block: 2rem; }
.card { background: var(--paper); border: 1px solid var(--rule); border-radius: var(--radius-lg); }
.card-header, .card-body { padding: 1rem 1.25rem; }
.card-header { border-bottom: 1px solid var(--rule); font-family: var(--font-display); font-weight: 600; }
.form-control { width: 100%; padding: .6rem .7rem; border: 1px solid var(--rule); border-radius: var(--radius); background: var(--paper); font: inherit; }
.invalid-feedback { display: block; color: var(--danger); font-size: .8rem; }
.btn-block { width: 100%; }

@media (max-width: 860px) {
  .shell { grid-template-columns: minmax(0, 1fr); }
  .sidebar {
    position: static;
    height: auto;
    flex-direction: row;
    align-items: center;
    gap: .9rem;
    padding: .9rem 1rem;
    overflow-x: auto;
    overflow-y: hidden;
  }
  .sidebar nav { flex-direction: row; gap: .25rem; overflow-x: auto; overflow-y: hidden; padding-bottom: .1rem; }
  .brand { flex: 0 0 auto; }
  .sidebar-foot { margin: 0; padding: 0; border: 0; flex: 0 0 auto; }
  .sidebar-foot .btn { width: auto; }
  .main { padding: 1.1rem 1rem 2.5rem; }
  .topbar { margin-bottom: 1.25rem; }
  .form-grid { grid-template-columns: minmax(0, 1fr); }
  .entry { grid-template-columns: minmax(0, 1fr) auto; gap: .5rem; }
  .entry time { grid-column: 1 / -1; }
}

@media (max-width: 480px) {
  .page-head { flex-direction: column; align-items: flex-start; }
  .actions .btn, .actions button { flex: 1 1 auto; }
  .stat-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}

@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    transition-duration: 1ms !important;
    animation-duration: 1ms !important;
    scroll-behavior: auto !important;
  }
}
```

### 4.2 `resources/views/layouts/template.blade.php` — replace

```blade
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Timetable') · ITT626</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
  <a class="skip" href="#main">Skip to content</a>
  <div class="shell">
    <aside class="sidebar">
      <a class="brand" href="{{ route('home') }}">ITT626 <span>Timetable</span></a>
      <nav aria-label="Main">
        <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif @class(['active' => request()->routeIs('home')])>Dashboard</a>
        <a href="{{ route('timetables.index') }}" @if (request()->routeIs('timetables.*')) aria-current="page" @endif @class(['active' => request()->routeIs('timetables.*')])>Timetable</a>
        <a href="{{ route('subjects.index') }}" @if (request()->routeIs('subjects.*')) aria-current="page" @endif @class(['active' => request()->routeIs('subjects.*')])>Subjects</a>
        <a href="{{ route('halls.index') }}" @if (request()->routeIs('halls.*')) aria-current="page" @endif @class(['active' => request()->routeIs('halls.*')])>Halls</a>
        <a href="{{ route('days.index') }}" @if (request()->routeIs('days.*')) aria-current="page" @endif @class(['active' => request()->routeIs('days.*')])>Days</a>
        <a href="{{ route('groups.index') }}" @if (request()->routeIs('groups.*')) aria-current="page" @endif @class(['active' => request()->routeIs('groups.*')])>Groups</a>
        <a href="{{ route('students.index') }}" @if (request()->routeIs('students.*')) aria-current="page" @endif @class(['active' => request()->routeIs('students.*')])>Students</a>
      </nav>
      <form class="sidebar-foot" method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn" type="submit">Sign out</button>
      </form>
    </aside>

    <div class="main">
      <header class="topbar">
        <p class="mono">{{ now()->format('D · d M Y') }}</p>
        <p class="who">{{ auth()->user()->name ?? '' }}</p>
      </header>

      <main id="main">
        @if (session('success'))
          <div class="alert" role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="alert alert-error" role="alert">{{ session('error') }}</div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>
</body>
</html>
```

### 4.3 `resources/views/layouts/app.blade.php` — replace (auth shell)

```blade
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sign in') · ITT626</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth">
  <a class="skip" href="#main">Skip to content</a>
  <main id="main">
    @yield('content')
  </main>
</body>
</html>
```

### 4.4 `resources/views/auth/login.blade.php` — replace

```blade
@extends('layouts.app')

@section('content')
  <section class="auth-card">
    <p class="mono">ITT626 · Timetable</p>
    <h1>Sign in</h1>

    <form class="form" method="POST" action="{{ route('login') }}">
      @csrf

      <label class="field">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        @error('email') <em class="error">{{ $message }}</em> @enderror
      </label>

      <label class="field">
        <span>Password</span>
        <input type="password" name="password" required autocomplete="current-password">
        @error('password') <em class="error">{{ $message }}</em> @enderror
      </label>

      <div class="actions">
        <button class="btn btn-primary" type="submit">Sign in</button>
      </div>
    </form>
  </section>
@endsection
```

### 4.5 `resources/views/home.blade.php` — replace

```blade
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
```

### 4.6 `resources/views/timetables/index.blade.php` — new

```blade
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
              <form method="POST" action="{{ route('timetables.destroy', $entry) }}" onsubmit="return confirm('Delete this entry?')">
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
```

### 4.7 `resources/views/timetables/_form.blade.php` — new

```blade
@php($entry = $timetable ?? null)

<form class="panel form" method="POST" action="{{ $entry ? route('timetables.update', $entry) : route('timetables.store') }}">
  @csrf
  @if ($entry) @method('PUT') @endif

  <div class="form-grid">
    <label class="field">
      <span>Day <b aria-hidden="true">*</b></span>
      <select name="day_id" required>
        <option value="">Choose a day</option>
        @foreach ($days as $id => $name)
          <option value="{{ $id }}" @selected(old('day_id', $entry?->day_id) == $id)>{{ $name }}</option>
        @endforeach
      </select>
      @error('day_id') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>Subject <b aria-hidden="true">*</b></span>
      <select name="subject_id" required>
        <option value="">Choose a subject</option>
        @foreach ($subjects as $id => $name)
          <option value="{{ $id }}" @selected(old('subject_id', $entry?->subject_id) == $id)>{{ $name }}</option>
        @endforeach
      </select>
      @error('subject_id') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>Hall <b aria-hidden="true">*</b></span>
      <select name="hall_id" required>
        <option value="">Choose a hall</option>
        @foreach ($halls as $id => $name)
          <option value="{{ $id }}" @selected(old('hall_id', $entry?->hall_id) == $id)>{{ $name }}</option>
        @endforeach
      </select>
      @error('hall_id') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>Group</span>
      <select name="group_id">
        <option value="">No group</option>
        @foreach ($groups as $id => $name)
          <option value="{{ $id }}" @selected(old('group_id', $entry?->group_id) == $id)>{{ $name }}</option>
        @endforeach
      </select>
      @error('group_id') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>From <b aria-hidden="true">*</b></span>
      <input type="time" name="time_from" value="{{ old('time_from', $entry?->time_from) }}" required>
      @error('time_from') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>To <b aria-hidden="true">*</b></span>
      <input type="time" name="time_to" value="{{ old('time_to', $entry?->time_to) }}" required>
      @error('time_to') <em class="error">{{ $message }}</em> @enderror
    </label>
  </div>

  <div class="actions">
    <button class="btn btn-primary" type="submit">Save entry</button>
    <a class="btn btn-ghost" href="{{ route('timetables.index') }}">Cancel</a>
  </div>
</form>
```

### 4.8 `resources/views/timetables/create.blade.php` — new

```blade
@extends('layouts.template')

@section('title', 'New timetable entry')

@section('content')
  <div class="page-head">
    <div>
      <p class="mono">Timetable</p>
      <h1>New entry</h1>
    </div>
  </div>

  @include('timetables._form')
@endsection
```

### 4.9 `resources/views/timetables/edit.blade.php` — new

```blade
@extends('layouts.template')

@section('title', 'Edit timetable entry')

@section('content')
  <div class="page-head">
    <div>
      <p class="mono">Timetable</p>
      <h1>Edit entry</h1>
    </div>
  </div>

  @include('timetables._form')
@endsection
```

### 4.10 `resources/views/master/index.blade.php` — new

```blade
@extends('layouts.template')

@section('title', $title)

@section('content')
  <div class="page-head">
    <div>
      <p class="mono">Master data</p>
      <h1>{{ $title }}</h1>
    </div>
    <a class="btn btn-primary" href="{{ route($route . '.create') }}">New record</a>
  </div>

  <div class="panel table-wrap">
    <table>
      <thead>
        <tr>
          @foreach ($columns as $label)
            <th>{{ $label }}</th>
          @endforeach
          <th class="right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($rows as $row)
          <tr>
            @foreach ($columns as $key => $label)
              <td>{{ data_get($row, $key) ?? '—' }}</td>
            @endforeach
            <td>
              <div class="row-actions">
                <a class="btn btn-small" href="{{ route($route . '.edit', $row) }}">Edit</a>
                <form method="POST" action="{{ route($route . '.destroy', $row) }}" onsubmit="return confirm('Delete this record?')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-small btn-danger" type="submit">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td class="empty" colspan="{{ count($columns) + 1 }}">No records yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
@endsection
```

### 4.11 `resources/views/master/form.blade.php` — new

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

  <form class="panel form" method="POST" action="{{ $action }}">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="form-grid">
      @foreach ($fields as $field)
        <label class="field">
          <span>
            {{ $field['label'] }}
            @if ($field['required'] ?? false) <b aria-hidden="true">*</b> @endif
          </span>
          <input
            type="{{ $field['type'] ?? 'text' }}"
            name="{{ $field['name'] }}"
            value="{{ old($field['name'], $record?->{$field['name']} ?? '') }}"
            @if ($field['required'] ?? false) required @endif
          >
          @error($field['name']) <em class="error">{{ $message }}</em> @enderror
        </label>
      @endforeach
    </div>

    <div class="actions">
      <button class="btn btn-primary" type="submit">Save</button>
      <a class="btn btn-ghost" href="{{ route($route . '.index') }}">Cancel</a>
    </div>
  </form>
@endsection
```

### 4.12 Student views

`resources/views/students/index.blade.php` — replace:

```blade
@extends('layouts.template')

@section('title', 'Students')

@section('content')
  <div class="page-head">
    <div>
      <p class="mono">Students</p>
      <h1>Students</h1>
    </div>
    <a class="btn btn-primary" href="{{ route('students.create') }}">New student</a>
  </div>

  @include('students.table')
@endsection
```

`resources/views/students/table.blade.php` — replace:

```blade
<div class="panel table-wrap">
  <table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Joined</th>
        <th class="right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($students as $student)
        <tr>
          <td>{{ $student->name }}</td>
          <td>{{ $student->email }}</td>
          <td>{{ $student->phone_number ?? '—' }}</td>
          <td>{{ $student->created_at?->format('d M Y') ?? '—' }}</td>
          <td>
            <div class="row-actions">
              <a class="btn btn-small" href="{{ route('students.edit', $student) }}">Edit</a>
              <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Delete this student and login?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-small btn-danger" type="submit">Delete</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td class="empty" colspan="5">No students yet.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>
```

`resources/views/students/_form.blade.php` — new:

```blade
@php($studentRecord = $student ?? null)

<form class="panel form" method="POST" action="{{ $studentRecord ? route('students.update', $studentRecord) : route('students.store') }}">
  @csrf
  @if ($studentRecord) @method('PUT') @endif

  <div class="form-grid">
    <label class="field">
      <span>Name <b aria-hidden="true">*</b></span>
      <input name="name" value="{{ old('name', $studentRecord?->name) }}" required autocomplete="name">
      @error('name') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>Age</span>
      <input type="number" name="age" min="1" max="150" value="{{ old('age', $studentRecord?->age) }}">
      @error('age') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>Email <b aria-hidden="true">*</b></span>
      <input type="email" name="email" value="{{ old('email', $studentRecord?->email) }}" required autocomplete="email">
      @error('email') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>Phone</span>
      <input name="phone_number" value="{{ old('phone_number', $studentRecord?->phone_number) }}" autocomplete="tel">
      @error('phone_number') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>Password {{ $studentRecord ? '(leave blank to keep)' : '' }} @if (! $studentRecord) <b aria-hidden="true">*</b> @endif</span>
      <input type="password" name="password" @if (! $studentRecord) required @endif autocomplete="new-password">
      @error('password') <em class="error">{{ $message }}</em> @enderror
    </label>

    <label class="field">
      <span>Address</span>
      <textarea name="address">{{ old('address', $studentRecord?->address) }}</textarea>
      @error('address') <em class="error">{{ $message }}</em> @enderror
    </label>
  </div>

  <div class="actions">
    <button class="btn btn-primary" type="submit">Save student</button>
    <a class="btn btn-ghost" href="{{ route('students.index') }}">Cancel</a>
  </div>
</form>
```

`resources/views/students/create.blade.php` — replace:

```blade
@extends('layouts.template')

@section('title', 'New student')

@section('content')
  <div class="page-head">
    <div>
      <p class="mono">Students</p>
      <h1>New student</h1>
    </div>
  </div>

  @include('students._form')
@endsection
```

`resources/views/students/edit.blade.php` — replace:

```blade
@extends('layouts.template')

@section('title', 'Edit student')

@section('content')
  <div class="page-head">
    <div>
      <p class="mono">Students</p>
      <h1>Edit {{ $student->name }}</h1>
    </div>
  </div>

  @include('students._form')
@endsection
```

Delete `resources/views/students/show.blade.php`; the route no longer exists.

## 5. Test

**File:** `tests/Feature/TimetableTest.php` — new

```php
<?php

namespace Tests\Feature;

use App\Models\Day;
use App\Models\Group;
use App\Models\Hall;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TimetableTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::factory()->create(['password' => 'password']);
        $this->actingAs($user);

        return $user;
    }

    private function fields(array $overrides = []): array
    {
        return array_merge([
            'day_id' => Day::create(['day_name' => 'Monday'])->id,
            'subject_id' => Subject::create(['subject_code' => 'ITT626', 'subject_name' => 'Back-End'])->id,
            'hall_id' => Hall::create(['lecture_hall_name' => 'DK', 'lecture_hall_place' => 'Dewan Kuliah'])->id,
            'group_id' => Group::create(['name' => 'CS230', 'part' => '5'])->id,
            'time_from' => '10:00',
            'time_to' => '11:00',
        ], $overrides);
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get(route('timetables.index'))->assertRedirect(route('login'));
    }

    public function test_store_uses_the_authenticated_user_and_hashes_student_passwords(): void
    {
        $user = $this->actingUser();

        $this->post(route('timetables.store'), $this->fields())->assertRedirect(route('timetables.index'));
        $this->assertDatabaseHas('timetables', ['user_id' => $user->id, 'time_from' => '10:00']);

        $this->post(route('students.store'), [
            'name' => 'New Student',
            'email' => 'new@example.com',
            'password' => 'secret123',
        ])->assertRedirect(route('students.index'));

        $this->assertTrue(Hash::check('secret123', User::where('email', 'new@example.com')->firstOrFail()->password));
    }

    public function test_end_time_must_be_after_start_time(): void
    {
        $this->actingUser();

        $this->post(route('timetables.store'), $this->fields(['time_from' => '11:00', 'time_to' => '10:00']))
            ->assertSessionHasErrors('time_to');
    }

    public function test_hall_double_booking_is_rejected(): void
    {
        $this->actingUser();
        $data = $this->fields();

        $this->post(route('timetables.store'), $data)->assertRedirect(route('timetables.index'));

        $this->post(route('timetables.store'), $this->fields(['time_from' => '10:30', 'time_to' => '11:30']))
            ->assertSessionHasErrors('time_from');

        $this->assertSame(1, Timetable::count());
    }
}
```

## 6. Verification

```sh
cd C:/laragon/www/itt626
php artisan optimize:clear
php artisan migrate --seed
php artisan route:list
php artisan route:cache && php artisan route:clear
php artisan test
```

Manual checks:

- `/` redirects to login; guests cannot reach any app route.
- Login `ali@example.com` / `password` reaches the dashboard.
- Dashboard shows real counts and today's entries; empty state works with no entries.
- Timetable week board lists each day; create/edit/delete works.
- End time before start time is rejected; hall/group double-booking is rejected.
- Student create rejects duplicate email and short password; edit with blank password keeps the old login.
- Delete is blocked for self, referenced students, and referenced master rows.
- At 320/375/414/768 px: no page horizontal scroll, forms are one column, tables scroll only inside `.table-wrap`, buttons stay one line.
- Keyboard: skip link, visible focus ring, nav `aria-current`, flash messages announced.

## 7. Risks / skipped

- No roles: every authenticated user can manage all data. Add a policy if the app leaves the classroom.
- Deleting a student deletes their login; deletion is blocked while timetable entries exist.
- Timetable clash check is application-level, not a DB constraint (see `shortcut:` in controller).
- `time_from` / `time_to` remain strings in the schema; validation enforces `H:i`.
- Auth password-reset/verify views keep their old Bootstrap markup; they still render but are not redesigned. Only login is in the primary flow.
- No dashboard charts, exports, drag-and-drop, or calendar integration — not required by the brief.
