@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Interns with Complete Internship Hours</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Evaluations</li>
                <li class="breadcrumb-item active">Intern Exit Form</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('evaluations.index') }}" class="btn btn-secondary mb-3 btn-sm">Back</a>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Filter Recipients</h5>
            <form method="GET" action="{{ route('evaluations.internCompanyRecipientList', $evaluation->id) }}">
                <select name="filter" class="form-select mb-3" onchange="this.form.submit()">
                    <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All</option>
                    <option value="received" {{ $filter === 'received' ? 'selected' : '' }}>Received</option>
                    <option value="not_received" {{ $filter === 'not_received' ? 'selected' : '' }}>Not Received</option>
                    <option value="answered" {{ $filter === 'answered' ? 'selected' : '' }}>Answered</option>
                    <option value="not_answered" {{ $filter === 'not_answered' ? 'selected' : '' }}>Not Answered</option>
                </select>
            </form>
            <table class="table datatable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Internship Company</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($studentsWithCompleteInternship as $student)
                        @php
                            $status = in_array($student->id, $receivedIds) ? 'received' : 'not_received';
                            $status .= in_array($student->id, $answeredIds) ? ' answered' : ' not_answered';
                        @endphp
                        <tr data-status="{{ $status }}">
                            <td>{{ $student->profile->first_name }} {{ $student->profile->last_name }}</td>
                            <td>{{ $student->course->course_code ?? 'N/A' }}</td>
                            <td>{{ $student->acceptedInternship->job->company->name ?? 'N/A' }}</td>
                            <td>
                                @if(in_array($student->id, $answeredIds))
                                    <span class="badge bg-success">Answered</span>
                                @elseif(in_array($student->id, $receivedIds))
                                    <span class="badge bg-primary">Received</span>
                                @else
                                    <span class="badge bg-secondary">Not Received</span>
                                @endif
                            </td>
                            <td>
                                @if(!in_array($student->id, $receivedIds))
                                    <form method="POST" action="{{ route('evaluations.send', $evaluation->id) }}">
                                        @csrf
                                        <input type="hidden" name="recipient_role" value="student">
                                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                                        <button type="submit" class="btn btn-primary btn-sm">Send</button>
                                    </form>
                                @else
                                    <span class="badge bg-success">Sent</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
