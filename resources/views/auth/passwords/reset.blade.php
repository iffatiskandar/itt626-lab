@extends('layouts.app')

@section('title', 'Reset password')

@section('content')
  <section class="auth-card">
    <p class="mono">ITT626 · Timetable</p>
    <h1>Reset password</h1>
    <p class="auth-lede">Choose a new password for {{ $email ?? old('email') }}.</p>

    <form class="form" method="POST" action="{{ route('password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <label class="field">
        <span>Email</span>
        <input type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
        <em class="error">{{ $errors->first('email') }}</em>
      </label>

      <label class="field">
        <span>New password</span>
        <input type="password" name="password" required autocomplete="new-password">
        <em class="error">{{ $errors->first('password') }}</em>
      </label>

      <label class="field">
        <span>Confirm new password</span>
        <input type="password" name="password_confirmation" required autocomplete="new-password">
        <em class="error"></em>
      </label>

      <div class="actions">
        <button class="btn btn-primary" type="submit">Reset password</button>
      </div>
    </form>

    <p class="auth-foot">
      <a href="{{ route('login') }}">Back to sign in</a>
    </p>
  </section>
@endsection