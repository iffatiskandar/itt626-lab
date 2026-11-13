@extends('layouts.app')

@section('title', 'Confirm password')

@section('content')
  <section class="auth-card">
    <p class="mono">ITT626 · Timetable</p>
    <h1>Confirm password</h1>
    <p class="auth-lede">Please confirm your password before continuing.</p>

    <form class="form" method="POST" action="{{ route('password.confirm') }}">
      @csrf

      <label class="field">
        <span>Password</span>
        <input type="password" name="password" required autocomplete="current-password" autofocus>
        <em class="error">{{ $errors->first('password') }}</em>
      </label>

      <div class="actions">
        <button class="btn btn-primary" type="submit">Confirm password</button>
      </div>
    </form>

    <p class="auth-foot">
      <a href="{{ route('password.request') }}">Forgot password?</a>
      <a href="{{ route('login') }}">Back to sign in</a>
    </p>
  </section>
@endsection