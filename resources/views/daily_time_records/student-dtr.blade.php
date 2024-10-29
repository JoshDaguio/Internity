@extends('layouts.app')
@section('body')

    <div class="pagetitle">
        <h1>Daily Time Record for {{ $student->profile->first_name }} {{ $student->profile->last_name }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Students</li>
                <li class="breadcrumb-item active">DTR Reports</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary mb-3 btn-sm">Back</a>

    <!-- Check if student has an internship -->
    @if(isset($noInternship) && $noInternship)
        <div class="alert alert-danger text-center">
            <p><strong>Section is Locked</strong></p>
            <p>No Internship Yet, Please Apply or Wait for Acceptance</p>
        </div>
    @else

    <div class="row mb-3">
        <!-- Student Profile Card -->
        <div class="col-md-4">
            <div class="card mb-3 h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $student->profile ? $student->profile->first_name . ' ' . $student->profile->last_name : 'N/A' }}</h5>
                    <div class="row">
                        <!-- Profile Picture Section (Center-aligned) -->
                        <div class="col-12 d-flex justify-content-center mb-3">
                            <img id="profilePicturePreview" src="{{ $student->profile->profile_picture ? asset('storage/' . $student->profile->profile_picture) : asset('assets/img/profile-img.jpg') }}" alt="Profile" class="rounded-circle" width="150">
                        </div>

                        <!-- Student Details Section -->
                        <div class="col-12">
                            <p><strong><i class="bi bi-envelope me-2"></i> Email:</strong> {{ $student->email }}</p>
                            <p><strong><i class="bi bi-building me-2"></i> Company:</strong> {{ $student->acceptedInternship->job->company->name }}</p>
                            <p><strong><i class="bi bi-person-badge me-2"></i> Job Title:</strong> {{ $student->acceptedInternship->job->title }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Internship Details Card -->
        <div class="col-md-4">
            <div class="card mb-3 h-100">
                <div class="card-body">
                    <h5 class="card-title">Internship Details</h5>
                    <p><strong><i class="bi bi-hourglass-split"></i> Total Hours:</strong> {{ $totalWorkedHours }} / {{ $remainingHours }} hrs <strong>({{ round($completionPercentage, 2) }}%)</strong></p>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: {{ $completionPercentage }}%; background-color: #B30600;" 
                             aria-valuenow="{{ $completionPercentage }}" aria-valuemin="0" aria-valuemax="100">
                             {{ round($completionPercentage, 2) }}%
                        </div>
                    </div>
                    
                    <p><strong><i class="bi bi-hourglass-split"></i> Internship Hours:</strong> {{ $internshipHours->hours }} hrs <strong>({{ $internshipHours->course->course_code }})</strong></p>
                    <p><strong><i class="bi bi-stopwatch"></i> Remaining Hours:</strong> {{ $remainingHours }} hours</p>
                    <p><strong><span class="badge bg-warning"><i class="bi bi-clock-history"></i> Est. Days Left:</strong></span> {{ ceil($remainingHours / 8) }} days</p>

                    <hr class="my-4">

                    <p><strong><span class="badge bg-success"><i class="bi bi-calendar"></i> Start Date:</strong></span> {{ \Carbon\Carbon::parse($acceptedInternship->start_date)->format('F d, Y') }}</p>
                    <p><strong><span class="badge bg-primary"><i class="bi bi-calendar-check"></i> Est. Date of Finish:</strong></span> {{ $estimatedFinishDate->format('F d, Y') }}</p>
                </div>
            </div>
        </div>
                <div class="col-md-4">
                    <div class="card mb-3 h-100" style="height: 400px; overflow-y: auto;">
                        <div class="card-body">
                            <h5 class="card-title">Internship Schedule</h5>
                            <p><strong><i class="bi bi-geo-alt-fill me-2"></i> Work Type:</strong>  {{ ucfirst($acceptedInternship->work_type) }}</p>

                            <hr class="my-4">

                            <p><strong><i class="bi bi-calendar-week me-2"></i> Schedule:</strong></p>

                            @php
                                function formatTimeTo12Hour($time) {
                                    return \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
                                }

                                $savedSchedule = is_array($acceptedInternship->schedule) 
                                    ? $acceptedInternship->schedule 
                                    : json_decode($acceptedInternship->schedule, true);

                                $customSchedule = isset($acceptedInternship->custom_schedule) && is_array($acceptedInternship->custom_schedule)
                                    ? $acceptedInternship->custom_schedule
                                    : json_decode($acceptedInternship->custom_schedule ?? '[]', true);
                            @endphp

                            @if($student->profile->is_irregular)
                                @foreach($customSchedule as $day => $times)
                                    <p><strong>{{ ucfirst($day) }}:</strong> {{ formatTimeTo12Hour($times['start']) }} - {{ formatTimeTo12Hour($times['end']) }}</p>
                                @endforeach
                            @else
                                @if($acceptedInternship->work_type === 'Hybrid')
                                    <p><strong><u>On-site Days:</u></strong></p>
                                    @foreach($savedSchedule['onsite_days'] as $onsiteDay)
                                        <p><strong>{{ ucfirst($onsiteDay) }}:</strong> {{ formatTimeTo12Hour($savedSchedule['start_time']) }} - {{ formatTimeTo12Hour($savedSchedule['end_time']) }}</p>
                                    @endforeach

                                    <p><strong><u>Remote Days:</u></strong></p>
                                    @foreach($savedSchedule['remote_days'] as $remoteDay)
                                        <p><strong>{{ ucfirst($remoteDay) }}:</strong> {{ formatTimeTo12Hour($savedSchedule['start_time']) }} - {{ formatTimeTo12Hour($savedSchedule['end_time']) }}</p>
                                    @endforeach
                                @else
                                    @foreach($savedSchedule['days'] as $day)
                                        <p><strong>{{ ucfirst($day) }}:</strong> {{ formatTimeTo12Hour($savedSchedule['start_time']) }} - {{ formatTimeTo12Hour($savedSchedule['end_time']) }}</p>
                                    @endforeach
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
    </div>



        <!-- Report Logs Filtered by Month -->
        <div class="row mb-2">
            <div class="col-md-7">
                <div class="card mb-3" style="height: 400px;">
                    <div class="card-body">
                        <h5 class="card-title">Logs for the Month</h5>
                        <!-- Filter by Month -->
                        <form method="GET" action="{{ route('students.dtr', $student->id) }}">
                            <div class="form-group mb-3">
                                <select name="month" id="month" class="form-control" onchange="this.form.submit()">
                                    @foreach ($monthsRange as $monthData)
                                        <option value="{{ $monthData['month'] }}" {{ $selectedMonth == $monthData['month'] ? 'selected' : '' }}>
                                            {{ $monthData['monthName'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                        <!-- Display Table for Logs -->
                        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>Mor. In</th>
                                        <th>Mor. Out</th>
                                        <th>Aft. In</th>
                                        <th>Aft. Out</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($filteredDates as $date)
                                        @php
                                            $dayOfWeek = $date->format('l');
                                            $record = $filteredRecords->where('log_date', $date->format('Y-m-d'))->first();
                                            $logTimes = $record ? json_decode($record->log_times, true) : [];
                                        @endphp

                                        <tr>
                                            <td>{{ $date->format('M d') }}</td>
                                            <td>{{ $dayOfWeek }}</td>

                                            @if (in_array($dayOfWeek, $scheduledDays))
                                                @if ($record)
                                                    <td>{{ $logTimes['morning_in'] ?? 'Not Logged' }}</td>
                                                    <td>{{ $logTimes['morning_out'] ?? 'Not Logged' }}</td>
                                                    <td>{{ $logTimes['afternoon_in'] ?? 'Not Logged' }}</td>
                                                    <td>{{ $logTimes['afternoon_out'] ?? 'Not Logged' }}</td>
                                                @else
                                                    <td colspan="4" class="text-danger text-center">No Logs for This Day</td>
                                                @endif
                                            @else
                                                <td colspan="4" class="text-center">No Internship Schedule for This Date</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Penalties Gained</h5>
                        <div class="table-responsive">
                            <table class="table" style="max-height: 400 px; overflow-y: auto;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Violation</th>
                                        <th>Penalty</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($penaltiesAwarded as $penalty)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($penalty->awarded_date)->format('F d, Y') }}</td>
                                            <td>{{ $penalty->penalty->violation }}</td>
                                            <td>{{ $penalty->penalty_hours }} hours</td>
                                            <td>{{ $penalty->remarks ?? 'None' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>    
            </div>
        </div>

        <div class="row mb-2">
            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
            <div class="col-md-12">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Award Penalty</h5>
                        <form action="{{ route('penalties.award', $student->id) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="penalty_id">Violation</label>
                                <select name="penalty_id" id="penalty_id" class="form-control">
                                    @foreach($penalties->where('penalty_type', 'fixed') as $penalty)
                                        <option value="{{ $penalty->id }}">{{ $penalty->violation }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="awarded_date">Awarded Date</label>
                                <input type="date" name="awarded_date" id="awarded_date" class="form-control" 
                                    max="{{ \Carbon\Carbon::now('Asia/Manila')->format('Y-m-d') }}">
                            </div>
                            <div class="form-group mb-3">
                                <label for="remarks">Remarks (optional)</label>
                                <textarea name="remarks" id="remarks" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Award Penalty</button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            @endif
        </div>

        <div class="row mb-2">
        </div>

    @endif

@endsection
