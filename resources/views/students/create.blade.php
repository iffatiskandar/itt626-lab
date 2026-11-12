@extends('layouts.template')

@section('title', 'New student')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Students</p>
            <h1>New student</h1>
        </div>
    </div>

    @include('students._form', ['studentRecord' => null])
@endsection