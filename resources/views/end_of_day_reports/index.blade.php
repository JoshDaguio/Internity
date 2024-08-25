@extends('layouts.app')

@section('body')
    <h1>End of Day Reports</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('end_of_day_reports.create') }}" class="btn btn-primary me-2">Create Report</a>
        <a href="{{ route('end_of_day_reports.compile.monthly') }}" class="btn btn-secondary">Generate Monthly Compilation</a>
    </div>
    
    <form method="GET" action="{{ route('end_of_day_reports.index') }}" class="d-flex align-items-center">
        <label for="filter" class="me-2">Filter:</label>
        <select name="filter" id="filter" class="form-control me-2">
            <option value="week" {{ request('filter') == 'week' ? 'selected' : '' }}>Week</option>
            <option value="month" {{ request('filter') == 'month' ? 'selected' : '' }}>Month</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
    </div>


    @if($reports->isEmpty())
        <p>No reports available.</p>
    @else
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Date Submitted</th>
                    <th>Key Successes</th>
                    <th>Main Challenges</th>
                    <th>Plans for Tomorrow</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $report)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($report->date_submitted)->format('F d, Y') }}</td>
                        <td>{{ Str::limit($report->key_successes, 50) }}</td>
                        <td>{{ Str::limit($report->main_challenges, 50) }}</td>
                        <td>{{ Str::limit($report->plans_for_tomorrow, 50) }}</td>
                        <td>
                            <a href="{{ route('end_of_day_reports.show', $report->id) }}" class="btn btn-primary">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
