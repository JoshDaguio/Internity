<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Results PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid black;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Results for {{ $evaluation->title }}</h2>

    <h4>Detailed Statistical Results:</h4>
    <table>
        <thead>
            <tr>
                <th>Question</th>
                <th>Response Counts (4, 3, 2, 1)</th>
                <th>Percentage (%)</th>
                <th>Population</th>
                <th>Average</th>
                <th>Weighted Mean</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($evaluation->questions as $question)
                @if ($question->question_type == 'radio')
                    <tr>
                        <td>{{ $question->question_text }}</td>
                        <td>
                            4: {{ $results[$question->id]['responseCounts'][4] ?? 0 }} |
                            3: {{ $results[$question->id]['responseCounts'][3] ?? 0 }} |
                            2: {{ $results[$question->id]['responseCounts'][2] ?? 0 }} |
                            1: {{ $results[$question->id]['responseCounts'][1] ?? 0 }}
                        </td>
                        <td>
                            4: {{ $results[$question->id]['percentages'][4] ?? 0 }}% |
                            3: {{ $results[$question->id]['percentages'][3] ?? 0 }}% |
                            2: {{ $results[$question->id]['percentages'][2] ?? 0 }}% |
                            1: {{ $results[$question->id]['percentages'][1] ?? 0 }}%
                        </td>
                        <td>{{ $results[$question->id]['totalResponses'] }}</td>
                        <td>{{ round($results[$question->id]['average'], 2) }}</td>
                        <td>{{ $results[$question->id]['weightedMean'] }}</td>
                        <td>{{ $results[$question->id]['remarks'] }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <h4>Text Responses (Comments/Suggestions):</h4>
    @foreach ($evaluation->questions as $question)
        @if ($question->question_type == 'long_text')
            <h5>{{ $question->question_text }}</h5>
            <ul>
                @foreach ($results[$question->id]['responses'] as $response)
                    <li>{{ $response }}</li>
                @endforeach
            </ul>
        @endif
    @endforeach
</body>
</html>
