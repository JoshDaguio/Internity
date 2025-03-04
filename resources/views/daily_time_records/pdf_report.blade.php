<!DOCTYPE html>
<html>
<head>
    <title>DTR Monthly Report</title>
    <style>
        body { 
            font-family: sans-serif; 
        }
        h2{
            margin-top: 0;
            text-align: center;
        }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid black; padding: 5px; text-align: center; }
        h3,h5 { 
            margin-top: 0;
            text-align: center; 
        }
        p .schedule{
            font-size: 18px;
        }
        p .names{
            font-size: 20px;
        }
        .signature-container { 
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .signature-line { 
            display: table-cell;
            width: 45%; 
            text-align: center; 
            vertical-align: bottom; 
            padding: 0 20px;
        }

        .line { 
            border-top: 1px solid black; 
            width: 100%; 
            margin-top: 30px; 
        }
    </style>
</head>
<body>
    <h2>Daily Time Record Monthly Report</h2>

    <p class="names"><b>Name:</b> {{ $student->profile->first_name }} {{ $student->profile->last_name }}</p>
    <p class="names"><b>Company:</b> {{ $acceptedInternship->company->name }}</p>
    <p class="names"><b>Month:</b> {{ $month }}</p>
    <p class="names"><b>Total Worked Hours:</b> {{ $totalWorkedHours }} hours</p>

    <!-- <h5>Schedule:</h5>
    @php
        function formatTimeTo12Hour($time) {
            return \Carbon\Carbon::createFromFormat('H:i', $time)->format('g:i A');
        }
    @endphp
    @if ($student->profile->is_irregular && $customSchedule)
        @foreach ($customSchedule as $day => $times)
            <p class="schedule"><strong>{{ ucfirst($day) }}:</strong> {{ formatTimeTo12Hour($times['start']) }} - {{ formatTimeTo12Hour($times['end']) }}</p>
        @endforeach
    @else
        @foreach ($schedule['days'] as $day)
            <p class="schedule"><strong>{{ ucfirst($day) }}:</strong> {{ formatTimeTo12Hour($schedule['start_time']) }} - {{ formatTimeTo12Hour($schedule['end_time']) }}</p>
        @endforeach
    @endif -->
<!-- 
    <h3>Daily Time Record for {{ $month }}:</h3> -->
    <table>
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
                            <td colspan="4" class="text-danger text-center" style="color:red;">No Logs for This Day</td>
                        @endif
                    @else
                        <td colspan="4" class="text-center">No Internship Schedule for This Date</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Summary:</h4>
    <p class="names"><b>Total Absences:</b> {{ $totalAbsences }}</p>
    <p class="names"><b>Total Tardiness:</b> {{ $totalTardiness }}</p>
    <p class="names"><b>Total Makeup Hours:</b> {{ $totalMakeupHours }} hours</p>

    <div class="signature-container">
        <div class="signature-line">
            <div class="line"></div>
            <p><b>Supervisor</b></p>
        </div>
        <div class="signature-line">
            <div class="line"></div>
            <p><b>{{ $student->profile->first_name }} {{ $student->profile->last_name }} (Intern)</b></p>
        </div>
    </div>
</body>
</html>
