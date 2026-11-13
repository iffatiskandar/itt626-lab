@extends('layouts.app')

@section('title', 'Verify email')

@section('content')
  <section class="auth-card">
    <p class="mono">ITT626 · Timetable</p>
    <h1>Verify your email</h1>

    @if (session('resent'))
      <div class="alert" role="status">A fresh verification link has been sent to your email address.</div>
    @endif

    <p class="auth-lede">Check your email for a verification link. If you did not receive it, request another below.</p>

    <form class="form" method="POST" action="{{ route('verification.resend') }}">
      @csrf
      <div class="actions">
        <button class="btn" type="submit">Send a new link</button>
      </div>
    </form>

    <p class="auth-foot">
      <a href="{{ route('home') }}">Continue to dashboard</a>
    </p>
  </section>
@endsection