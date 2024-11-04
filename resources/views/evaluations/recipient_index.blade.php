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

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Evaluations Available to You</h5>

            <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Eval. Type</th>
                            <th>Posted By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                            <tr>
                                <td>
                                    <a href="{{ route('evaluations.viewUserResponse', $evaluation->id) }}" class="btn btn-light btn-sm">
                                        {{ $evaluation->title }}
                                    </a>
                                </td>
                                <td>
                                    @if ($evaluation->evaluation_type == 'program')
                                        Program Evaluation
                                    @elseif ($evaluation->evaluation_type == 'intern_student')
                                        Intern Evaluation by Company
                                    @elseif ($evaluation->evaluation_type == 'intern_company')
                                        Company Evaluation by Intern
                                    @endif
                                </td>
                                <td>{{ $evaluation->creator->profile->first_name}} {{ $evaluation->creator->profile->last_name }}</td>
                                <td>
                                    @if($evaluation->is_answered)
                                        <span class="badge bg-success">Answered</span>
                                    @else
                                        <span class="badge bg-warning">Not Answered</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$evaluation->is_answered)
                                        <a href="{{ route('evaluations.showResponseForm', $evaluation->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-file-earmark-text"></i> Answer</a>
                                    @else
                                        <span class="badge bg-success">Already Answered</span>
                                    @endif
                                </td>

                                <!-- <td>
                                    @if($evaluation->responses->where('user_id', auth()->id())->count() > 0)
                                        <a href="{{ route('evaluations.viewUserResponse', $evaluation->id) }}" class="btn btn-info btn-sm">
                                            <i class="bi bi-file-earmark-text"></i> 
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">No Response</span>
                                    @endif
                                </td> -->
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Card for Student to View Company Evaluation -->
@if(auth()->user()->role_id == 5) <!-- Only visible to students -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Intern Evaluation by Company</h5>
                    
                    @if($completedCompanyEvaluation)
                        <!-- Show button to view evaluation if completed by the company -->
                        <p>
                            Your Intern Evaluation is now available. Click here to see score:
                            <a href="{{ route('evaluations.viewStudentEvaluation', ['evaluation' => $completedCompanyEvaluation->id, 'student' => auth()->id()]) }}" class="btn btn-primary btn-sm">
                                View
                            </a>
                        </p>
                    @else
                        <!-- Message if no evaluation has been completed yet -->
                        <p class="text-muted">No evaluation from your internship company is available yet.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection
