@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Student Evaluation Scores</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Students</li>
                <li class="breadcrumb-item active">Evaluation Scores</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary mb-3 btn-sm">Back</a>


    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $evaluation->title }}</h5>
                    <div class="form-text">
                        <p><strong>{{ $evaluation->description }}</strong></p>
                    </div>

                    <!-- Student and Company Details Section -->
                    <h5 class="card-title">Student and Company Details</h5>
                    <p><strong>Student Name:</strong> {{ $student->profile->first_name }} {{ $student->profile->last_name }}</p>
                    <p><strong>Course:</strong> {{ $student->course->course_name }}</p>
                    @if($company)
                        <p><strong>Company Name:</strong> {{ $company->name }}</p>
                        <p><strong>Contact Person:</strong> {{ $company->profile->first_name }} {{ $company->profile->last_name }}</p>
                        <p><strong>Email:</strong> {{ $company->email }}</p>
                    @endif

                    <h5 class="card-title">Evaluation Responses:</h5>

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

                    <a href="{{ route('admin.evaluations.downloadStudentScoresPDF', ['evaluation' => $evaluation->id, 'student' => $student->id]) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
