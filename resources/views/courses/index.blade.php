@extends('layouts.app')

@section('body')

<div class="pagetitle">
    <h1>Courses</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Courses</li>
        </ol>
    </nav>
</div><!-- End Page Title -->

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<a href="{{ route('courses.create') }}" class="btn btn-primary mb-3">Add Course</a>

<div class="row">
    <!-- Course List -->
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Course List</h5>
                <div class="table-responsive">
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th scope="col">Course Code</th>
                                <th scope="col">Course Name</th>
                                <th scope="col">Faculty</th>
                                <th scope="col">Students</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($courses as $course)
                            <tr>
                                <td>
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-light btn-sm">
                                        {{ $course->course_code }}
                                    </a>
                                </td>
                                <td>{{ $course->course_name }}</td>
                                <td>{{ $course->faculty_count }}</td>
                                <td>{{ $course->students_count }}</td>
                                <td>
                                    <!-- <a href="{{ route('courses.show', $course) }}" class="btn btn-info btn-sm"><i class="bi bi-info-circle"></i></a> -->
                                    <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart for Course Population -->
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body pb-0">
                <h5 class="card-title">Course Population</h5>
                <div id="coursePopulationChartContainer" style="min-height: 400px;" class="echart"></div>

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        const coursePopulationData = @json($coursePopulationData);

                        if (coursePopulationData.length > 0) {
                            echarts.init(document.querySelector("#coursePopulationChartContainer")).setOption({
                                tooltip: {
                                    trigger: 'item'
                                },
                                legend: {
                                    top: '5%',
                                    left: 'center'
                                },
                                series: [{
                                    name: 'Course Population',
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
                                    data: coursePopulationData.map(data => ({
                                        value: data.population,
                                        name: data.course
                                    }))
                                }]
                            });
                        } else {
                            document.getElementById('coursePopulationChartContainer').innerHTML = '<p class="text-center text-muted">No data available yet.</p>';
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</div>

@endsection
