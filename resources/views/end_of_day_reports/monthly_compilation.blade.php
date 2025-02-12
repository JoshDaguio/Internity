@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>End Of Day Reports - Monthly</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item active">Monthly Report</li>
            </ol>
        </nav>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Month Selection Form -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Select Month</h5>
            <form method="GET" action="{{ route('end_of_day_reports.compile.monthly') }}">
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

                <!-- Show missing submissions if no reports are available -->
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
                <!-- Display the reports -->
                <table class="table datatable">
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
                                        - {{ $task->task_description }} ({{ $hours > 0 ? $hours . ' hrs ' : '' }}{{ $minutes > 0 ? $minutes . ' mins' : '' }})<br><br>
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

        <!-- Display Missing Submissions -->
        @if(!$missingDates->isEmpty() && $reports->isNotEmpty())
            <h5>Missing Submissions for this month:</h5>
            <ul>
                @foreach($missingDates as $missingDate)
                    <li>{{ \Carbon\Carbon::parse($missingDate)->format('F d, Y') }}</li>
                @endforeach
            </ul>
        @endif

        <div class="mt-3">
            <a href="{{ route('end_of_day_reports.download.monthly', ['month' => $selectedMonth]) }}" class="btn btn-success btn-sm"><i class="bi bi-download"></i> PDF</a>
            <a href="{{ route('end_of_day_reports.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
        </div>
        <div class="form-text">
            <p><strong><code>Note: Downloading Monthly PDF Report also links your Report File on the Internship Files Section.</code></strong></p>
        </div>
    </div>
</div>

@endsection
