@extends('layouts.app')

@section('title', 'Register')

@section('content')
  <section class="auth-card auth-card-wide">
    <p class="mono">ITT626 · Timetable</p>
    <h1>Register</h1>

    <form class="form" method="POST" action="{{ route('register') }}">
      @csrf

      <div class="form-grid">
        <label class="field">
          <span>Name</span>
          <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
          <em class="error">{{ $errors->first('name') }}</em>
        </label>

        <label class="field">
          <span>Age</span>
          <input type="number" name="age" value="{{ old('age') }}" autocomplete="age">
          <em class="error">{{ $errors->first('age') }}</em>
        </label>

        <label class="field">
          <span>Email</span>
          <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
          <em class="error">{{ $errors->first('email') }}</em>
        </label>

        <label class="field">
          <span>Phone number</span>
          <input type="tel" name="phone_number" value="{{ old('phone_number') }}" autocomplete="tel">
          <em class="error">{{ $errors->first('phone_number') }}</em>
        </label>

        <label class="field">
          <span>Password</span>
          <input type="password" name="password" required autocomplete="new-password">
          <em class="error">{{ $errors->first('password') }}</em>
        </label>

        <label class="field">
          <span>Confirm password</span>
          <input type="password" name="password_confirmation" required autocomplete="new-password">
          <em class="error"></em>
        </label>

        <label class="field span-all">
          <span>Address</span>
          <input type="text" name="address" value="{{ old('address') }}" autocomplete="street-address">
          <em class="error">{{ $errors->first('address') }}</em>
        </label>
      </div>

      <div class="actions">
        <button class="btn btn-primary" type="submit">Register</button>
      </div>
    </form>

    <p class="auth-foot">
      <a href="{{ route('login') }}">Already have an account? Sign in</a>
    </p>
  </section>
@endsection