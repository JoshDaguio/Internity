@extends('layouts.app')

@section('body')
    <h1>Pending Student Registrations</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID Number</th>
                <th>Last Name</th>
                <th>First Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pendingRegistrations as $registration)
                <tr>
                    <td>{{ $registration->profile->id_number }}</td>
                    <td>{{ $registration->profile->last_name }}</td>
                    <td>{{ $registration->profile->first_name }}</td>
                    <td>{{ $registration->email }}</td>
                    <td>{{ $registration->course->course_name }}</td>
                    <td>
                        <form action="{{ route('registrations.approve', $registration->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">Approve</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
