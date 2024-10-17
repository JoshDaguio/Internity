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
        @else
            <a href="{{ route('company.show', $job->company_id) }}" class="btn btn-secondary mb-3">Back</a>
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
                        <td class="fw-bold">Industry:</td>
                        <td>{{ $job->industry }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Positions Available:</td>
                        <td>
                            @if($job->positions_available > 0)
                                {{ $job->positions_available }}
                            @else
                                <span class="text-danger">No more available positions</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Location:</td>
                        <td>{{ $job->location }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Work Type:</td>
                        <td>{{ $job->work_type }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Schedule:</td>
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
                        <td class="fw-bold">Description:</td>
                        <td>{{ $job->description }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Qualification:</td>
                        <td>{{ $job->qualification }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Preferred Skills:</td>
                        <td>{{ $job->skillTags->pluck('name')->implode(', ') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="d-flex">
            @if(Auth::id() === $job->company_id)
                <a href="{{ route('jobs.edit', $job) }}" class="btn btn-warning me-2">
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($acceptedInterns as $intern)
                    <tr>
                        <td>{{ $intern->student->profile->first_name }} {{ $intern->student->profile->last_name }}</td>
                        <td>{{ $intern->student->email }}</td>
                        <td>{{ $intern->student->course->course_code }}</td>
                        <td>{{ \Carbon\Carbon::parse($intern->start_date)->format('F d, Y') }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewInternModal{{ $intern->id }}"><i class="bi bi-info-circle"></i></button>
                        </td>
                    </tr>

                    <!-- Intern Details Modal -->
                    <div class="modal fade" id="viewInternModal{{ $intern->id }}" tabindex="-1" aria-labelledby="viewInternModalLabel{{ $intern->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="viewInternModalLabel{{ $intern->id }}">Intern Information</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Full Name:</strong> {{ $intern->student->profile->first_name }} {{ $intern->student->profile->last_name }}</p>
                                    <p><strong>Course:</strong> {{ $intern->student->course->course_code }}</p>
                                    <p><strong>Email:</strong> {{ $intern->student->email }}</p>
                                    <p><strong>Job Title:</strong> {{ $job->title }}</p>
                                    <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($intern->start_date)->format('F d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for file preview -->
<div class="modal fade" id="filePreviewModal" tabindex="-1" aria-labelledby="filePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filePreviewLabel">File Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="filePreviewIframe" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    function showPreview(url) {
        document.getElementById('filePreviewIframe').src = url;
        var filePreviewModal = new bootstrap.Modal(document.getElementById('filePreviewModal'), {});
        filePreviewModal.show();
    }
</script>

@endsection
