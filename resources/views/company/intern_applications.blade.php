@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Applications</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Internsships</li>
                <li class="breadcrumb-item active">Applications</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <div class="row">
        <!-- Internship Listings Table -->
        <div class="col-lg-8">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Internship Listings</h5>
              <div class="table-responsive">
                <!-- Table with DataTables formatting -->
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Industry</th>
                            <th>Date Posted</th>
                            <th>Applicants</th>
                            <th>Accepted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobs as $job)
                        <tr>
                            <td>
                                <a href="{{ route('company.jobApplications', $job->id) }}" class="btn btn-light btn-sm">
                                    {{ $job->title }}
                                </a>
                            </td>
                            <td>{{ $job->industry }}</td>
                            <td>{{ $job->created_at->format('M d, Y') }}</td>
                            <td>{{ $job->nonAcceptedApplicationsCount() }}</td>
                            <td>{{ $job->acceptedApplicantsCount() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- End of table -->
                </div>
            </div>
          </div>
        </div>
        
        <!-- Responsive Pie Chart for Applicants -->
        <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body pb-0">
                            <h5 class="card-title">Number of Applicants</h5>
                            <div id="applicantsChartContainer" style="min-height: 400px;" class="echart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const applicantsData = @json($applicantsData);

                                    if (applicantsData.length > 0) {
                                        echarts.init(document.querySelector("#applicantsChartContainer")).setOption({
                                            tooltip: {
                                                trigger: 'item'
                                            },
                                            legend: {
                                                top: '5%',
                                                left: 'center'
                                            },
                                            series: [{
                                                name: 'Applicants',
                                                type: 'pie',
                                                radius: ['40%', '70%'],
                                                avoidLabelOverlap: false,
                                                label: {
                                                    show: false,
                                                    position: 'center'
                                                },
                                                emphasis: {
                                                    label: {
                                                        show: true,
                                                        fontSize: '18',
                                                        fontWeight: 'bold'
                                                    }
                                                },
                                                labelLine: {
                                                    show: false
                                                },
                                                data: applicantsData.map(data => ({
                                                    value: data.applicants_count,
                                                    name: data.job_title
                                                }))
                                            }]
                                        });
                                    } else {
                                        document.getElementById('applicantsChartContainer').innerHTML = '<p class="text-center text-muted">No applicants data available yet.</p>';
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
@endsection
