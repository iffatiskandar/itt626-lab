<div class="panel table-wrap">
    <table>
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Joined</th>
                <th scope="col" class="right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
                <tr>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->phone_number ?? '—' }}</td>
                    <td>{{ $student->created_at?->format('d M Y') ?? '—' }}</td>
                    <td>
                        <div class="row-actions">
                            <a class="btn btn-small" href="{{ route('students.edit', $student) }}">Edit</a>
                            <form method="POST" action="{{ route('students.destroy', $student) }}"
                                onsubmit="return confirm('Delete this student and login?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-small btn-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="empty" colspan="5">No students yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>