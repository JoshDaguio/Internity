<p>Dear {{ $student->profile->first_name }} {{ $student->profile->last_name }},</p>

<p>Your request with the subject "<strong>{{ $request->subject }}</strong>" for absence on <strong>{{ \Carbon\Carbon::parse($request->absence_date)->format('F d, Y') }}</strong> has been <strong>{{ ucfirst($status) }}</strong>.</p>

@if($status === 'approved')
    <p>The following penalty was applied: <strong>{{ ucfirst(str_replace('_', ' ', $penaltyType)) }}</strong>.</p>
@endif

<p>Admin Remarks: {{ $remarks }}</p>

<p>Best regards,<br>Your Internship Management Team</p>
