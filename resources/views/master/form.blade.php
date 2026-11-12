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

    <form class="panel form" method="POST" action="{{ $action }}">
        @csrf
        @if ($method !== 'POST') @method($method) @endif

        <div class="form-grid">
            @foreach ($fields as $field)
                <label class="field">
                    <span>
                        {{ $field['label'] }}
                        @if ($field['required'] ?? false) <b aria-hidden="true">*</b> @endif
                    </span>
                    <input type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}"
                        value="{{ old($field['name'], $record?->{$field['name']} ?? '') }}" @if ($field['required'] ?? false)
                        required @endif>
                    @error($field['name']) <em class="error">{{ $message }}</em> @enderror
                </label>
            @endforeach
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit">Save</button>
            <a class="btn btn-ghost" href="{{ route($route . '.index') }}">Cancel</a>
        </div>
    </form>
@endsection