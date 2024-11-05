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
            <h5 class="card-title">Filter by Academic Year & Evaluation Type</h5>
            <form method="GET" action="{{ route('evaluations.index') }}">
                <div class="row">
                    <!-- Academic Year Filter -->
                    <div class="col-md-6">
                        <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                    {{ $year->start_year }} - {{ $year->end_year }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Evaluation Type Filter -->
                    <div class="col-md-6">
                        <select name="evaluation_type" class="form-select" onchange="this.form.submit()">
                            <option value="" {{ $selectedEvaluationType == '' ? 'selected' : '' }}>All Types</option>
                            <option value="program" {{ $selectedEvaluationType == 'program' ? 'selected' : '' }}>Program</option>
                            <option value="program" {{ $selectedEvaluationType == 'intern_student' ? 'selected' : '' }}>Intern Evalution by Company</option>
                            <option value="intern_company" {{ $selectedEvaluationType == 'intern_company' ? 'selected' : '' }}>Company Evalution by Intern</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Evaluations</h5>
            <a href="{{ route('evaluations.create') }}" class="btn btn-success btn-sm mb-3"><i class="bi bi-plus-circle"></i> Create</a>

            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Created By</th>
                            <th>Sent To</th>
                            <th>Send</th>
                            <!-- <th>Results</th> -->
                            <th>Manage Questions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                            <tr>
                                <td>
                                    @if ($evaluation->evaluation_type == 'program')
                                        <a href="{{ route('evaluations.results', $evaluation->id) }}" class="btn btn-light btn-sm">
                                            {{ $evaluation->title }}
                                        </a>
                                    @elseif ($evaluation->evaluation_type == 'intern_student')
                                        <a href="{{ route('evaluations.internStudentRecipientList', $evaluation->id) }}" class="btn btn-light btn-sm">
                                            {{ $evaluation->title }}
                                        </a>
                                    @elseif ($evaluation->evaluation_type == 'intern_company')
                                        <a href="{{ route('evaluations.internCompanyRecipientList', $evaluation->id) }}" class="btn btn-light btn-sm">
                                            {{ $evaluation->title }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    @if ($evaluation->evaluation_type == 'program')
                                        Program Evaluation
                                    @elseif ($evaluation->evaluation_type == 'intern_student')
                                        Intern Performance Evaluation
                                    @elseif ($evaluation->evaluation_type == 'intern_company')
                                        Intern Exit Form
                                    @endif
                                </td>
                                <td>{{ $evaluation->creator->profile->first_name}} {{ $evaluation->creator->profile->last_name }}</td>
                                <td>
                                    @if ($evaluation->evaluation_type == 'program')
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
                                    @elseif ($evaluation->evaluation_type == 'intern_student')
                                        Companies
                                    @elseif ($evaluation->evaluation_type == 'intern_company')
                                        Students
                                    @endif
                                </td>
                                <td>
                                    @if ($evaluation->evaluation_type == 'program')
                                        <form method="POST" action="{{ route('evaluations.send', $evaluation->id) }}">
                                            @csrf
                                            <div class="d-flex align-items-center">
                                                <select name="recipient_role" class="form-select form-select-sm me-2" style="width: 150px;">
                                                    <option value="all">All</option>
                                                    <option value="faculty">Faculty</option>
                                                    <option value="company">Companies</option>
                                                    <option value="student">Students</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary btn-sm">Send</button>
                                            </div>
                                        </form>
                                    @elseif ($evaluation->evaluation_type == 'intern_student')
                                        To be sent to Companies
                                    @elseif ($evaluation->evaluation_type == 'intern_company')
                                        To be sent to Students
                                    @endif
                                </td>
                                <!-- <td>
                                    <a href="{{ route('evaluations.results', $evaluation->id) }}" class="btn btn-info btn-sm"><i class="bi bi-bar-chart-line"></i></a>
                                </td> -->
                                <td>
                                    <a href="{{ route('evaluations.manageQuestions', $evaluation->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-gear"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@endsection
