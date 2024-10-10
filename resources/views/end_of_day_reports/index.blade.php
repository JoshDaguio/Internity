@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>End of Day Reports</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Reports</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(isset($noInternship) && $noInternship)
        <div class="alert alert-danger text-center">
            <p><strong>Section is Locked</strong></p>
            <p>No Internship Yet, Please Apply or Wait for Acceptance</p>
        </div>
    @else

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('end_of_day_reports.compile.weekly') }}" class="btn btn-success me-2"><i class="bi bi-download"></i> Weekly</a>
            <a href="{{ route('end_of_day_reports.compile.monthly') }}" class="btn btn-success me-2"><i class="bi bi-download"></i> Monthly</a>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Internship Details Card -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Internship Details</h5>
                    <p class="mb-1"><strong>Company:</strong> {{ $acceptedInternship->job->company->name }}</p>
                    <p class="mb-1"><strong>Job:</strong> {{ $acceptedInternship->job->title }}</p>
                    <p class="mb-0"><strong>Work Type and Days:</strong> 
                    @php
                        $schedule = json_decode($acceptedInternship->schedule, true);
                        $workType = $acceptedInternship->work_type;

                        if ($workType === 'Hybrid') {
                            $onsiteDays = implode(', ', $schedule['onsite_days'] ?? []);
                            $remoteDays = implode(', ', $schedule['remote_days'] ?? []);
                            echo "Hybrid | Onsite: {$onsiteDays} | Remote: {$remoteDays}";
                        } elseif ($workType === 'On-site') {
                            $onsiteDays = implode(', ', $schedule['days'] ?? []);
                            echo "On-site | Days: {$onsiteDays}";
                        } elseif ($workType === 'Remote') {
                            $remoteDays = implode(', ', $schedule['days'] ?? []);
                            echo "Remote | Days: {$remoteDays}";
                        } else {
                            echo "Work type information is not available.";
                        }
                    @endphp
                    </p>
                </div>
            </div>
        </div>

        <!-- Schedule Card -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Schedule</h5>
                    <p class="mb-1"><strong>Start Date:</strong> {{ $startDate->format('F d, Y') }}</p>
                    <p class="mb-1"><strong>Schedule Time:</strong> 
                        {{ \Carbon\Carbon::parse($schedule['start_time'])->format('g:i A') }} - 
                        {{ \Carbon\Carbon::parse($schedule['end_time'])->format('g:i A') }}
                    </p>
                    <p class="mb-0"><strong>Current Date:</strong> {{ $currentDateTime->format('F d, Y h:i A') }}</p> <!-- Using API time -->
                </div>
            </div>
        </div>

        <!-- Submissions Card -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Submissions</h5>
                    <p class="mb-1">
                        <strong>
                            <span class="badge bg-danger"><i class="bi bi-exclamation-octagon me-1"></i> Missing Reports:</span>
                        </strong> 
                        {{ $missingDates->count() }}
                    </p>
                    <p class="mb-0">
                        <strong>              
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Created Reports:</span>
                        </strong> 
                        {{ $reports->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Card -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Reports Date Summary</h5>
            <div id="calendar"></div>
        </div>
    </div>

    <style>
        /* Responsive styling for the calendar */
        #calendar {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        /* Ensure the calendar adjusts for different screen sizes */
        @media (max-width: 768px) {
            #calendar {
                padding: 10px;
            }
            .fc-toolbar-title {
                font-size: 1.2rem; /* Adjust font size on smaller screens */
            }
            .fc-button {
                padding: 0.2rem 0.5rem;
                font-size: 0.8rem; /* Adjust button size on smaller screens */
            }
        }

        /* Customize FullCalendar appearance */
        .fc-toolbar-title {
            text-align: left; /* Aligning the title to the left */
            margin-right: 10px; /* Space between title and buttons */
        }
        .fc-toolbar {
            display: flex;
            align-items: center;
            justify-content: start; /* Aligns toolbar items to the left */
        }
        .fc-button {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
            margin-left: 5px; /* Space between buttons */
        }
        .fc-button:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        /* Make sure the day cells are of equal height */
        .fc-daygrid-day-frame {
            min-height: 100px;
        }
        .fc-event {
            font-size: 0.85rem;
            padding: 4px;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'title',
                    center: '',
                    right: 'prev,next'
                },
                events: [
                    // Display submitted reports as blue
                    @if(!$reports->isEmpty())
                    @foreach($reports as $report)
                    {
                        title: 'View Report',
                        start: '{{ $report->date_submitted->format("Y-m-d") }}',
                        backgroundColor: 'blue',
                        url: '{{ route("end_of_day_reports.show", $report->id) }}'
                    },
                    @endforeach
                    @endif
                    
                    // Display missing submissions as red
                    @if(!$missingDates->isEmpty())
                    @foreach($missingDates as $missingDate)
                    {
                        title: 'Missing Submission',
                        start: '{{ \Carbon\Carbon::parse($missingDate)->format("Y-m-d") }}',
                        backgroundColor: '#B30600'
                    },
                    @endforeach
                    @endif

                    // Mark the start date in green
                    {
                        title: 'Internship Start Date',
                        start: '{{ $startDate->format("Y-m-d") }}',
                        backgroundColor: 'green'
                    },

                    // Today's submission (yellow) if scheduled and not yet submitted
                    @if(!$hasSubmittedToday && $isScheduledDay)
                    {
                        title: 'Submit Today',
                        start: '{{ \Carbon\Carbon::now("Asia/Manila")->format("Y-m-d") }}',
                        backgroundColor: '#FFD700',
                        url: '{{ route("end_of_day_reports.create") }}'
                    }
                    @endif
                ],
                validRange: {
                    start: '{{ \Carbon\Carbon::now()->subYear(1)->startOfMonth()->format("Y-m-d") }}',
                    end: '{{ \Carbon\Carbon::now()->addMonth(1)->startOfMonth()->format("Y-m-d") }}' // Up to the start of next month
                },
                dateClick: function(info) {
                    var currentDate = '{{ \Carbon\Carbon::now("Asia/Manila")->format("Y-m-d") }}';
                    
                    // Disable clicks on future dates or dates before the start date
                    if (info.dateStr > currentDate || info.dateStr < '{{ $startDate->format("Y-m-d") }}') {
                        return false;
                    }

                    // Allow submission only on scheduled days
                    const allowedDays = {!! json_encode($scheduleDays) !!};
                    const clickedDay = new Date(info.dateStr).toLocaleDateString('en-US', { weekday: 'long' });

                    if (!allowedDays.includes(clickedDay)) {
                        alert("You can only submit reports on your scheduled days.");
                        return false;
                    }
                },
                nowIndicator: true, // Show the current date
                now: '{{ \Carbon\Carbon::now("Asia/Manila")->format("Y-m-d") }}'
            });

            calendar.render();
        });
    </script>
    @endif
@endsection