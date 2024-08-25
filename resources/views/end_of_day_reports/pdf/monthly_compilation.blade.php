<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $studentName }} - {{ \Carbon\Carbon::create()->month($currentMonth)->format('F') }} {{ $currentYear }} - Monthly Report</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        h1 {
            text-align: center;
            color: #B30600;
            font-size: 24px; /* Adjusted font size */
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

    <h2>{{ $studentName }}</h2> <!-- Left-aligned, black color -->
    <h1>{{ \Carbon\Carbon::create()->month($currentMonth)->format('F') }} {{ $currentYear }} - Monthly Report</h1>

    @foreach($reports as $report)
        <h4>Date Submitted: {{ \Carbon\Carbon::parse($report->date_submitted)->format('F d, Y') }}</h4>
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
                        @if($index === 0) <!-- Only display these columns for the first task in the report -->
                            <td rowspan="{{ count($report->tasks) }}">{{ $report->key_successes }}</td>
                            <td rowspan="{{ count($report->tasks) }}">{{ $report->main_challenges }}</td>
                            <td rowspan="{{ count($report->tasks) }}">{{ $report->plans_for_tomorrow }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>
