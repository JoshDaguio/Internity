@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>End Of Day Reports - Weekly</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item active">Weekly Report</li>
            </ol>
        </nav>
    </div>

    <div class="card">
            <div class="card-body">
            <h5 class="card-title">Weekly Report ({{ $startOfWeek->format('M d, Y') }} - {{ $endOfWeek->format('M d, Y') }})</h5>
    @if(isset($noReports) && $noReports)
        <p>No reports available for this week.</p>
    @else
                <!-- Tasks Completed Table -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date Submitted</th>
                                <th>Daily Tasks</th>
                                <th>Key Successes</th>
                                <th>Main Challenges</th>
                                <th>Plans for Tomorrow</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($report->date_submitted)->format('F d, Y') }}</td>
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
    @endif
                <!-- Display missing submissions -->
                @if(!$missingDates->isEmpty())
                    <h6><strong>Missing Submissions for this week:</strong></h6>
                    <ul>
                        @foreach($missingDates as $missingDate)
                            <li>{{ \Carbon\Carbon::parse($missingDate)->format('F d, Y') }}</li>
                        @endforeach
                    </ul>
                @else
                    <p>All submissions are up-to-date for this week.</p>
                @endif

                <div class="mt-3">
                    <a href="{{ route('end_of_day_reports.download.weekly') }}" class="btn btn-success btn-sm"><i class="bi bi-download"></i> PDF</a>
                    <a href="{{ route('end_of_day_reports.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </div>
        </div>

@endsection
