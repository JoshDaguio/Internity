@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Daily Time Record Report</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Daily Time Record</li>
                <li class="breadcrumb-item active">Reports</li>
            </ol>
        </nav>
    </div>

    <a href="javascript:history.back()" class="btn btn-secondary btn-sm mb-3">Back</a>

    @if(isset($noInternship) && $noInternship)
        <div class="alert alert-danger text-center">
            <p><strong>Section is Locked</strong></p>
            <p>No Internship Yet, Please Apply or Wait for Acceptance</p>
        </div>
    @else

    <div class="row mb-2">
            <!-- Current Date and Time (Philippine Time) -->
            <div class="col-md-4">
                <div class="card mb-3" style="height: 330px;">
                    <div class="card-body">
                        <h5 class="card-title">Current Date & Time (Philippines)</h5>
                        <div class="table-responsive">
                        <iframe src="https://free.timeanddate.com/clock/i9ltpxpe/n2265/fn14/fs20/ftb/tt0/tw1/tm1/th2/tb2" frameborder="0" width="329" height="26"></iframe>
    
                        <!-- <iframe src="https://free.timeanddate.com/clock/i9lf5ga0/n2280/fn14/fs24/ftb/tt0/tw0/tm1/th2/tb2" frameborder="0" width="326" height="31"></iframe> -->
                        </div>

                        <hr class="my-3">

                        <h5 class="card-title">Internship Dates</h5>
                        <p><strong><span class="badge bg-success"><i class="bi bi-calendar"></i> Start Date:</strong></span> {{ \Carbon\Carbon::parse($acceptedInternship->start_date)->format('F d, Y') }}</p>
                        <p><strong><span class="badge bg-primary"><i class="bi bi-calendar-check"></i> Est. Date of Finish:</strong></span> {{ $estimatedFinishDate->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
            <!-- Internship Progress Bar -->
            <div class="col-md-4">
                <div class="card mb-3" style="height: 330px;">
                    <div class="card-body">
                        <h5 class="card-title">Internship Progress</h5>
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
                    </div>
                </div>
            </div>
            <!-- Internship Details -->
            <div class="col-md-4">
                <div class="card mb-3" style="height: 330px; overflow-y: auto;">
                    <div class="card-body">
                        <h5 class="card-title">Internship Schedule</h5>
                        <p><strong><i class="bi bi-geo-alt-fill me-2"></i> Work Type:</strong> {{ ucfirst($acceptedInternship->work_type) }}</p>
                        
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

                        <!-- Regular Days for Regular Students -->
                        @if($student->profile->is_irregular)
                            @foreach($customSchedule as $day => $times)
                                <p><strong>{{ ucfirst($day) }}:</strong> {{ formatTimeTo12Hour($times['start']) }} - {{ formatTimeTo12Hour($times['end']) }}</p>
                            @endforeach
                        @else
                            <!-- Display Hybrid, Onsite, or Remote Schedule -->
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
                                <!-- Regular Days -->
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
            <div class="col-md-8">
                <div class="card mb-3" style="height: 500px;">
                    <div class="card-body">
                        <h5 class="card-title">Logs for the Month</h5>
                        <!-- Filter by Month -->
                        <form method="GET" action="{{ route('dtr.reports') }}">
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
                        <form action="{{ route('reports.generate_pdf') }}" method="POST">
                            @csrf
                            <input type="hidden" name="month" value="{{ $selectedMonth }}">
                            <button type="submit" class="btn btn-success btn-sm mb-3"><i class="bi bi-download"></i> Generate PDF</button>
                        </form>

                        <!-- Display Table for Logs -->
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
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
        </div>

        <!-- Penalties Gained Details -->
        <div class="row mb-2">
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Penalties Gained</h5>
                        <div class="table-responsive" style="max-height: 400 px; overflow-y: auto;">
                            <table class="table">
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



    @endif

@endsection
