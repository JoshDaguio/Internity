<!DOCTYPE html>
<html>
<head>
    <title>Internship Acceptance</title>
</head>
<body>
    <h1>Congratulations {{ $application->student->profile->first_name }}!</h1>
    <p>You have been accepted as an intern for the position of {{ $application->job->title }} at {{ $application->job->company->name }}.</p>
    <p>Best of luck in your internship!</p>
    <p>Regards,<br>{{ $application->job->company->name }}</p>
</body>
</html>
