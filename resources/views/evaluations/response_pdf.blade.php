<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Response</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        h2, h3 {
            color: #333;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 22px;
            font-weight: bold;
        }
        h3 {
            font-size: 18px;
            margin-top: 30px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .section {
            margin-bottom: 15px;
        }
        .user-details p, .responses p {
            margin: 8px 0;
            color: #444;
        }
        .user-details p strong {
            font-weight: bold;
            color: #333;
        }
        .responses p {
            margin: 10px 0;
        }
        .question-title {
            font-size: 15px;
            font-weight: bold;
            margin-top: 20px;
            color: #333;
        }
        .answer {
            margin-top: 5px;
            font-style: italic;
            padding-left: 15px;
        }
        .answer p {
            margin: 0;
        }
        hr {
            border: none;
            height: 1px;
            background-color: #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<!-- User Details Section -->
<div class="user-details section">
    @if ($user->role_id == 3)
        <p><strong>Faculty Name:</strong> {{ $user->profile->first_name }} {{ $user->profile->last_name }}</p>
        <p><strong>Faculty Course:</strong> {{ $user->course->course_name }}</p>
    @elseif ($user->role_id == 5)
        <p><strong>Student Name:</strong> {{ $user->profile->first_name }} {{ $user->profile->last_name }}</p>
        <p><strong>Course:</strong> {{ $user->course->course_name }}</p>
        <p><strong>Company:</strong> {{ $user->acceptedInternship->company->name }}</p>
        <p><strong>Job:</strong> {{ $user->acceptedInternship->job->title }}</p>
    @elseif ($user->role_id == 4)
        <p><strong>Company Name:</strong> {{ $user->name }}</p>
        <p><strong>Contact Person:</strong> {{ $user->profile->first_name }} {{ $user->profile->last_name }}</p>
    @endif


    @if ($evaluation->evaluation_type === 'intern_student' && $evaluatee)
        <p><strong>Intern Name:</strong> {{ $evaluatee->profile->first_name }} {{ $evaluatee->profile->last_name }}</p>
        <p><strong>Intern's Course:</strong> {{ $evaluatee->course->course_code ?? 'N/A' }}</p>
        <p><strong>Intern's Position:</strong> {{ $evaluatee->acceptedInternship->job->title ?? 'N/A' }}</p>
        <p><strong>Company:</strong> {{ $user->name }}</p>
    <!-- Supervisor Details for Intern Company Evaluations -->
    @elseif ($evaluation->evaluation_type === 'intern_company' && $evaluationResult)
        <p><strong>Supervisor:</strong> {{ $evaluationResult->supervisor }}</p>
    @endif
</div>


<!-- Evaluation Details -->
<div class="section">
    <h2>{{ $evaluation->title }}</h2>
    <p>{{ $evaluation->description }}</p>
</div>

<!-- Questions and Responses -->
<div class="section">
    <h3>Questions & Responses</h3>
    @foreach ($responses as $response)
        <p class="question-title">{{ $response->question->question_text }}</p>
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
            <p class="answer">Answer: <strong>{{ $ratingText }}</strong></p>
        @elseif ($response->question->question_type == 'long_text')
            <div class="answer">
                <p>{{ $response->response_text }}</p>
            </div>
        @endif
        <hr>
    @endforeach

    <!-- Total Score -->
    <div class="section">
        <h3>Total Score Given</h3>
        <p>{{ $totalScore }} / {{ $totalPossibleScore }}</p>
    </div>
</div>

</body>
</html>
