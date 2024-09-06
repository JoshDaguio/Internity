@extends('layouts.app')

@section('body')
    <h1>Job Details</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Field</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Title:</strong></td>
                <td>{{ $job->title }}</td>
            </tr>
            <tr>
                <td><strong>Industry:</strong></td>
                <td>{{ $job->industry }}</td>
            </tr>
            <tr>
                <td><strong>Positions Available:</strong></td>
                <td>{{ $job->positions_available }}</td>
            </tr>
            <tr>
                <td><strong>Location:</strong></td>
                <td>{{ $job->location }}</td>
            </tr>
            <tr>
                <td><strong>Work Type:</strong></td>
                <td>{{ $job->work_type }}</td>
            </tr>
            <tr>
                <td><strong>Schedule:</strong></td>
                <td>
                    @php
                        $schedule = json_decode($job->schedule, true);
                        $startTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['start_time'])->format('g:i A');
                        $endTime = \Carbon\Carbon::createFromFormat('H:i', $schedule['end_time'])->format('g:i A');
                    @endphp
                    Days: {{ implode(', ', $schedule['days'] ?? []) }} <br>
                    @if ($job->work_type === 'Hybrid')
                        On-site Days: {{ implode(', ', $schedule['onsite_days'] ?? []) }} <br>
                        Remote Days: {{ implode(', ', $schedule['remote_days'] ?? []) }} <br>
                    @endif
                    Time: {{ $startTime }} - {{ $endTime }}
                </td>
            </tr>
            <tr>
                <td><strong>Description:</strong></td>
                <td>{{ $job->description }}</td>
            </tr>
            <tr>
                <td><strong>Qualification:</strong></td>
                <td>{{ $job->qualification }}</td>
            </tr>
            <tr>
                <td><strong>Preferred Skills:</strong></td>
                <td>{{ $job->skillTags->pluck('name')->implode(', ') }}</td>
            </tr>
        </tbody>
    </table>

    @if(Auth::id() === $job->company_id)
        <a href="{{ route('jobs.edit', $job) }}" class="btn btn-primary">Edit</a>

        <form action="{{ route('jobs.destroy', $job) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    @endif

    @if(Auth::user()->role_id === 4) <!-- Company -->
        <a href="{{ route('jobs.index') }}" class="btn btn-secondary">Back</a>
    @else <!-- Super Admin or Admin -->
        <a href="{{ route('company.show', $job->company_id) }}" class="btn btn-secondary">Back</a>
    @endif
@endsection
