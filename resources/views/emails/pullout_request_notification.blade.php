<!DOCTYPE html>
<html>
<head>
    <title>Pullout Request Notification</title>
</head>
<body>
    <h2>Pullout Request</h2>
    <p>You have a new pullout request from {{ $pullout->creator->name }} for the following students:</p>
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
