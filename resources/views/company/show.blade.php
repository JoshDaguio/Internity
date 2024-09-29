@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>{{ $company->name }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Company</li>
                <li class="breadcrumb-item active">{{ $company->name }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->
    <a href="{{ route('company.index') }}" class="btn btn-secondary mb-3">Back to List</a>


    <table class="table table-bordered">
        <tr>
            <th>Company Name:</th>
            <td>{{ $company->name }}</td>
        </tr>
        <tr>
            <th>Email:</th>
            <td>{{ $company->email }}</td>
        </tr>
        <tr>
            <th>Contact Person:</th>
            <td>{{ $company->profile->first_name }} {{ $company->profile->last_name }}</td>
        </tr>
        <tr>
            <th>Status:</th>
            <td>{{ $company->status_id == 1 ? 'Active' : 'Inactive' }}</td>
        </tr>
    </table>

    <a href="{{ route('company.edit', $company->id) }}" class="btn btn-warning {{ $company->status_id != 1 ? 'd-none' : '' }}">Edit</a>

    @if($company->status_id == 1)
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deactivateModal">
            Deactivate
        </button>
    @else
        <form action="{{ route('company.reactivate', $company->id) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success">Reactivate</button>
        </form>
    @endif

    <!-- Deactivate Modal -->
    <div class="modal fade" id="deactivateModal" tabindex="-1" aria-labelledby="deactivateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deactivateModalLabel">Deactivate Company</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to deactivate this company account?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('company.destroy', $company->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Deactivate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <hr> 


    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Jobs Posted</h5>

            @if($company->jobs->isEmpty())
                <p>No jobs posted by this company.</p>
            @else
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Available</th>
                            <th>Work Type</th>
                            <th>Schedule</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($company->jobs as $job)
                            <tr>
                                <td>{{ $job->title }}</td>
                                <td>{{ $job->positions_available }}</td>
                                <td>{{ $job->work_type }}</td>
                                <td>
                                    @php
                                        $schedule = json_decode($job->schedule, true);
                                    @endphp
                                    Days: {{ implode(', ', $schedule['days'] ?? []) }} <br>
                                    @if ($job->work_type === 'Hybrid')
                                        On-site Days: {{ implode(', ', $schedule['onsite_days'] ?? []) }} <br><br>
                                        Remote Days: {{ implode(', ', $schedule['remote_days'] ?? []) }} <br>
                                    @endif
                                    Time: {{ \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('h:i A') }} - {{ \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('h:i A') }}
                                </td>
                                <td>
                                    <a href="{{ route('jobs.show', $job->id) }}" class="btn btn-info btn-sm"><i class="bi bi-info-circle"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
