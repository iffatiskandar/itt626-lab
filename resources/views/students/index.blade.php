@extends('layouts.template')

@section('title', 'Students')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Students</p>
            <h1>Students</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('students.create') }}">New student</a>
    </div>

    @include('students.table')
@endsection