<!DOCTYPE html>
<html>
<head>
    <title>Student Evaluation Scores</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
        h2, h3 { color: #333; margin-bottom: 10px; }
        h2 { font-size: 22px; font-weight: bold; }
        h3 { font-size: 18px; margin-top: 30px; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        .section { margin-bottom: 15px; }
        .responses p { margin: 10px 0; }
        .question-title { font-size: 15px; font-weight: bold; margin-top: 20px; color: #333; }
        .answer { margin-top: 5px; font-style: italic; padding-left: 15px; }
        .answer p { margin: 0; }
        hr { border: none; height: 1px; background-color: #ddd; margin: 20px 0; }
    </style>
</head>
<body>

<!-- Student Details Section -->
<div class="section">
    <p><strong>Student Name:</strong> {{ $student->profile->first_name }} {{ $student->profile->last_name }}</p>
    <p><strong>Course:</strong> {{ $student->course->course_name }}</p>
</div>

<!-- Company Details Section -->
@if($company)
    <div class="section">
        <p><strong>Company Name:</strong> {{ $company->name }}</p>
        <p><strong>Contact Person:</strong> {{ $company->profile->first_name }} {{ $company->profile->last_name }}</p>
        <p><strong>Email:</strong> {{ $company->email }}</p>
    </div>
@endif

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
