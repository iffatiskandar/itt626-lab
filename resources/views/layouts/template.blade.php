<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Timetable') · ITT626</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
  <a class="skip" href="#main">Skip to content</a>
  <div class="shell">
    <aside class="sidebar">
      <a class="brand" href="{{ route('home') }}">ITT626 <span>Timetable</span></a>
      <nav aria-label="Main">
        <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif @class(['active' => request()->routeIs('home')])>Dashboard</a>
        <a href="{{ route('timetables.index') }}" @if (request()->routeIs('timetables.*')) aria-current="page" @endif
          @class(['active' => request()->routeIs('timetables.*')])>Timetable</a>
        <a href="{{ route('subjects.index') }}" @if (request()->routeIs('subjects.*')) aria-current="page" @endif
          @class(['active' => request()->routeIs('subjects.*')])>Subjects</a>
        <a href="{{ route('halls.index') }}" @if (request()->routeIs('halls.*')) aria-current="page" @endif
          @class(['active' => request()->routeIs('halls.*')])>Halls</a>
        <a href="{{ route('days.index') }}" @if (request()->routeIs('days.*')) aria-current="page" @endif
          @class(['active' => request()->routeIs('days.*')])>Days</a>
        <a href="{{ route('groups.index') }}" @if (request()->routeIs('groups.*')) aria-current="page" @endif
          @class(['active' => request()->routeIs('groups.*')])>Groups</a>
        <a href="{{ route('students.index') }}" @if (request()->routeIs('students.*')) aria-current="page" @endif
          @class(['active' => request()->routeIs('students.*')])>Students</a>
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