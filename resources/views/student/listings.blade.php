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
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Skills</h5>
                    <p>{{ $student->profile->skillTags->pluck('name')->implode(', ') }}</p>
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
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="jobModalLabel{{ $job->id }}">Job Information</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Company:</strong> {{ $job->company->name }}</p>
                                        <p><strong>Job Title:</strong> {{ $job->title }}</p>
                                        <p><strong>Location:</strong> {{ $job->location }}</p>
                                        <p><strong>Work Type:</strong> {{ $job->work_type }}</p>
                                        <p><strong>Description:</strong> {{ $job->description }}</p>
                                        <p><strong>Qualifications:</strong> {{ $job->qualification }}</p>

                                        <!-- Schedule Details -->
                                        @php
                                            $schedule = json_decode($job->schedule, true);
                                            $startTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('g:i A');
                                            $endTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('g:i A');
                                        @endphp
                                        <p><strong>Schedule:</strong></p>
                                        <ul>
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
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="jobModalLabel{{ $job->id }}">Job Information</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Company:</strong> {{ $job->company->name }}</p>
                                        <p><strong>Job Title:</strong> {{ $job->title }}</p>
                                        <p><strong>Location:</strong> {{ $job->location }}</p>
                                        <p><strong>Work Type:</strong> {{ $job->work_type }}</p>
                                        <p><strong>Description:</strong> {{ $job->description }}</p>
                                        <p><strong>Qualifications:</strong> {{ $job->qualification }}</p>

                                        <!-- Schedule Details -->
                                        @php
                                            $schedule = json_decode($job->schedule, true);
                                            $startTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('g:i A');
                                            $endTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('g:i A');
                                        @endphp
                                        <p><strong>Schedule:</strong></p>
                                        <ul>
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
