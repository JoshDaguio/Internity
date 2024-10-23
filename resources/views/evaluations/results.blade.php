@extends('layouts.app')

@section('body')
    <div class="pagetitle">
        <h1>Evaluation Results</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Evaluations</li>
                <li class="breadcrumb-item active">Results</li>
            </ol>
        </nav>
    </div>

    <a href="{{ route('evaluations.index') }}" class="btn btn-secondary mb-3">Back</a>


    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">{{ $evaluation->title }}</h5>
                    <div class="form-text">
                        <p><strong>{{ $evaluation->description }}</strong></p>
                    </div>

                    <h5 class="card-title">Detailed Statistical Results:</h5>
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
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

                    <h5 class="card-title">Text Responses ({{ $question->question_text }})</h5>
                    <div class="form-text">
                        @foreach ($evaluation->questions as $question)
                            @if ($question->question_type == 'long_text')
                                <ul>
                                    @foreach ($results[$question->id]['responses'] as $response)
                                        <li>{{ $response }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        @endforeach
                    </div>

                <a href="{{ route('evaluations.downloadPDF', $evaluation->id) }}" class="btn btn-success btn-sm"><i class="bi bi-download"></i> Generate PDF</a>
                </div>
            </div>
        </div>
    </div>

@endsection
