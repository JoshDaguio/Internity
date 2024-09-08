@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Available Internship Listings</h1>

    <!-- Student Details -->
    <div class="row mb-4">
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
    <h2>Recommended Internship Listings</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Position</th>
                <th>Company</th>
                <th>Matching Skills</th>
                <th>View Listing</th>
                <th>Add Priority</th>
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
                <td>{{ $job->skillTags->pluck('name')->implode(', ') }}</td>
                <td>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#jobModal{{ $job->id }}">View</button>
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
                            <button type="submit" class="btn btn-primary">Set</button>
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

    <!-- Other Internship Listings -->
    <h2>Other Internship Listings</h2>
    <table class="table">
        <thead>
            <tr>
                <th>Position</th>
                <th>Company</th>
                <th>View Listing</th>
                <th>Add Priority</th>
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
                <td>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#jobModal{{ $job->id }}">View</button>
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
                            <button type="submit" class="btn btn-primary">Set</button>
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
@endsection
