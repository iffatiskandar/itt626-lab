@extends('layouts.template')

@section('title', $title)

@section('content')
    <div class="page-head">
        <div>
            <p class="mono">Master data</p>
            <h1>{{ $title }}</h1>
        </div>
        <a class="btn btn-primary" href="{{ route($route . '.create') }}">New record</a>
    </div>

    <div class="panel table-wrap">
        <table>
            <thead>
                <tr>
                    @foreach ($columns as $label)
                        <th scope="col">{{ $label }}</th>
                    @endforeach
                    <th scope="col" class="right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        @foreach ($columns as $key => $label)
                            <td>{{ data_get($row, $key) ?? '—' }}</td>
                        @endforeach
                        <td>
                            <div class="row-actions">
                                <a class="btn btn-small" href="{{ route($route . '.edit', $row) }}">Edit</a>
                                <form method="POST" action="{{ route($route . '.destroy', $row) }}"
                                    onsubmit="return confirm('Delete this record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-small btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td class="empty" colspan="{{ count($columns) + 1 }}">No records yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection