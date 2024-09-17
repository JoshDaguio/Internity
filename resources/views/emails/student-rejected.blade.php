<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Rejected</title>
</head>
<body>
    <h1>Dear {{ $application->student->profile->first_name }},</h1>
    <p>We regret to inform you that your application for the position of {{ $application->job->title }} has been rejected.</p>
    <p>We appreciate your interest in this internship, and we wish you the best of luck in your future endeavors.</p>
    <p>Thank you,<br>The {{ $application->job->company->name }} Team</p>
</body>
</html>
