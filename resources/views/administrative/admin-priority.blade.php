@extends('layouts.app')

@section('body')
<div class="pagetitle">
    <h1>Manage Priority for {{ $student->profile->first_name }} {{ $student->profile->last_name }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item"><a href="{{ route('students.list') }}">Students</a></li>
            <li class="breadcrumb-item"><a href="{{ route('students.show', $student->id) }}">{{ $student->profile->first_name }} {{ $student->profile->last_name }}</a></li>
            <li class="breadcrumb-item active">Set Priority</li>
        </ol>
    </nav>
</div>

<a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary mb-3 btn-sm">Back</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Current Priorities</h5>
        @if($priorityListings->isEmpty())
            <p>No priority companies selected.</p>
        @else
            <ul>
                @foreach($priorityListings as $priority)
                    <li>
                        {{ $priority->job->company->name }} - {{ $priority->job->title }} (Priority: {{ $priority->priority }})
                        <form action="{{ route('admin.students.removePriority', [$student->id, $priority->job_id]) }}" method="POST" style="display:inline;" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm mb">Remove Priority</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif
        
        <h5 class="card-title">Set Priority for Jobs</h5>

        <table class="table">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Job Title</th>
                    <th>Set Priority</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                    @php
                        // Check if the job is already prioritized and determine if both priorities are filled
                        $existingPriority = $priorityListings->firstWhere('job_id', $job->id);
                        $prioritiesTaken = $priorityListings->pluck('priority')->toArray();
                    @endphp
                    <tr>
                        <td>{{ $job->company->name }}</td>
                        <td>{{ $job->title }}</td>
                        <td>
                            @if ($existingPriority)
                                <p>Already added to {{ $existingPriority->priority == 1 ? '1st' : '2nd' }} Priority</p>
                            @elseif (in_array(1, $prioritiesTaken) && in_array(2, $prioritiesTaken))
                                <p>Both priorities are already set.</p>
                            @else
                                <form method="POST" action="{{ route('admin.students.setPriority', $student->id) }}">
                                    @csrf
                                    <input type="hidden" name="job_id" value="{{ $job->id }}">
                                    <div class="d-flex">
                                        <select name="priority" class="form-control me-2" style="width: auto;" required>
                                            <option value="" disabled selected>Set Priority</option>
                                            @if (!in_array(1, $prioritiesTaken))
                                                <option value="1">First</option>
                                            @endif
                                            @if (!in_array(2, $prioritiesTaken))
                                                <option value="2">Second</option>
                                            @endif
                                        </select>
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-check"></i></button>
                                    </div>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
@endsection
