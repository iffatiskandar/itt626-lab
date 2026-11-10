@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>List of Students</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('students.create') }}"> Add New Student</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Email</th>
            <th>Joined On</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($students as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->name }}</td>
                <td>{{ $s->email }}</td>
                <td>{{ $s->created_at }}</td>
                <td>
                    <form action="{{ route('students.destroy', $s->id) }}" method="POST">

                        <a class="btn btn-info" href="{{ route('students.show', $s->id) }}">Show</a>

                        <a class="btn btn-primary" href="{{ route('students.edit', $s->id) }}">Edit</a>

                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Student Timetable</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-success" href="{{ route('timetables.create') }}"> Add New Timetable</a>
            </div>
        </div>
    </div>
@if ($message = Session::get('success'))
    \<div class="alert alert-success">
        \<p>{{ $message }}\</p>
    \</div>
@endif

\<table class="table table-bordered">
    \<tr>
        \<th>No\</th>
        \<th>Day\</th>
        \<th>Subject\</th>
        \<th>Hall\</th>
        \<th>Time From\</th>
        \<th>Time To\</th>
        \<th width="280px">Action\</th>
    \</tr>


    @foreach ($timetables as $t)
    \<tr>
        \<td>{{ $t->id }}\</td>
        \<td>{{ $t->day->day_name}}\</td>
        \<td>{{ $t->subject->subject_code }} - {{ $t->subject->subject_name  }}\</td>
        \<td>{{ $t->hall->lecture_hall_name}} \</td>
        \<td>{{ $t->time_from }}\</td>
        \<td>{{ $t->time_to }} \</td>
        \<td>
            \<form action="{{ route('timetables.destroy',$t->id) }}" method="POST">

            \<a class="btn btn-info" href="{{ route('timetables.show',$t->id) }}">Show\</a>

                \<a class="btn btn-primary" href="{{ route('timetables.edit',$t->id) }}">Edit\</a>

                @csrf
                @method('DELETE')
  
                \<button type="submit" class="btn btn-danger">Delete\</button>
            \</form>
        \</td>
    \</tr>
    @endforeach
\</table>