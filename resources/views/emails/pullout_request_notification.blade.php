<!DOCTYPE html>
<html>
<head>
    <title>Leave Request Notification</title>
</head>
<body>
    <h2>Leave Request</h2>
    <p>You have a new leave request from {{ $pullout->creator->name }} for the following students:</p>
    <ul>
        @foreach ($pullout->students as $student)
            <li>{{ $student->profile->first_name }} {{ $student->profile->last_name }}</li>
        @endforeach
    </ul>
    <p>Date: {{ $pullout->pullout_date->format('F d, Y') }}</p>
    <p>Reason: {{ $pullout->excuse_detail }}</p>
    <p>Please respond to this request at your earliest convenience.</p>
</body>
</html>
