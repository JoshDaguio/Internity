@extends('layouts.app')

@section('body')
    <h1>End of Day Reports</h1>

    @if($reports->isEmpty())
        <p>No reports available.</p>
    @else
        <table class="table">
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
                        <td>{{ $report->date_submitted->format('F d, Y') }}</td>
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
