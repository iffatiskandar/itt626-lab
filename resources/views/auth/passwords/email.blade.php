@extends('layouts.app')

@section('title', 'Forgot password')

@section('content')
  <section class="auth-card">
    <p class="mono">ITT626 · Timetable</p>
    <h1>Forgot password</h1>
    <p class="auth-lede">Enter the email on your account and we will send a reset link.</p>

    @if (session('status'))
      <div class="alert" role="status">{{ session('status') }}</div>
    @endif

    <form class="form" method="POST" action="{{ route('password.email') }}">
      @csrf

      <label class="field">
        <span>Email</span>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
        <em class="error">{{ $errors->first('email') }}</em>
      </label>

      <div class="actions">
        <button class="btn btn-primary" type="submit">Send reset link</button>
      </div>
    </form>

    <p class="auth-foot">
      <a href="{{ route('login') }}">Back to sign in</a>
    </p>
  </section>
@endsection