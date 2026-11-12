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
