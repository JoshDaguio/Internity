@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Internship Applications</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Internship</li>
                <li class="breadcrumb-item active">Applications</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Student Details -->
    <div class="row mb-0">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Student Name</h5>
                    <p>{{ $student->profile->first_name }} {{ $student->profile->last_name }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Course</h5>
                    <p>{{ $student->course->course_code }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="height: 125px;"> <!-- Set fixed height -->
                <div class="card-body" style="overflow-x: auto;"> <!-- Enable horizontal scrolling -->
                    <h5 class="card-title">Skills</h5>
                    @if($student->profile->skillTags->isNotEmpty())
                        <p style="white-space: nowrap;">{{ $student->profile->skillTags->pluck('name')->implode(', ') }}</p>
                    @else
                        <p>No skills added yet.</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Priority Listings -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Priority Listings</h5>
            <div class="table-responsive">

                <table class="table">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Company</th>
                            <th>Priority</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($priorityListings as $priority)
                        @php
                            // Check if the application has been submitted for this priority
                            $submitted = $submittedApplications->where('job_id', $priority->job_id)->first();
                        @endphp
                        <tr>
                            <td>{{ $priority->job->title }}</td>
                            <td>{{ $priority->job->company->name }}</td>
                            <td>{{ $priority->priority == 1 ? '1st Priority' : '2nd Priority' }}</td>
                            <td>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#jobModal{{ $priority->job->id }}"><i class="bi bi-info-circle"></i></button>

                                <!-- Remove priority button (only show if application is not yet submitted) -->
                                @if(!$submitted)
                                    <form method="POST" action="{{ route('internship.removePriority', $priority->job->id) }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Remove Priority</button>
                                    </form>
                                @endif
                            </td>
                        </tr>

                        
<!-- Job Modal -->
<div class="modal fade" id="jobModal{{ $priority->job->id }}" tabindex="-1" aria-labelledby="jobModalLabel{{ $priority->job->id }}" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobModalLabel{{ $priority->job->id }}">Job Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Company Details Section -->
                <h5 class="card-title text-center">{{ $priority->job->company->name }}</h5>
                <div class="card-body d-flex align-items-center">
                    <img src="{{ $priority->job->company->profile->profile_picture ? asset('storage/' . $priority->job->company->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Company Profile" class="rounded-circle img-thumbnail" width="150">
                    <div class="ms-5">
                        <p><strong><i class="bi bi-person-circle me-2"></i> Contact Person:</strong> {{ $priority->job->company->profile->first_name }} {{ $priority->job->company->profile->last_name }}</p>
                        <p><strong><i class="bi bi-telephone me-2"></i> Contact Number:</strong> {{ $priority->job->company->profile->contact_number ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-geo-alt me-2"></i> Address:</strong> {{ $priority->job->company->profile->address ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-info-circle me-2"></i> About:</strong> {{ $priority->job->company->profile->about ?? 'No details provided.' }}</p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Links Section -->
                <div class="row">
                    <div class="col-md-3">
                        <strong><i class="bi bi-link-45deg me-2"></i> Links:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($priority->job->company->profile->links->isNotEmpty())
                            <ul class="list-unstyled">
                                @foreach($priority->job->company->profile->links as $link)
                                    <li><a href="{{ $link->link_url }}" target="_blank">{{ $link->link_name }}</a></li>
                                @endforeach
                            </ul>
                        @else
                            <p>No links available.</p>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <!-- Job Details Section -->
                <h5 class="card-title">Job Details</h5>
                <p><strong><i class="bi bi-briefcase-fill me-2"></i> Job Title:</strong> {{ $priority->job->title }}</p>
                <p><strong><i class="bi bi-geo-alt-fill me-2"></i> Location:</strong> {{ $priority->job->location }}</p>
                <p><strong><i class="bi bi-building me-2"></i> Work Type:</strong> {{ $priority->job->work_type }}</p>
                <p><strong><i class="bi bi-card-text me-2"></i> Description:</strong> {{ $priority->job->description }}</p>
                <p><strong><i class="bi bi-award me-2"></i> Qualifications:</strong> {{ $priority->job->qualification }}</p>

                <!-- Schedule Details -->
                @php
                    $schedule = json_decode($priority->job->schedule, true);
                    $startTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('g:i A');
                    $endTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('g:i A');
                @endphp
                <p><strong><i class="bi bi-clock me-2"></i> Schedule:</strong></p>
                <ul>
                    <li>Days: {{ implode(', ', $schedule['days'] ?? []) }}</li>
                    @if ($priority->job->work_type === 'Hybrid')
                        <li>On-site Days: {{ implode(', ', $schedule['onsite_days'] ?? []) }}</li>
                        <li>Remote Days: {{ implode(', ', $schedule['remote_days'] ?? []) }}</li>
                    @endif
                    <li>Time: {{ $startTime }} - {{ $endTime }}</li>
                </ul>

                <hr class="my-4">

                <!-- Application Submission -->
                @if(!$submitted)
                    @if($priority->priority == 1)
                        <form method="POST" action="{{ route('internship.submit', $priority->job->id) }}">
                            @csrf
                            @if($student->requirements && $student->requirements->endorsement_letter)
                                <p class="text-success">Your endorsement letter will be automatically included in this application.</p>
                            @else
                                <p class="text-danger">Please wait for your endorsement letter to be uploaded in the requirements.</p>
                            @endif

                            @if($student->profile->cv_file_path)
                                <p class="text-success">Your CV will be automatically included in this application.</p>
                            @else
                                <p class="text-danger">Please upload your CV in your profile before submitting.</p>
                            @endif

                            @if($student->profile->cv_file_path && $student->requirements && $student->requirements->endorsement_letter)
                                <button type="submit" class="btn btn-primary">Submit Application</button>
                            @else
                                <p class="text-danger">You cannot submit your application until both your CV and endorsement letter are uploaded.</p>
                            @endif
                        </form>
                    @elseif($priority->priority == 2 && $firstPrioritySubmittedOrRejected)
                        <form method="POST" action="{{ route('internship.submit', $priority->job->id) }}">
                            @csrf
                            @if($student->requirements && $student->requirements->endorsement_letter)
                                <p class="text-success">Your endorsement letter will be automatically included in this application.</p>
                            @else
                                <p class="text-danger">Please wait for your endorsement letter to be uploaded in the requirements.</p>
                            @endif

                            @if($student->profile->cv_file_path)
                                <p class="text-success">Your CV will be automatically included in this application.</p>
                            @else
                                <p class="text-danger">Please upload your CV in your profile before submitting.</p>
                            @endif

                            @if($student->profile->cv_file_path && $student->requirements && $student->requirements->endorsement_letter)
                                <button type="submit" class="btn btn-primary">Submit Application</button>
                            @else
                                <p class="text-danger">You cannot submit your application until both your CV and endorsement letter are uploaded.</p>
                            @endif
                        </form>
                    @else
                        <p class="text-warning">First Priority Application Still In Progress</p>
                    @endif
                @else
                    <p class="text-success">Application Submitted</p>
                @endif

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

    <!-- Submitted Applications -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Submitted Applications</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($submittedApplications as $application)
                        <tr>
                            <td>{{ $application->job->title }}</td>
                            <td>{{ $application->job->company->name }}</td>
                            <td>
                                <span class="badge 
                                    {{ $application->status->status == 'To Review' ? 'bg-warning' : 
                                    ($application->status->status == 'Accepted' ? 'bg-success' : 
                                    ($application->status->status == 'For Interview' ? 'bg-primary' : 
                                    'bg-danger')) }}">
                                    {{ $application->status->status }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#submittedJobModal{{ $application->job->id }}"><i class="bi bi-info-circle"></i></button>
                            </td>
                        </tr>

                        <div class="modal fade" id="submittedJobModal{{ $application->job->id }}" tabindex="-1" aria-labelledby="submittedJobModalLabel{{ $application->job->id }}" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submittedJobModalLabel{{ $application->job->id }}">Application Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Company Details Section -->
                <h5 class="card-title text-center">{{ $application->job->company->name }}</h5>
                <div class="card-body d-flex align-items-center">
                        <img src="{{ $application->job->company->profile->profile_picture ? asset('storage/' . $application->job->company->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Company Profile" class="rounded-circle img-thumbnail" width="150">
                    <div class="ms-5">
                        <p><strong><i class="bi bi-person-circle me-2"></i> Contact Person:</strong> {{ $application->job->company->profile->first_name }} {{ $application->job->company->profile->last_name }}</p>
                        <p><strong><i class="bi bi-telephone me-2"></i> Contact Number:</strong> {{ $application->job->company->profile->contact_number ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-geo-alt me-2"></i> Address:</strong> {{ $application->job->company->profile->address ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-info-circle me-2"></i> About:</strong> {{ $application->job->company->profile->about ?? 'No details provided.' }}</p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Links Section -->
                <div class="row">
                    <div class="col-md-3">
                        <strong><i class="bi bi-link-45deg me-2"></i> Links:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($application->job->company->profile->links->isNotEmpty())
                            <ul class="list-unstyled">
                                @foreach($application->job->company->profile->links as $link)
                                    <li><a href="{{ $link->link_url }}" target="_blank">{{ $link->link_name }}</a></li>
                                @endforeach
                            </ul>
                        @else
                            <p>No links available.</p>
                        @endif
                    </div>
                </div>

                <hr class="my-4">

                <!-- Job Details Section -->
                <h5 class="card-title">Job Details</h5>
                <p><strong><i class="bi bi-briefcase-fill me-2"></i> Job Title:</strong> {{ $application->job->title }}</p>
                <p><strong><i class="bi bi-geo-alt-fill me-2"></i> Location:</strong> {{ $application->job->location }}</p>
                <p><strong><i class="bi bi-building me-2"></i> Work Type:</strong> {{ $application->job->work_type }}</p>
                <p><strong><i class="bi bi-card-text me-2"></i> Description:</strong> {{ $application->job->description }}</p>
                <p><strong><i class="bi bi-award me-2"></i> Qualifications:</strong> {{ $application->job->qualification }}</p>

                <!-- Schedule Details -->
                @php
                    $schedule = json_decode($application->job->schedule, true);
                    $startTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('g:i A');
                    $endTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('g:i A');
                @endphp
                <p><strong><i class="bi bi-clock me-2"></i> Schedule:</strong></p>
                <ul>
                    <li>Days: {{ implode(', ', $schedule['days'] ?? []) }}</li>
                    @if ($application->job->work_type === 'Hybrid')
                        <li>On-site Days: {{ implode(', ', $schedule['onsite_days'] ?? []) }}</li>
                        <li>Remote Days: {{ implode(', ', $schedule['remote_days'] ?? []) }}</li>
                    @endif
                    <li>Time: {{ $startTime }} - {{ $endTime }}</li>
                </ul>

                <hr class="my-4">

                <!-- Display Uploaded Endorsement Letter -->
                @if($application->endorsement_letter_path)
                    <p><strong><i class="bi bi-file-earmark-text me-2"></i> Endorsement Letter:</strong> <button class="btn btn-dark" onclick="showPreview('{{ route('application.preview', ['type' => 'endorsement_letter', 'id' => $application->id]) }}')"><i class="bi bi-folder"></i></button></p>
                @else
                    <p><strong><i class="bi bi-file-earmark-text me-2"></i> Endorsement Letter:</strong> Not uploaded</p>
                @endif

                <!-- Display Uploaded CV -->
                @if($application->cv_path)
                    <p><strong><i class="bi bi-file-person me-2"></i> CV:</strong> <button class="btn btn-dark" onclick="showPreview('{{ route('application.preview', ['type' => 'cv', 'id' => $application->id]) }}')"><i class="bi bi-folder"></i></button></p>
                @else
                    <p><strong><i class="bi bi-file-person me-2"></i> CV:</strong> Not uploaded</p>
                @endif
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

