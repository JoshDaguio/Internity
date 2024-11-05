@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Companies with Interns to Evaluate</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Evaluations</li>
                <li class="breadcrumb-item active">Intern Performance Evaluation</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('evaluations.index') }}" class="btn btn-secondary mb-3 btn-sm">Back</a>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Filter Recipients</h5>
            <form method="GET" action="{{ route('evaluations.internStudentRecipientList', $evaluation->id) }}">
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
                        <th>Company Name</th>
                        <th>Intern Name</th>
                        <th>Course</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($companiesWithInterns as $company)
                    @foreach($company->interns as $intern)
                        @php
                            $status = in_array($intern->student_id, $receivedIds) ? 'received' : 'not_received';
                            $status .= in_array($intern->student_id, $answeredIds) ? ' answered' : ' not_answered';
                        @endphp
                        <tr data-status="{{ $status }}">
                            <td>{{ $company->name }}</td>
                            <td>{{ $intern->student->profile->first_name }} {{ $intern->student->profile->last_name }}</td>
                            <td>{{ $intern->student->course->course_code ?? 'N/A' }}</td>
                            <td>
                                @if(in_array($intern->student_id, $answeredIds))
                                    <span class="badge bg-success">Answered</span>
                                @elseif(in_array($intern->student_id, $receivedIds))
                                    <span class="badge bg-primary">Received</span>
                                @else
                                    <span class="badge bg-secondary">Not Received</span>
                                @endif
                            </td>
                            <td>
                                @if(!in_array($intern->student_id, $receivedIds))
                                    <form method="POST" action="{{ route('evaluations.sendInternStudent', $evaluation->id) }}">
                                        @csrf
                                        <input type="hidden" name="student_id" value="{{ $intern->student_id }}">
                                        <button type="submit" class="btn btn-primary btn-sm">Send</button>
                                    </form>
                                @else
                                    <span class="badge bg-success">Sent</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
