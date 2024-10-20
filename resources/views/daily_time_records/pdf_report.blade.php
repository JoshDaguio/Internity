<!DOCTYPE html>
<html>
<head>
    <title>Internship Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid black; padding: 8px; text-align: center; }
        h2, h3 { margin-top: 0; }
    </style>
</head>
<body>
    <h2>Internship Report</h2>

    <h3>Intern Name: {{ $student->profile->first_name }} {{ $student->profile->last_name }}</h3>
    <h3>Company Name: {{ $acceptedInternship->company->name }}</h3>
    <h3>Month: {{ $month }}</h3>

    <h3>Schedule:</h3>
    @php
        function formatTimeTo12Hour($time) {
            return \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
        }
    @endphp
    @if ($student->profile->is_irregular && $customSchedule)
        @foreach ($customSchedule as $day => $times)
            <p><strong>{{ ucfirst($day) }}:</strong> {{ formatTimeTo12Hour($times['start']) }} - {{ formatTimeTo12Hour($times['end']) }}</p>
        @endforeach
    @else
        @foreach ($schedule['days'] as $day)
            <p><strong>{{ ucfirst($day) }}:</strong> {{ formatTimeTo12Hour($schedule['start_time']) }} - {{ formatTimeTo12Hour($schedule['end_time']) }}</p>
        @endforeach
    @endif

    <h3>Daily Time Record for {{ $month }}:</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Day</th>
                <th>Morning In</th>
                <th>Morning Out</th>
                <th>Afternoon In</th>
                <th>Afternoon Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filteredDates as $date)
                @php
                    $dayOfWeek = $date->format('l');
                    $record = $dailyRecords->where('log_date', $date->format('Y-m-d'))->first();
                    $logTimes = $record ? json_decode($record->log_times, true) : [];
                @endphp

                <tr>
                    <td>{{ $date->format('M d') }}</td>
                    <td>{{ $dayOfWeek }}</td>

                    @if (in_array($dayOfWeek, $scheduledDays))
                        @if ($record)
                            <td>{{ $logTimes['morning_in'] ?? 'Not Logged' }}</td>
                            <td>{{ $logTimes['morning_out'] ?? 'Not Logged' }}</td>
                            <td>{{ $logTimes['afternoon_in'] ?? 'Not Logged' }}</td>
                            <td>{{ $logTimes['afternoon_out'] ?? 'Not Logged' }}</td>
                        @else
                            <td colspan="4" class="text-danger text-center">No Logs for This Day</td>
                        @endif
                    @else
                        <td colspan="4" class="text-center">No Internship Schedule for This Date</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Summary:</h3>
    <p>Total Absences: {{ $totalAbsences }}</p>
    <p>Total Tardiness: {{ $totalTardiness }}</p>
    <p>Total Makeup Hours: {{ $totalMakeupHours }} hours</p>
</body>
</html>
