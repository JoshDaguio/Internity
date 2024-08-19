@extends('layouts.app')

@section('body')
    <h1>Approved Students</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
            </tr>
        </thead>
        <tbody>
            @foreach($approvedStudents as $student)
                <tr>
                    <td>{{ $student->profile ? $student->profile->id_number : 'N/A' }}</td>
                    <td>{{ $student->profile ? $student->profile->last_name : 'N/A' }}, {{ $student->profile ? $student->profile->first_name : 'N/A' }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->course ? $student->course->course_name : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
