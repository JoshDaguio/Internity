<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $studentName }} - {{ \Carbon\Carbon::create()->month($selectedMonth)->format('F') }} {{ $currentYear }} - Monthly Report</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        h1 {
            text-align: center;
            color: #B30600;
            font-size: 24px;
        }
        h2 {
            text-align: left;
            color: black;
            margin-top: 5px;
            font-size: 16px;
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color:rgb(112, 112, 112);
            color: #fff;
            width: 30%;
        }
        .table-secondary {
            background-color: #f2f2f2;
            font-weight: bold;
            width: 30%;
        }
        .page-break {
            page-break-before: always;
        }
        .late{
            color: #B30600;
            font-weight: bold;
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
    @if(!$reports->isEmpty())
        @foreach($reports as $report)
            <div class="page-break"></div>
            <h1>{{ \Carbon\Carbon::create()->month($selectedMonth)->format('F') }} {{ $currentYear }} - Monthly Report</h1>

            <h2>Name: {{ $studentName }}</h2>
            <h2>Company: {{ $acceptedInternship->company->name }}</h2>
            <h2>Course: {{ $user->course->course_code }}</h2>            
            <h4>{{ \Carbon\Carbon::parse($report->submission_for_date)->format('F d, Y') }} Report</h4>
            <h4>Date Submitted: {{ \Carbon\Carbon::parse($report->date_submitted)->format('F d, Y') }}</h4>
            
            <!-- Tasks Completed Table -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Task Completed</th>
                            <th>Time Spent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($report->tasks as $task)
                            @php
                                $hours = floor($task->time_spent / 60);
                                $minutes = $task->time_spent % 60;
                                $displayTime = ($hours > 0 ? $hours . ' hour(s) ' : '') . ($minutes > 0 ? $minutes . ' minute(s)' : '');
                            @endphp
                            <tr>
                                <td>{{ $task->task_description }}</td>
                                <td>{{ $displayTime }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Additional Report Information -->
            <table class="table mt-4">
                <tr>
                    <th class="table-secondary">Successes:</th>
                    <td>{{ $report->key_successes }}</td>
                </tr>
                <tr>
                    <th class="table-secondary">Challenges:</th>
                    <td>{{ $report->main_challenges }}</td>
                </tr>
                <tr>
                    <th class="table-secondary">Plans:</th>
                    <td>{{ $report->plans_for_tomorrow }}</td>
                </tr>
                <tr>
                    <th class="table-secondary">Submission:</th>
                    <td>{{ \Carbon\Carbon::parse($report->submission_for_date)->format('F d, Y') }}</td>
                </tr>
                <tr>
                    <th class="table-secondary">Submitted:</th>
                    <td>{{ \Carbon\Carbon::parse($report->date_submitted)->format('F d, Y') }}</td>
                </tr>
                @if($report->is_late)
                <tr>
                    <th class="table-secondary">Late:</th>
                    <td class="late">Yes</td>
                </tr>
                @endif
            </table>

            <div class="signature-container">
                <div class="signature-line">
                    <div class="line"></div>
                    <p><b>Ma. Joyce V. Macaspac, MIT</b></p>
                </div>
                <div class="signature-line">
                    <div class="line"></div>
                    <p><b>{{ $studentName }}</b></p>
                </div>
            </div>
        @endforeach
    @else
        <p>No reports submitted for this month.</p>
    @endif
    
    <!-- Display Missing Submissions -->
    @if(!$missingDates->isEmpty())
        <h3>Missing Submissions for this month:</h3>
        <ul>
            @foreach($missingDates as $missingDate)
                <li>{{ \Carbon\Carbon::parse($missingDate)->format('F d, Y') }}</li>
            @endforeach
        </ul>
    @else
        <p>All submissions are up-to-date for this month.</p>
    @endif
</body>
</html>
