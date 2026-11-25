@extends('layouts.template')

@section('title', $title)

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Master data</p>
            <h1>{{ $title }}</h1>
        </div>
        <a class="btn btn-ghost" href="{{ route($route . '.index') }}">Back</a>
    </div>

    <div class="panel form">
        <div class="form-grid">
            @foreach ($columns as $key => $label)
                <div class="field">
                    <span>{{ $label }}</span>
                    <p>{{ data_get($record, $key) ?? '-' }}</p>
                </div>
            @endforeach
        </div>

        <div class="actions">
            <a class="btn btn-primary" href="{{ route($route . '.edit', $record) }}">Edit</a>
            <a class="btn btn-ghost" href="{{ route($route . '.index') }}">Back</a>
        </div>
    </div>
@endsection