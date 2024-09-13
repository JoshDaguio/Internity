@extends('layouts.app')

@section('body')


    <div class="pagetitle">
        <h1>Internship Hours</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Internship Hours</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <a href="{{ route('internship_hours.create') }}" class="btn btn-primary mb-3">Add Internship Hours</a>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Skill Tags List</h5>
            <table class="table datatable">
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
    </div>
@endsection
