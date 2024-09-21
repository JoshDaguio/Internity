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


    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('end_of_day_reports.compile.weekly') }}" class="btn btn-success me-2"><i class="bi bi-download"></i> Weekly</a>
            <a href="{{ route('end_of_day_reports.compile.monthly') }}" class="btn btn-success me-2"><i class="bi bi-download"></i> Monthly</a>
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
                    @if(!$reports->isEmpty())
                    // Display reports if available
                    @foreach($reports as $report)
                    {
                        title: 'View Report',
                        start: '{{ $report->date_submitted->format("Y-m-d") }}',
                        backgroundColor: 'green',
                        url: '{{ route("end_of_day_reports.show", $report->id) }}'
                    },
                    @endforeach
                    @endif
                    
                    @if(!$missingDates->isEmpty())
                    // Display missing submissions as red
                    @foreach($missingDates as $missingDate)
                    {
                        title: 'Missing Submission',
                        start: '{{ \Carbon\Carbon::parse($missingDate)->format("Y-m-d") }}',
                        backgroundColor: '#B30600'
                    },
                    @endforeach
                    @endif

                    // Today's submission (yellow) if not yet submitted
                    @if(!$hasSubmittedToday && $isWeekday)
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
                    end: '{{ \Carbon\Carbon::now()->addMonth(1)->startOfMonth()->format("Y-m-d") }}'  // Adds one day to the end of the month
                },
                dateClick: function(info) {
                    var currentDate = '{{ \Carbon\Carbon::now("Asia/Manila")->format("Y-m-d") }}';
                    if (info.dateStr > currentDate) {
                        return false; // disable clicks on future dates
                    }
                }
            });

            calendar.render();
        });
    </script>
@endsection
