@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Internship Listings</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Internship</li>
                <li class="breadcrumb-item active">Listings</li>
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

    <!-- Recommended Internship Listings -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Recommended Internship Listings</h5>
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Company</th>
                            <th>Available</th>
                            <th>Matching Skills</th>
                            <th>View</th>
                            <th>Priority</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recommendedJobs as $job)
                        @php
                            // Check if the job is already in the student's priorities
                            $existingPriority = $student->priorities ? $student->priorities->where('job_id', $job->id)->first() : null;
                            $existingPriorities = $student->priorities ? $student->priorities->pluck('priority')->toArray() : [];
                        @endphp
                        <tr>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->company->name }}</td>
                            <td>{{ $job->positions_available }}</td>
                            <td>
                                @php
                                    // Get the student's skills and the job's preferred skills
                                    $studentSkills = $student->profile->skillTags->pluck('name')->toArray();
                                    $jobSkills = $job->skillTags->pluck('name')->toArray();

                                    // Find the matching skills between the student and the job
                                    $matchingSkills = array_intersect($studentSkills, $jobSkills);
                                @endphp

                                <!-- Display the matching skills, if any -->
                                {{ implode(', ', $matchingSkills) ?: 'No Matching Skills' }}
                            </td>

                            <td>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#jobModal{{ $job->id }}"><i class="bi bi-info-circle"></i></button>
                            </td>
                            <td>
                                @if ($existingPriority)
                                    <p>Already added to {{ $existingPriority->priority == 1 ? '1st' : '2nd' }} Priority</p>
                                @elseif (count($existingPriorities) == 2)
                                    <p>Both priorities are already set.</p>
                                @else
                                <form method="POST" action="{{ route('internship.priority') }}">
                                    @csrf
                                    <input type="hidden" name="job_id" value="{{ $job->id }}">

                                    <div class="d-flex">
                                        <select name="priority" class="form-control me-2" style="width: auto;">
                                            <option value="" selected disabled>Set Priority</option>
                                            @if (!in_array(1, $existingPriorities))
                                                <option value="1">First</option>
                                            @endif
                                            @if (!in_array(2, $existingPriorities))
                                                <option value="2">Second</option>
                                            @endif
                                        </select>
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i></button>
                                    </div>
                                </form>
                                @endif
                            </td>
                        </tr>

                        <!-- Job Modal -->
<div class="modal fade" id="jobModal{{ $job->id }}" tabindex="-1" aria-labelledby="jobModalLabel{{ $job->id }}" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobModalLabel{{ $job->id }}">Job Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Company Details Section -->
                <h5 class="card-title  text-center">{{ $job->company->name }}</h5>
                <div class="card-body d-flex align-items-center">
                    <img id="profilePicturePreview" src="{{ $job->company->profile->profile_picture ? asset('storage/' . $job->company->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" 
                         alt="Company Profile" class="rounded-circle" width="150">
                    <div class="ms-5">
                        <p><strong><i class="bi bi-person-circle me-2"></i> Contact Person:</strong> {{ $job->company->profile->first_name }} {{ $job->company->profile->last_name }}</p>
                        <p><strong><i class="bi bi-telephone me-2"></i> Contact Number:</strong> {{ $job->company->profile->contact_number ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-geo-alt me-2"></i> Address:</strong> {{ $job->company->profile->address ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-info-circle me-2"></i> About:</strong> {{ $job->company->profile->about ?? 'No details provided.' }}</p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Links Section -->
                <div class="row">
                    <div class="col-md-3">
                        <strong><i class="bi bi-link-45deg me-2"></i> Links:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($job->company->profile->links->isNotEmpty())
                            <ul class="list-unstyled">
                                @foreach($job->company->profile->links as $link)
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
                <p><strong><i class="bi bi-briefcase-fill me-2"></i> Job Title:</strong> {{ $job->title }}</p>
                <p><strong><i class="bi bi-geo-alt-fill me-2"></i> Location:</strong> {{ $job->location }}</p>
                <p><strong><i class="bi bi-building me-2"></i> Work Type:</strong> {{ $job->work_type }}</p>
                <p><strong><i class="bi bi-card-text me-2"></i> Description:</strong> {{ $job->description }}</p>
                <p><strong><i class="bi bi-award me-2"></i> Qualifications:</strong> {{ $job->qualification }}</p>

                <!-- Schedule Details -->
                @php
                    $schedule = json_decode($job->schedule, true);
                    $startTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('g:i A');
                    $endTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('g:i A');
                @endphp
                <p><strong><i class="bi bi-clock me-2"></i> Schedule:</strong></p>
                <ul class="list-unstyled">
                    <li>Days: {{ implode(', ', $schedule['days'] ?? []) }}</li>
                    @if ($job->work_type === 'Hybrid')
                        <li>On-site Days: {{ implode(', ', $schedule['onsite_days'] ?? []) }}</li>
                        <li>Remote Days: {{ implode(', ', $schedule['remote_days'] ?? []) }}</li>
                    @endif
                    <li>Time: {{ $startTime }} - {{ $endTime }}</li>
                </ul>
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

    <!-- Other Internship Listings -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Other Internship Listings</h5>
            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Company</th>
                            <th>Available</th>
                            <th>View</th>
                            <th>Priority</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($otherJobs as $job)
                        @php
                            $existingPriority = $student->priorities ? $student->priorities->where('job_id', $job->id)->first() : null;
                            $existingPriorities = $student->priorities ? $student->priorities->pluck('priority')->toArray() : [];
                        @endphp
                        <tr>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->company->name }}</td>
                            <td>{{ $job->positions_available }}</td>
                            <td>
                                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#jobModal{{ $job->id }}"><i class="bi bi-info-circle"></i></button>
                            </td>
                            <td>
                                @if ($existingPriority)
                                    <p>Already added to {{ $existingPriority->priority == 1 ? '1st' : '2nd' }} Priority</p>
                                @elseif (count($existingPriorities) == 2)
                                    <p>Both priorities are already set.</p>
                                @else
                                <form method="POST" action="{{ route('internship.priority') }}">
                                    @csrf
                                    <input type="hidden" name="job_id" value="{{ $job->id }}">

                                    <div class="d-flex">
                                        <select name="priority" class="form-control me-2" style="width: auto;">
                                            <option value="" selected disabled>Set Priority</option>
                                            @if (!in_array(1, $existingPriorities))
                                                <option value="1">First</option>
                                            @endif
                                            @if (!in_array(2, $existingPriorities))
                                                <option value="2">Second</option>
                                            @endif
                                        </select>
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i></button>
                                    </div>
                                </form>
                                @endif
                            </td>
                        </tr>

                        <!-- Job Modal -->
<div class="modal fade" id="jobModal{{ $job->id }}" tabindex="-1" aria-labelledby="jobModalLabel{{ $job->id }}" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobModalLabel{{ $job->id }}">Job Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Company Details Section -->
                <h5 class="card-title text-center">{{ $job->company->name }}</h5>
                <div class="card-body d-flex align-items-center">
                    <img id="profilePicturePreview" src="{{ $job->company->profile->profile_picture ? asset('storage/' . $job->company->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" 
                         alt="Company Profile" class="rounded-circle" width="150">
                    <div class="ms-5">
                        <p><strong><i class="bi bi-person-circle me-2"></i> Contact Person:</strong> {{ $job->company->profile->first_name }} {{ $job->company->profile->last_name }}</p>
                        <p><strong><i class="bi bi-telephone me-2"></i> Contact Number:</strong> {{ $job->company->profile->contact_number ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-geo-alt me-2"></i> Address:</strong> {{ $job->company->profile->address ?? 'N/A' }}</p>
                        <p><strong><i class="bi bi-info-circle me-2"></i> About:</strong> {{ $job->company->profile->about ?? 'No details provided.' }}</p>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Links Section -->
                <div class="row">
                    <div class="col-md-3">
                        <strong><i class="bi bi-link-45deg me-2"></i> Links:</strong>
                    </div>
                    <div class="col-md-9">
                        @if($job->company->profile->links->isNotEmpty())
                            <ul class="list-unstyled">
                                @foreach($job->company->profile->links as $link)
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
                <p><strong><i class="bi bi-briefcase-fill me-2"></i> Job Title:</strong> {{ $job->title }}</p>
                <p><strong><i class="bi bi-geo-alt-fill me-2"></i> Location:</strong> {{ $job->location }}</p>
                <p><strong><i class="bi bi-building me-2"></i> Work Type:</strong> {{ $job->work_type }}</p>
                <p><strong><i class="bi bi-card-text me-2"></i> Description:</strong> {{ $job->description }}</p>
                <p><strong><i class="bi bi-award me-2"></i> Qualifications:</strong> {{ $job->qualification }}</p>

                <!-- Schedule Details -->
                @php
                    $schedule = json_decode($job->schedule, true);
                    $startTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('g:i A');
                    $endTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('g:i A');
                @endphp
                <p><strong><i class="bi bi-clock me-2"></i> Schedule:</strong></p>
                <ul class="list-unstyled">
                    <li>Days: {{ implode(', ', $schedule['days'] ?? []) }}</li>
                    @if ($job->work_type === 'Hybrid')
                        <li>On-site Days: {{ implode(', ', $schedule['onsite_days'] ?? []) }}</li>
                        <li>Remote Days: {{ implode(', ', $schedule['remote_days'] ?? []) }}</li>
                    @endif
                    <li>Time: {{ $startTime }} - {{ $endTime }}</li>
                </ul>
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
@endsection
