@extends('layouts.template')

@section('title', 'Student details')

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Students</p>
            <h1>{{ $student->name }}</h1>
        </div>
        <a class="btn btn-ghost" href="{{ route('students.index') }}">Back</a>
    </div>

    <div class="panel form">
        <div class="form-grid">
            <div class="field"><span>Name</span>
                <p>{{ $student->name }}</p>
            </div>
            <div class="field"><span>Email</span>
                <p>{{ $student->email }}</p>
            </div>
            <div class="field"><span>Age</span>
                <p>{{ $student->age ?? '-' }}</p>
            </div>
            <div class="field"><span>Phone</span>
                <p>{{ $student->phone_number ?? '-' }}</p>
            </div>
            <div class="field"><span>Address</span>
                <p>{{ $student->address ?? '-' }}</p>
            </div>
            <div class="field"><span>Joined</span>
                <p>{{ $student->created_at?->format('d M Y') ?? '-' }}</p>
            </div>
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="{{ route('students.edit', $student) }}">Edit</a>
            <a class="btn btn-ghost" href="{{ route('students.index') }}">Back</a>
        </div>
    </div>
@endsection