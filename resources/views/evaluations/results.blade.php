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

                    @foreach ($evaluation->questions as $question)
                        @if ($question->question_type == 'radio')
                            <div class="row">
                                <div class="col-md-12">
                                    <h5><strong>{{ $question->question_text }}</strong></h5>
                                </div>

                                <!-- Display the Population, Average, Weighted Mean, Remarks -->
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Population</th>
                                                <th>Average</th>
                                                <th>Weighted Mean</th>
                                                <th>Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $results[$question->id]['totalResponses'] }}</td>
                                                <td>{{ round($results[$question->id]['average'], 2) }}</td>
                                                <td>{{ $results[$question->id]['weightedMean'] }}</td>
                                                <td>{{ $results[$question->id]['remarks'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Bar Chart for Response Counts -->
                                <div class="col-lg-7">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Response Counts</h5>
                                            <div id="barChart_{{ $question->id }}" style="height: 250px;" class="echart"></div>
                                            <script>
                                                document.addEventListener("DOMContentLoaded", () => {
                                                    echarts.init(document.querySelector("#barChart_{{ $question->id }}")).setOption({
                                                        xAxis: {
                                                            type: 'category',
                                                            data: ['4 (Strongly Agree)', '3 (Agree)', '2 (Disagree)', '1 (Strongly Disagree)']
                                                        },
                                                        yAxis: {
                                                            type: 'value'
                                                        },
                                                        series: [{
                                                            data: [
                                                                {{ $results[$question->id]['responseCounts'][4] ?? 0 }},
                                                                {{ $results[$question->id]['responseCounts'][3] ?? 0 }},
                                                                {{ $results[$question->id]['responseCounts'][2] ?? 0 }},
                                                                {{ $results[$question->id]['responseCounts'][1] ?? 0 }}
                                                            ],
                                                            type: 'bar',
                                                            barWidth: '50%',
                                                            color: ['#28a745', '#17a2b8', '#ffc107', '#dc3545']  // Colors for 4, 3, 2, 1 respectively
                                                        }]
                                                    });
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pie Chart for Percentages -->
                                <div class="col-lg-5">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Percentage</h5>
                                            <div id="pieChart_{{ $question->id }}" style="height: 250px;" class="echart"></div>
                                            <script>
                                                document.addEventListener("DOMContentLoaded", () => {
                                                    new ApexCharts(document.querySelector("#pieChart_{{ $question->id }}"), {
                                                        series: [
                                                            {{ $results[$question->id]['percentages'][4] ?? 0 }},
                                                            {{ $results[$question->id]['percentages'][3] ?? 0 }},
                                                            {{ $results[$question->id]['percentages'][2] ?? 0 }},
                                                            {{ $results[$question->id]['percentages'][1] ?? 0 }}
                                                        ],
                                                        chart: {
                                                            height: 300,
                                                            type: 'pie',
                                                            toolbar: { show: false }  // Remove toolbar download option
                                                        },
                                                        labels: ['4 (Strongly Agree)', '3 (Agree)', '2 (Disagree)', '1 (Strongly Disagree)'],
                                                        colors: ['#28a745', '#17a2b8', '#ffc107', '#dc3545']  // Matching colors
                                                    }).render();
                                                });
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <!-- Text Responses Section -->
                    <h5 class="card-title">Text Responses</h5>
                    <div class="form-text">
                        @foreach ($evaluation->questions as $question)
                            @if ($question->question_type == 'long_text')
                                <h5><strong>{{ $question->question_text }}</strong></h5>
                                <ul>
                                    @foreach ($results[$question->id]['responses'] as $response)
                                        <li>{{ $response }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        @endforeach
                    </div>

                    <a href="{{ route('evaluations.downloadPDF', $evaluation->id) }}" class="btn btn-success btn-sm"><i class="bi bi-download"></i> PDF</a>
                    <a href="{{ route('evaluations.downloadExcel', $evaluation->id) }}" class="btn btn-success btn-sm"><i class="bi bi-download"></i> Excel</a>
                </div>
            </div>
        </div>
    </div>

@endsection
