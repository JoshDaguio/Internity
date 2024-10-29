@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Accepted Interns</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Internships</li>
            <li class="breadcrumb-item active">Interns</li>
        </ol>
    </nav>
</div>

<!-- Accepted Interns List -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Accepted Interns</h5>
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Intern</th>
                        <th>Job</th>
                        <th>Email</th>
                        <th>Start Date</th>
                        <th>Est. Finish Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($acceptedInterns as $intern)
                    <tr>
                        <td>
                            <a href="{{ route('students.show', $intern->student->id) }}" class="btn btn-light btn-sm">
                                {{ $intern->student->profile->last_name }}, {{ $intern->student->profile->first_name }}
                            </a>
                        </td>
                        <td>{{ $intern->job->title }}</td>
                        <td>{{ $intern->student->email }}</td>
                        <td><span class="badge bg-success">{{ \Carbon\Carbon::parse($intern->start_date)->format('M d, Y') }}</span></td>
                        <td><span class="badge bg-primary">{{ \Carbon\Carbon::parse($intern->estimatedFinishDate)->format('M d, Y') }}</span></td> <!-- Display Est. Finish Date -->
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
