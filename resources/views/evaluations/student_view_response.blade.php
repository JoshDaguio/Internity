@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Evaluation Given by Company</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Evaluations</li>
                <li class="breadcrumb-item active">View Evaluation</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('evaluations.recipientIndex') }}" class="btn btn-secondary btn-sm mb-3">Back</a>

    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $evaluation->title }}</h5>
                    <div class="form-text">
                        <p><strong>{{ $evaluation->description }}</strong></p>
                    </div>

                    <h5 class="card-title">Evaluator Details</h5>
                        <p><strong>Company Name:</strong> {{ $student->acceptedInternship->job->company->name }}</p>
                        <p><strong>Contact Person:</strong> {{ $student->acceptedInternship->job->company->profile->first_name }} {{ $student->acceptedInternship->job->company->profile->last_name }}</p>
                        <p><strong>Email:</strong> {{ $student->acceptedInternship->job->company->email }}</p>

                    <h5 class="card-title">Company Evaluation Responses:</h5>

                    @if ($responses->isNotEmpty())
                        @foreach ($responses as $response)
                            <div class="mb-3">
                                <h5><strong>{{ $response->question->question_text }}</strong></h5>
                                <div class="form-text">
                                    @if ($response->question->question_type == 'radio')
                                        @php
                                            $ratingText = '';
                                            switch($response->response_value) {
                                                case 1:
                                                    $ratingText = '(1) | Strongly Disagree';
                                                    break;
                                                case 2:
                                                    $ratingText = '(2) | Disagree';
                                                    break;
                                                case 3:
                                                    $ratingText = '(3) | Agree';
                                                    break;
                                                case 4:
                                                    $ratingText = '(4) | Strongly Agree';
                                                    break;
                                            }
                                        @endphp
                                        <p>Rating: <strong>{{ $ratingText }}</strong></p>
                                    @elseif ($response->question->question_type == 'long_text')
                                        <p>{{ $response->response_text }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if (!is_null($totalScore) && !is_null($totalPossibleScore))
                            <p><strong>Total Score Given:</strong> {{ $totalScore }} / {{ $totalPossibleScore }}</p>
                        @endif
                    @else
                        <p>No detailed responses to display for this evaluation yet.</p>
                    @endif

                    <a href="{{ route('evaluations.downloadStudentEvaluationPDF', ['evaluation' => $evaluation->id, 'student' => $student->id]) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
