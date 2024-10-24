@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Evaluations</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active">Evaluations</li>
            </ol>
        </nav>
    </div>

    <!-- Flash message for success -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Evaluations</h5>

            <!-- Academic Year Filter -->
            <form method="GET" action="{{ route('evaluations.index') }}" class="mb-3">
                <label for="academic_year_id">Filter by Academic Year:</label>
                <select name="academic_year_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->start_year }} - {{ $year->end_year }}
                        </option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('evaluations.create') }}" class="btn btn-success btn-sm mb-3"><i class="bi bi-plus-circle"></i> Create</a>

            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Created By</th>
                            <th>Sent To</th> <!-- Added a new column for recipient role -->
                            <th>Send</th>
                            <th>Results</th>
                            <th>View Form</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                            <tr>
                                <td>{{ $evaluation->title }}</td>
                                <td>{{ ucfirst($evaluation->evaluation_type) }}</td>
                                <td>{{ $evaluation->creator->profile->first_name}} {{ $evaluation->creator->profile->last_name }}</td>
                                <td>
                                    @if($evaluation->recipient_role)
                                        @if($evaluation->recipient_role == 'all')
                                            All (Faculty, Company, Students)
                                        @elseif($evaluation->recipient_role == 'faculty')
                                            Faculty
                                        @elseif($evaluation->recipient_role == 'company')
                                            Company
                                        @elseif($evaluation->recipient_role == 'student')
                                            Students
                                        @endif
                                    @else
                                        Not Sent
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('evaluations.send', $evaluation->id) }}">
                                        @csrf
                                        <div class="d-flex align-items-center">
                                            <select name="recipient_role" class="form-select form-select-sm me-2" style="width: 150px;">
                                                <option value="all">All</option>
                                                <option value="faculty">Faculty</option>
                                                <option value="company">Company</option>
                                                <option value="student">Students</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary btn-sm">Send</button>
                                        </div>
                                    </form>
                                </td>
                                <td>
                                    <a href="{{ route('evaluations.results', $evaluation->id) }}" class="btn btn-info btn-sm"><i class="bi bi-bar-chart-line"></i></a>
                                </td>
                                <td>
                                    <a href="{{ route('evaluations.showResponseForm', $evaluation->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-file-earmark-text"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
