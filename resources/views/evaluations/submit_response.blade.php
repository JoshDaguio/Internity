@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Submit Evaluation</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item">Evaluations</li>
            <li class="breadcrumb-item active">Submit</li>
        </ol>
    </nav>
</div>
    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $evaluation->title }}</h5>
                    <div class="form-text">
                        <p><strong>{{ $evaluation->description }}</strong></p>
                    </div>

                    @if($evaluation->is_answered)
                    <div class="alert alert-success text-center">
                        <p><strong>Section is Locked</strong></p>
                        <p>Evaluation Already Answered.</p>
                    </div>
                    @else
                        <form action="{{ route('evaluations.submitResponse', $evaluation->id) }}" method="POST">
                            @csrf

                            @if ($evaluation->evaluation_type === 'intern_student')
                                <h5 class="card-title">Intern Details</h5>
                                <p><strong>Intern Name:</strong> {{ $evaluatee->profile->first_name }} {{ $evaluatee->profile->last_name }}</p>
                                <p><strong>Intern's Course:</strong> {{ $evaluatee->course->course_code ?? 'N/A' }}</p>
                                <p><strong>Intern's Position:</strong> {{ $evaluatee->acceptedInternship->job->title ?? 'N/A' }}</p>
                                <p><strong>Company:</strong> {{ $user->name }}</p>
                                <input type="hidden" name="evaluatee_id" value="{{ $evaluatee->id }}">
                            @elseif ($evaluation->evaluation_type === 'intern_company')
                                <h5 class="card-title">Internship Details</h5>
                                <p><strong>Company Name:</strong> {{ $user->acceptedInternship->job->company->name }}</p>
                                <p><strong>Location:</strong> {{ $user->acceptedInternship->job->company->profile->address ?? 'No Address Added' }}</p>
                                <div class="form-group mb-3">
                                    <label for="supervisor_name"><strong>Supervisor:</strong></label>
                                    <input type="text" name="supervisor_name" id="supervisor_name" class="form-control" required>
                                </div>
                            @endif

                            <h5 class="card-title">Evaluation Questions</h5>
                            
                            @foreach ($evaluation->questions as $question)
                                <div class="mb-3">
                                    <label for="question-{{ $question->id }}" class="form-label"><strong>{{ $question->question_text }}</strong></label>
                                    @if ($question->question_type == 'radio')
                                        <div class="row text-center mt-2">
                                            <div class="col">
                                                <label for="strongly-disagree-{{ $question->id }}">Strongly Disagree</label>
                                                <div>
                                                    <input type="radio" id="strongly-disagree-{{ $question->id }}" name="responses[{{ $question->id }}][response_value]" value="1">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label for="disagree-{{ $question->id }}">Disagree</label>
                                                <div>
                                                    <input type="radio" id="disagree-{{ $question->id }}" name="responses[{{ $question->id }}][response_value]" value="2">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label for="agree-{{ $question->id }}">Agree</label>
                                                <div>
                                                    <input type="radio" id="agree-{{ $question->id }}" name="responses[{{ $question->id }}][response_value]" value="3">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <label for="strongly-agree-{{ $question->id }}">Strongly Agree</label>
                                                <div>
                                                    <input type="radio" id="strongly-agree-{{ $question->id }}" name="responses[{{ $question->id }}][response_value]" value="4">
                                                </div>
                                            </div>
                                        </div>
                                    @elseif ($question->question_type == 'long_text')
                                        <textarea name="responses[{{ $question->id }}][response_text]" class="form-control mt-2"></textarea>
                                    @endif
                                </div>
                            @endforeach

                            <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            <a href="{{ route('evaluations.recipientIndex') }}" class="btn btn-secondary btn-sm">Cancel</a>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection
