@extends('layouts.app')

@section('title', 'Sign in')

@section('content')
  <section class="auth-card">
    <p class="mono">ITT626 · Timetable</p>
    <h1>Sign in</h1>

    <form class="form" method="POST" action="{{ route('login') }}">
      @csrf

      <label class="field">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        <em class="error">{{ $errors->first('email') }}</em>
      </label>

      <label class="field">
        <span>Password</span>
        <input type="password" name="password" required autocomplete="current-password">
        <em class="error">{{ $errors->first('password') }}</em>
      </label>

      <div class="actions">
        <button class="btn btn-primary" type="submit">Sign in</button>
      </div>
    </form>

    <p class="auth-foot">
      <a href="{{ route('password.request') }}">Forgot password?</a>
      <a href="{{ route('register') }}">Create account</a>
    </p>
  </section>
@endsection
