<form class="panel form" method="POST"
    action="{{ $entry ? route('timetables.update', $entry) : route('timetables.store') }}">
    @csrf
    @if ($entry) @method('PUT') @endif

    <div class="form-grid">
        <label class="field">
            <span>Day <b aria-hidden="true">*</b></span>
            <select name="day_id" required>
                <option value="">Choose a day</option>
                @foreach ($days as $id => $name)
                    <option value="{{ $id }}" @selected(old('day_id', $entry?->day_id) == $id)>{{ $name }}</option>
                @endforeach
            </select>
            <em class="error">{{ $errors->first('day_id') }}</em>
        </label>

        <label class="field">
            <span>Subject <b aria-hidden="true">*</b></span>
            <select name="subject_id" required>
                <option value="">Choose a subject</option>
                @foreach ($subjects as $id => $name)
                    <option value="{{ $id }}" @selected(old('subject_id', $entry?->subject_id) == $id)>{{ $name }}</option>
                @endforeach
            </select>
            <em class="error">{{ $errors->first('subject_id') }}</em>
        </label>

        <label class="field">
            <span>Hall <b aria-hidden="true">*</b></span>
            <select name="hall_id" required>
                <option value="">Choose a hall</option>
                @foreach ($halls as $id => $name)
                    <option value="{{ $id }}" @selected(old('hall_id', $entry?->hall_id) == $id)>{{ $name }}</option>
                @endforeach
            </select>
            <em class="error">{{ $errors->first('hall_id') }}</em>
        </label>

        <label class="field">
            <span>Group</span>
            <select name="group_id">
                <option value="">No group</option>
                @foreach ($groups as $id => $name)
                    <option value="{{ $id }}" @selected(old('group_id', $entry?->group_id) == $id)>{{ $name }}</option>
                @endforeach
            </select>
            <em class="error">{{ $errors->first('group_id') }}</em>
        </label>

        <label class="field">
            <span>From <b aria-hidden="true">*</b></span>
            <input type="time" name="time_from" value="{{ old('time_from', $entry?->time_from) }}" required>
            <em class="error">{{ $errors->first('time_from') }}</em>
        </label>

        <label class="field">
            <span>To <b aria-hidden="true">*</b></span>
            <input type="time" name="time_to" value="{{ old('time_to', $entry?->time_to) }}" required>
            <em class="error">{{ $errors->first('time_to') }}</em>
        </label>
    </div>

    <div class="actions">
        <button class="btn btn-primary" type="submit">Save entry</button>
        <a class="btn btn-ghost" href="{{ route('timetables.index') }}">Cancel</a>
    </div>
</form>
