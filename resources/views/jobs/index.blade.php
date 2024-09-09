@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Job Listings</h1>
    <a href="{{ route('jobs.create') }}" class="btn btn-primary mb-3">Add Job</a>

    <div class="row">
        <!-- Job Listings Table -->
        <div class="col-md-8">
            <table class="table">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Industry</th>
                        <th>Available</th>
                        <th>Work Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                    <tr>
                        <td>{{ $job->title }}</td>
                        <td>{{ $job->industry }}</td>
                        <td>{{ $job->positions_available }}</td>
                        <td>{{ $job->work_type }}</td>
                        <td>
                            <a href="{{ route('jobs.show', $job) }}" class="btn btn-info btn-sm"><i class="bi bi-info-circle"></i></a>
                            <a href="{{ route('jobs.edit', $job) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                            <form action="{{ route('jobs.destroy', $job) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Progress Bar Section -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Active Internships</h5>

                    <!-- Job list container with fixed height and overflow -->
                    <div class="job-list" style="max-height: 200px; overflow-y: auto;">
                        @foreach ($jobs as $job)
                            @php
                                $percentage = ($job->positions_available / $totalPositions) * 100;
                            @endphp
                            <p>{{ $job->title }} ({{ $job->positions_available }})</p>
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%; background-color: #B30600;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ round($percentage, 2) }}%
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
