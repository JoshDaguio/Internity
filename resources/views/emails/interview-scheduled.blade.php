<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Interview Scheduled</title>
</head>
<body>
    <h2>Interview Scheduled for {{ $application->job->title }}</h2>

    <p>Dear {{ $application->student->profile->first_name }} {{ $application->student->profile->last_name }},</p>

    <p>Your application for the position of <strong>{{ $application->job->title }}</strong> at <strong>{{ $application->job->company->name }}</strong> has been processed, and you have been scheduled for an interview. Below are the details:</p>

    <h3>Interview Details:</h3>
    <ul>
        <li><strong>Interview Type:</strong> {{ $interview->interview_type }}</li>
        @if($interview->interview_type === 'Online' && $interview->interview_link)
            <li><strong>Interview Link:</strong> <a href="{{ $interview->interview_link }}" target="_blank">{{ $interview->interview_link }}</a></li>
        @endif
        <li><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($interview->interview_datetime)->format('F d, Y h:i A') }} (Philippine Time)</li>
        @if($interview->message)
            <li><strong>Additional Message:</strong> {{ $interview->message }}</li>
        @endif
    </ul>

    <p>Thank you for your interest in joining our team. We look forward to meeting you!</p>

    <p>Best regards,</p>
    <p><strong>{{ $application->job->company->name }}</strong></p>
</body>
</html>
