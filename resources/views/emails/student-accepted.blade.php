<!DOCTYPE html>
<html>
<head>
    <title>Internship Acceptance</title>
</head>
<body>
    <h1>Congratulations {{ $application->student->profile->first_name }} {{ $application->student->profile->last_name }}!</h1>
    <p>Your application for the {{ $application->job->title }} position at {{ $application->job->company->name }} has been accepted.</p>
    <p>Your internship will start on <strong>{{ \Carbon\Carbon::parse($start_date)->format('F d, Y') }}</strong>.</p>
    
    <p><strong>Work Type:</strong> {{ $work_type }}</p>
    <p><strong>Schedule:</strong> {{ implode(', ', $schedule) }}</p>
    <p><strong>Time:</strong> 
        {{ \Carbon\Carbon::parse($start_time)->format('g:i A') }} 
        - 
        {{ \Carbon\Carbon::parse($end_time)->format('g:i A') }}
    </p>

    <p>Best of luck in your internship!</p>
    <p>Regards,<br>{{ $application->job->company->name }}</p>
</body>
</html>
