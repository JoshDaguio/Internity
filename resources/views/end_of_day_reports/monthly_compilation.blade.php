@extends('layouts.app')

@section('body')
    <h1>{{ \Carbon\Carbon::create()->month($currentMonth)->format('F') }} {{ $currentYear }} - Compiled Reports</h1>

    @if($reports->isEmpty())
        <p>No reports available for this month.</p>
    @else
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
                                - {{ $task->task_description }} ({{ $task->time_spent }} {{ $task->time_unit }})<br>
                            @endforeach
                        </td>
                        <td>{{ $report->key_successes }}</td>
                        <td>{{ $report->main_challenges }}</td>
                        <td>{{ $report->plans_for_tomorrow }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-3">
            <a href="{{ route('end_of_day_reports.download.monthly') }}" class="btn btn-primary"><i class="bi bi-download"></i> PDF</a>
    @endif
            <a href="{{ route('end_of_day_reports.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
@endsection
