@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>End of Day Report Details</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Reports</li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('end_of_day_reports.index') }}" class="btn btn-secondary btn-sm mb-3">Back to Calendar</a>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">End of Day Report</h5>
        
        <!-- Tasks Completed Table -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Task Completed</th>
                        <th>Time Spent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->tasks as $task)
                        @php
                            // Convert time to hours and minutes for display
                            $hours = floor($task->time_spent / 60);
                            $minutes = $task->time_spent % 60;
                            $displayTime = ($hours > 0 ? $hours . ' hour(s) ' : '') . ($minutes > 0 ? $minutes . ' minute(s)' : '');
                        @endphp
                        <tr>
                            <td>{{ $task->task_description }}</td>
                            <td>{{ $displayTime }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Additional Report Information -->
        <table class="table mt-4">
            <tr>
                <th class="table-secondary">Key Successes:</th>
                <td>{{ $report->key_successes }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Main Challenges:</th>
                <td>{{ $report->main_challenges }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Plans for Tomorrow:</th>
                <td>{{ $report->plans_for_tomorrow }}</td>
            </tr>
            <tr>
                <th class="table-secondary">Date Submitted:</th>
                <td>{{ \Carbon\Carbon::parse($report->date_submitted)->format('F d, Y') }}</td>
            </tr>
        </table>
    </div>
</div>

@endsection
