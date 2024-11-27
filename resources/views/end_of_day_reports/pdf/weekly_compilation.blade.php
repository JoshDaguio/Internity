<!-- resources/views/end_of_day_reports/pdf/weekly_compilation.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $studentName }} - Weekly Report</title>
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
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin-top: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
            color: #333;
        }
    </style>
</head>
<body>

    <h2>{{ $studentName }}</h2>
    <h1>Weekly Report: {{ \Carbon\Carbon::parse($startOfWeek)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($endOfWeek)->format('F d, Y') }}</h1>

    @if($reports->isNotEmpty())
        @foreach($reports as $report)
            <h4>{{ \Carbon\Carbon::parse($report->submission_for_date)->format('F d, Y') }} Report</h4>
            <h4>Date Submitted: {{ \Carbon\Carbon::parse($report->date_submitted)->format('F d, Y') }} | Late: {{ $report->is_late ? 'Yes' : 'No' }}</h4>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Task Description</th>
                        <th>Time Spent</th>
                        <th>Key Successes</th>
                        <th>Main Challenges</th>
                        <th>Plans for Tomorrow</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->tasks as $index => $task)
                        <tr>
                            <td>{{ $task->task_description }}</td>
                            <td>{{ $task->time_spent }} {{ $task->time_unit }}</td>
                            @if($index === 0)
                                <td rowspan="{{ count($report->tasks) }}">{{ $report->key_successes }}</td>
                                <td rowspan="{{ count($report->tasks) }}">{{ $report->main_challenges }}</td>
                                <td rowspan="{{ count($report->tasks) }}">{{ $report->plans_for_tomorrow }}</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <br>

        @endforeach

    @else
        <p>No reports submitted for this week.</p>
    @endif

    <!-- Display missing submissions -->
    @if(!$missingDates->isEmpty())
        <h3>Missing Submissions for this week:</h3>
        <ul>
            @foreach($missingDates as $missingDate)
                <li>{{ \Carbon\Carbon::parse($missingDate)->format('F d, Y') }}</li>
            @endforeach
        </ul>
    @else
        <p>All submissions are up-to-date for this week.</p>
    @endif

</body>
</html>
