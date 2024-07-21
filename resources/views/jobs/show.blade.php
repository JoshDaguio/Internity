@extends('layouts.app')

@section('body')
    <h1>Job Details</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Field</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Title:</strong></td>
                <td>{{ $job->title }}</td>
            </tr>
            <tr>
                <td><strong>Industry:</strong></td>
                <td>{{ $job->industry }}</td>
            </tr>
            <tr>
                <td><strong>Positions Available:</strong></td>
                <td>{{ $job->positions_available }}</td>
            </tr>
            <tr>
                <td><strong>Location:</strong></td>
                <td>{{ $job->location }}</td>
            </tr>
            <tr>
                <td><strong>Work Type:</strong></td>
                <td>{{ $job->work_type }}</td>
            </tr>
            <tr>
                <td><strong>Schedule:</strong></td>
                <td>{{ $job->schedule }}</td>
            </tr>
            <tr>
                <td><strong>Description:</strong></td>
                <td>{{ $job->description }}</td>
            </tr>
            <tr>
                <td><strong>Qualification:</strong></td>
                <td>{{ $job->qualification }}</td>
            </tr>
            <tr>
                <td><strong>Preferred Skills:</strong></td>
                <td>{{ $job->preferred_skills }}</td>
            </tr>
        </tbody>
    </table>

    <a href="{{ route('jobs.edit', $job) }}" class="btn btn-primary">Edit</a>

    <form action="{{ route('jobs.destroy', $job) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>

    <a href="{{ route('jobs.index') }}" class="btn btn-secondary">Back to List</a>
@endsection
