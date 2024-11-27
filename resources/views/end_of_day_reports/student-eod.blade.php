@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>End Of Day Reports for {{ $student->profile->first_name }} {{ $student->profile->last_name }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Students</li>
                <li class="breadcrumb-item active">EOD Reports</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary mb-3 btn-sm">Back</a>

    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $student->profile ? $student->profile->first_name . ' ' . $student->profile->last_name : 'N/A' }}</h5>

            <div class="row align-items-center">
                <div class="col-sm-12 col-md-3 d-flex justify-content-center mb-3 mb-md-0">
                    <img id="profilePicturePreview" src="{{ $student->profile->profile_picture ? asset('storage/' . $student->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" width="150">
                </div>
                
                <div class="col-sm-12 col-md-9">
                    <p><strong><i class="bi bi-envelope me-2"></i> Email:</strong> {{ $student->email }}</p>
                    <p><strong><i class="bi bi-building me-2"></i> Company:</strong> {{ $student->acceptedInternship->job->company->name }}</p>
                    <p><strong><i class="bi bi-person-badge me-2"></i> Job Title:</strong> {{ $student->acceptedInternship->job->title }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Month Selection Form -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Select Month</h5>
            <form method="GET" action="{{ route('students.eod', $student->id) }}">
                <select name="month" id="month" class="form-select" onchange="this.form.submit()">
                    @foreach($availableMonths as $month)
                        <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Monthly Report ({{ \Carbon\Carbon::create()->month($selectedMonth)->format('F') }} {{ $currentYear }})</h5>

            <div class="table-responsive">
                @if($reports->isEmpty())
                    <p>No reports available for this month.</p>

                    @if(!$missingDates->isEmpty())
                        <h5>Missing Submissions for this month:</h5>
                        <ul>
                            @foreach($missingDates as $missingDate)
                                <li>{{ \Carbon\Carbon::parse($missingDate)->format('F d, Y') }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>All submissions are up-to-date for this month.</p>
                    @endif
                @else
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Submission For</th>
                                <th>Date Submitted</th>
                                <th>Daily Tasks</th>
                                <th>Key Successes</th>
                                <th>Main Challenges</th>
                                <th>Plans for Tomorrow</th>
                                <th>Submission</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($report->submission_for_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($report->date_submitted)->format('M d, Y') }}</td>
                                    <td>
                                        @foreach($report->tasks as $task)
                                            @php
                                                $hours = intdiv($task->time_spent, 60);
                                                $minutes = $task->time_spent % 60;
                                            @endphp
                                            - {{ $task->task_description }} ({{ $hours > 0 ? $hours . ' hrs ' : '' }}{{ $minutes > 0 ? $minutes . ' mins' : '' }})<br>
                                        @endforeach
                                    </td>
                                    <td>{{ $report->key_successes }}</td>
                                    <td>{{ $report->main_challenges }}</td>
                                    <td>{{ $report->plans_for_tomorrow }}</td>
                                    <td>
                                        @if($report->is_late)
                                            <span class="badge bg-danger">Late</span>
                                        @else
                                            <span class="badge bg-success">On-Time</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if(!$missingDates->isEmpty() && $reports->isNotEmpty())
                <h5>Missing Submissions for this month:</h5>
                <ul>
                    @foreach($missingDates as $missingDate)
                        <li>{{ \Carbon\Carbon::parse($missingDate)->format('F d, Y') }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
