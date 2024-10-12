@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Job Listings</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Job Listings</li>
        </ol>
    </nav>
</div>

   <!-- Success and Error Messages -->
   @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

<a href="{{ route('admin.jobs.create') }}" class="btn btn-primary mb-3">Add Job</a>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Jobs List</h5>
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Work Type</th>
                        <th>Applicants</th>
                        <th>Interns</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                    <tr>
                        <td>
                            <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-light btn-sm">
                                {{ $job->title }}
                            </a>
                        </td>
                        <td>{{ $job->company->name }}</td>
                        <td>{{ $job->work_type }}</td>
                        <td>{{ $job->applications->count() }}</td>
                        <td>{{ $job->applications->where('status_id', 2)->count() }}</td> <!-- Number of accepted interns (status_id 2 for 'Accepted') -->
                        <td>
                            <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
