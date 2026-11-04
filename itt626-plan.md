# ITT626 — Development Plan

Project: `C:\laragon\www\itt626` (Laravel 13.35, PHP 8.3, AdminLTE 3 in `public/admin`).
Baseline: commit `00c5bed merge origin/main` (same as `origin/main`).

## 1. State at baseline

Committed:

- `routes/web.php`: only `/`, `Auth::routes()`, `/home`. No student or resource routes.
- `layouts/template.blade.php`: raw AdminLTE demo. No `@yield('content')`.
- `HomeController` plus empty CRUD stubs: Subject, Hall, Day, Group, Timetable.
- Models: User, Subject, Hall, Day, Group, Timetable.
- Migrations for all tables; seeders for users, days, groups, subjects, halls.

Working tree, not committed:

- Modified: `routes/web.php`, `layouts/template.blade.php`.
- Deleted: `layouts/templatetest.blade.php`.
- Untracked: `CHANGES.md`, `StudentController.php`, `resources/views/students/`.
- `students/edit.blade.php` and `students/show.blade.php` are empty.

## 2. Defects to clear first

1. `php artisan route:list` fails with `ReflectionException Class "StudentController" does not exist`.
   Cause: `Route::resource('students', 'StudentController')` string syntax at the bottom of `routes/web.php`.
2. Duplicate registrations: `/home` twice; students, subjects, halls, groups, timetables each defined twice.
3. `StudentController` imports `App\Http\Controllers\User`; correct class is `App\Models\User`.
4. `StudentController` implements only `index` and `create`.
5. `students/table.blade.php` declares `@section('content')` although it is included inside a section. Result: blank page.
6. `students/create.blade.php` contains literal `\</div>` escapes. Invalid HTML.
7. `template.blade.php` no longer has `<div class="content-wrapper">`; footer remains. Broken layout.
8. `template.blade.php` still has `Alexander Pierce`, AdminLTE brand, demo sidebar, no logout.
9. MySQL is down; SQLite has only the three base migrations, `users` empty.
10. `DaySeeder` has `Thurday`.
11. Routes point to empty `edit`/`show` views.

## 3. Phase 0 — Environment (blocking)

- Start Laragon MySQL, or set in `.env`:

  ```dotenv
  DB_CONNECTION=sqlite
  DB_DATABASE=C:/laragon/www/itt626/database/database.sqlite
  ```

- Run:

  ```sh
  php artisan config:clear
  php artisan migrate --seed
  php artisan route:list
  ```

- Expected login: `ali@example.com` / `password`.
- Do not start CRUD work until `migrate --seed` and `route:list` succeed.

## 4. Phase 1 — Routes

`routes/web.php`:

- Keep one `/`, `Auth::routes()`, one authenticated group.
- Put `/home` inside the auth group.
- Keep explicit student routes (no `Route::resource('students', ...)`).
- Use class syntax for the other five resources.
- No leading slash on resource names. No URL inside route names.

Shape:

```php
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    Route::resource('subjects', SubjectController::class);
    Route::resource('halls', HallController::class);
    Route::resource('days', DayController::class);
    Route::resource('groups', GroupController::class);
    Route::resource('timetables', TimetableController::class);
});
```

Delete the duplicate block at the bottom of the file.

Gate: `php artisan route:list` exits 0, each URI appears once.

## 5. Phase 2 — Layout

`resources/views/layouts/template.blade.php`:

- Restore content area after `</aside>`:

  ```blade
  <div class="content-wrapper">
    <section class="content">
      <div class="container-fluid">
        @yield('content')
      </div>
    </section>
  </div>
  ```

- Remove `@hasSection ... @else <!-- existing demo content --> @endif`. Keep one yield only.
- Title and brand: project name.
- Sidebar user: `{{ auth()->user()->name ?? 'Guest' }}`.
- Remove navbar search, messages, notifications.
- Add POST logout form to `route('logout')` with `@csrf`.
- Sidebar items only: Dashboard, Students, Subjects, Halls, Days, Groups, Timetables.
- Remove demo links (`index*.html`, `pages/*.html`), widgets, layout options, labels, multi-level items.
- Footer: project name + `{{ date('Y') }}`.
- Keep jQuery, Bootstrap bundle, AdminLTE app, overlayScrollbars. Remove chart/map/calendar/demo scripts.

## 6. Phase 3 — Student CRUD (reference implementation)

Controller rules:

- `use App\Models\User;`
- Seven methods; `User $student` route-model binding on show/edit/update/destroy.
- Store: validate `name`, `email` (`email|unique:users,email`), `password` (`min:8`). `User::create($validated)`.
- Update: same rules; email unique ignoring current id; `password` nullable; unset when blank so login is not overwritten.
- Destroy: refuse when `$student->id === auth()->id()`.
- Password hashed by `hashed` cast. Do not use `DB::table('users')->insert(...)`; it stores plaintext and skips timestamps.
- Redirect with `success` flash.

Views:

- `index.blade.php`: extends layout, one `@section('content')`, includes `students.table`.
- `table.blade.php`: table only, no `@section`/`@endsection`. Columns No, Name, Email, Joined On, Action. Delete form: `@csrf`, `@method('DELETE')`, confirm dialog.
- `create.blade.php`: name, email, password, `$errors`, no backslashes.
- `edit.blade.php`: prefilled email, password placeholder "Leave blank to keep current password".
- `show.blade.php`: read-only name, email, joined date, back button.
- Route calls: `route('students.show', $s)`; never a URL inside `route()`.

## 7. Phase 4 — Remaining CRUD

Pattern identical to Students. Fields from migrations:

- Subject: `subject_code`, `subject_name`, `lecturer_name`.
- Hall: `lecture_hall_name`, `lecture_hall_place`.
- Day: `day_name`.
- Group: `name`, `part`.
- Timetable: `user_id`, `subject_id`, `day_id`, `hall_id`, `group_id`, `time_from`, `time_to`.

Views per class: `index`, `table`, `create`, `edit`, `show`.

## 8. Phase 5 — Timetable and dashboard

Timetable:

- Add `belongsTo` relations: `user`, `subject`, `day`, `hall`, `group`.
- Validate all five ids with `exists`, `time_from`/`time_to` as `date_format:H:i`, `time_to` after `time_from`.
- Create/edit pass dropdown data.
- Index eager-loads relations and shows names, not IDs.

Dashboard:

- `HomeController` counts users, subjects, halls, days, groups, timetables.
- Add today's timetable with `now()->dayOfWeekIso`.
- `home.blade.php`: count boxes + today's table + empty state.
- Fix `DaySeeder` `Thurday` -> `Thursday`.

## 9. Phase 6 — Commit and hygiene

- Resolve `CHANGES.md`: commit as history or delete. It duplicates this plan.
- Commit routes + layout first, then one commit per CRUD class.
- Never commit `.env`.
- Do not commit empty `edit`/`show` views.

## 10. Verification

```sh
php artisan route:list
php artisan test
```

Manual:

- `/home` renders inside layout, not demo dashboard.
- Sidebar has 7 items; logout works; logged-in name shows.
- Student create rejects duplicate email and short password.
- Student edit with blank password keeps login working.
- Delete confirms; self-delete blocked.
- Subject, hall, day, group CRUD works.
- Timetable dropdowns work; end time before start time rejected.
- Dashboard works with zero timetable rows.
- Sidebar collapse/treeview still works.

## 11. Risks

- Deleting a student deletes that login account.
- No roles: every authenticated user can manage every student.
- Timetable foreign keys block deleting referenced master rows.
- `time_from`/`time_to` are strings in schema.
- Two layouts: `template` (Bootstrap 4) and `app` (Bootstrap 5). Use `template` only.
- Stub controllers return `null`, so their routes 500 until Phase 4.
