@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Job Details</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}">Job Listings</a></li>
            <li class="breadcrumb-item active">Job Details</li>
        </ol>
    </nav>
</div>


        @if(Auth::user()->role_id === 4)
            <a href="{{ route('jobs.index') }}" class="btn btn-secondary mb-3">Back</a>
        @elseif(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
            <a href="{{ route('admin.jobs.index') }}" class="btn btn-secondary mb-3">Back</a>
        @else
        @endif

    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <!-- Company Details Section -->
                <div class="row align-items-center">
                    <!-- Profile Picture -->
                    <div class="col-sm-12 col-md-3 d-flex justify-content-center mb-3 mb-md-0">
                        <img id="profilePicturePreview" src="{{ $company->profile->profile_picture ? asset('storage/' . $company->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" width="120">
                    </div>

                    <!-- Company Info -->
                    <div class="col-sm-12 col-md-9">
                        <h5 class="card-title">{{ $company->name }}</h5>
                        <p><strong><i class="bi bi-envelope me-2"></i> Email:</strong> {{ $company->email }}</p>
                        <p><strong><i class="bi bi-person-circle me-2"></i> Contact Person:</strong> {{ $company->profile->first_name }} {{ $company->profile->last_name }}</p>
                        <p><strong><i class="bi bi-telephone me-2"></i> Contact Number:</strong> {{ $company->profile->contact_number ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div> 
    @endif


<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <h5 class="card-title">{{ $job->title }}</h5>
        
        <div class="table-responsive">
            <table class="table table-borderless">
                <tbody>
                    <tr>
                        <td class="fw-bold"><i class="bi bi-briefcase"></i> Industry:</td>
                        <td>{{ $job->industry }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bi bi-person-plus"></i> Positions Available:</td>
                        <td>
                            @if($job->positions_available > 0)
                                {{ $job->positions_available }}
                            @else
                                <span class="text-danger">No more available positions</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bi bi-geo-alt"></i> Location:</td>
                        <td>{{ $job->location }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bi bi-building"></i> Work Type:</td>
                        <td>{{ $job->work_type }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bi bi-calendar3"></i> Schedule:</td>
                        <td>
                            @php
                                $schedule = json_decode($job->schedule, true);
                                $startTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('g:i A');
                                $endTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('g:i A');
                            @endphp
                            <strong>Days:</strong> {{ implode(', ', $schedule['days'] ?? []) }} <br>
                            @if ($job->work_type === 'Hybrid')
                                <strong>On-site Days:</strong> {{ implode(', ', $schedule['onsite_days'] ?? []) }} <br>
                                <strong>Remote Days:</strong> {{ implode(', ', $schedule['remote_days'] ?? []) }} <br>
                            @endif
                            <strong>Time:</strong> {{ $startTime }} - {{ $endTime }}
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bi bi-file-text"></i> Description:</td>
                        <td>{{ $job->description }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bi bi-mortarboard"></i> Qualification:</td>
                        <td>{{ $job->qualification }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold"><i class="bi bi-lightbulb"></i> Preferred Skills:</td>
                        <td>{{ $job->skillTags->pluck('name')->implode(', ') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex">
            @if(Auth::id() === $job->company_id)
                <a href="{{ route('jobs.edit', $job) }}" class="btn btn-warning me-2 btn-sm">
                    <i class="bi bi-pencil"></i> 
                </a>
            @endif
        </div>
    </div>
</div>



<!-- Accepted Interns Section -->
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Accepted Interns</h5>
        <div class="table-responsive">
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Intern</th>
                        <th>Email</th>
                        <th>Course</th>
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
                        <td>{{ $intern->student->email }}</td>
                        <td>{{ $intern->student->course->course_code }}</td>
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
