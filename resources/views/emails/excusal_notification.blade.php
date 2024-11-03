<p>Dear Company Representative,</p>

<p>This is to notify you that intern <strong>{{ $student->profile->first_name }} {{ $student->profile->last_name }}</strong> has an excusal request for <strong>{{ \Carbon\Carbon::parse($request->absence_date)->format('F d, Y') }}</strong>, which has been approved by the admin.</p>

<p><strong>Reason for Excusal:</strong> {{ $reason }}</p>

@if($penaltyType)
    <p><strong>Penalty Applied:</strong> {{ ucfirst(str_replace('_', ' ', $penaltyType)) }}</p>
@endif

<p>Best regards,<br>Your Internship Management Team</p>
