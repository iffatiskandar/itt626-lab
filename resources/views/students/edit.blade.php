@extends('layouts.template')

@section('title', 'Edit student')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Students</p>
            <h1>Edit {{ $student->name }}</h1>
        </div>
    </div>

    @include('students._form', ['studentRecord' => $student])
@endsection