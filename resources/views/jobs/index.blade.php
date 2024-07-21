@extends('layouts.app')

@section('body')
    <h1>Job Listings</h1>
    <a href="{{ route('jobs.create') }}" class="btn btn-primary mb-3">Create New Job</a>

    <table class="table">
        <thead>
            <tr>
                <th>Job Title</th>
                <th>Industry</th>
                <th>Positions Available</th>
                <th>Location</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($jobs as $job)
                <tr>
                    <td>{{ $job->title }}</td>
                    <td>{{ $job->industry }}</td>
                    <td>{{ $job->positions_available }}</td>
                    <td>{{ $job->location }}</td>
                    <td>
                        <a href="{{ route('jobs.show', $job) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('jobs.edit', $job) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('jobs.destroy', $job) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
