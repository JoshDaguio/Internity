@extends('layouts.app')

@section('body')
    <h1>End of Day Report Details</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Task Description</th>
                <th>Time Spent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->tasks as $task)
                <tr>
                    <td>{{ $task->task_description }}</td>
                    <td>{{ $task->time_spent }} {{ ucfirst($task->time_unit) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="table mt-4">
        <tr>
            <th>Date Submitted:</th>
            <td>{{ \Carbon\Carbon::parse($report->date_submitted)->format('F d, Y') }}</td>
        </tr>
        <tr>
            <th>Key Successes:</th>
            <td>{{ $report->key_successes }}</td>
        </tr>
        <tr>
            <th>Main Challenges:</th>
            <td>{{ $report->main_challenges }}</td>
        </tr>
        <tr>
            <th>Plans for Tomorrow:</th>
            <td>{{ $report->plans_for_tomorrow }}</td>
        </tr>
    </table>

    <a href="{{ route('end_of_day_reports.index') }}" class="btn btn-secondary mt-3">Back to Reports</a>
@endsection
