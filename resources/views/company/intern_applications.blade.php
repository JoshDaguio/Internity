@extends('layouts.app')

@section('body')
<div class="container">
    <h1>Internship Applications</h1>

    <div class="row">
        <!-- Internship Listings -->
        <div class="col-md-9">
            <table class="table">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Industry</th>
                        <th>Date Posted</th>
                        <th>Applicants</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                    <tr>
                        <td>{{ $job->title }}</td>
                        <td>{{ $job->industry }}</td>
                        <td>{{ $job->created_at->format('M d, Y') }}</td>
                        <td>{{ $job->applications_count }}</td>
                        <td>
                            <a href="{{ route('company.jobApplications', $job->id) }}" class="btn btn-info">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pie Chart for Applicants -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Number of Applicants</h5>
                    <canvas id="applicantsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Ensure Chart.js is loaded -->

    <script>
        const ctx = document.getElementById('applicantsChart').getContext('2d');
        const applicantsData = @json($applicantsData);
        
        // Debugging data
        console.log(applicantsData); // Check the data in the browser console

        // Proceed only if there is data
        if (applicantsData.length > 0) {
            const chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: applicantsData.map(data => data.job_title),
                    datasets: [{
                        label: 'Number of Applicants',
                        data: applicantsData.map(data => data.applicants_count),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                }
            });
        } else {
            console.log("No applicants data available for the chart.");
        }
    </script>
</div>
@endsection
