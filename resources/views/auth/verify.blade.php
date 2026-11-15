@extends('layouts.app')

@section('title', 'Verify email')

@section('content')
  <section class="auth-card">
    <p class="mono">ITT626 · Timetable</p>
    <h1>Verify your email</h1>

    <p class="auth-lede">Email verification is not enabled on this installation, so there is nothing to confirm.</p>

    <p class="auth-foot">
      <a href="{{ route('login') }}">Back to sign in</a>
    </p>
  </section>
@endsection