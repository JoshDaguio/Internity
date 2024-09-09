@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Internship Hours</h1>
    <a href="{{ route('internship_hours.create') }}" class="btn btn-primary mb-3">Add Internship Hours</a>
    <table class="table">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Hours</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($internshipHours as $internshipHour)
                <tr>
                    <td>{{ $internshipHour->course->course_code }}</td>
                    <td>{{ $internshipHour->hours }}</td>
                    <td>
                        <a href="{{ route('internship_hours.edit', $internshipHour->id) }}" class="btn btn-warning"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('internship_hours.destroy', $internshipHour->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
