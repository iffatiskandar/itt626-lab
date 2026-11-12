<form class="panel form" method="POST"
    action="{{ $studentRecord ? route('students.update', $studentRecord) : route('students.store') }}">
    @csrf
    @if ($studentRecord) @method('PUT') @endif

    <div class="form-grid">
        <label class="field">
            <span>Name <b aria-hidden="true">*</b></span>
            <input name="name" value="{{ old('name', $studentRecord?->name) }}" required autocomplete="name">
            @error('name') <em class="error">{{ $message }}</em> @enderror
        </label>

        <label class="field">
            <span>Age</span>
            <input type="number" name="age" min="1" max="150" value="{{ old('age', $studentRecord?->age) }}">
            @error('age') <em class="error">{{ $message }}</em> @enderror
        </label>

        <label class="field">
            <span>Email <b aria-hidden="true">*</b></span>
            <input type="email" name="email" value="{{ old('email', $studentRecord?->email) }}" required
                autocomplete="email">
            @error('email') <em class="error">{{ $message }}</em> @enderror
        </label>

        <label class="field">
            <span>Phone</span>
            <input name="phone_number" value="{{ old('phone_number', $studentRecord?->phone_number) }}"
                autocomplete="tel">
            @error('phone_number') <em class="error">{{ $message }}</em> @enderror
        </label>

        <label class="field">
            <span>Password {{ $studentRecord ? '(leave blank to keep)' : '' }} @if (!$studentRecord) <b
            aria-hidden="true">*</b> @endif</span>
            <input type="password" name="password" @if (!$studentRecord) required @endif autocomplete="new-password">
            @error('password') <em class="error">{{ $message }}</em> @enderror
        </label>

        <label class="field">
            <span>Address</span>
            <textarea name="address">{{ old('address', $studentRecord?->address) }}</textarea>
            @error('address') <em class="error">{{ $message }}</em> @enderror
        </label>
    </div>

    <div class="actions">
        <button class="btn btn-primary" type="submit">Save student</button>
        <a class="btn btn-ghost" href="{{ route('students.index') }}">Cancel</a>
    </div>
</form>