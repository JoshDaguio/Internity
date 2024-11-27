@extends('layouts.app')

@section('body')

    <div class="pagetitle">
        <h1>Program View</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item">Programs</li>
                <li class="breadcrumb-item active">{{ $course->course_code }}</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <!-- Back to List Button -->
    <a href="{{ route('courses.index') }}" class="btn btn-secondary mb-3 btn-sm">Back</a>

    <div class="row">

        <!-- First Section: Course Details -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Program Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Program Code:</strong> {{ $course->course_code }}
                                <a href="{{ route('courses.edit', $course) }}" class="btn btn-warning btn-sm ms-2"><i class="bi bi-pencil"></i></a>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Program Name:</strong> {{ $course->course_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Faculty:</strong> {{ $course->faculty_count }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Student:</strong> {{ $course->students_count }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- Second Section: Students Table and Graph -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Students in this Program</h5>
                    @if($students->isEmpty())
                        <p class="text-center text-muted">No students available for the current academic year.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>ID Number</th>
                                        <th>Last Name</th>
                                        <th>First Name</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                    <tr>
                                        <td>
                                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-light btn-sm">
                                                {{ $student->profile->id_number }}
                                            </a>
                                        </td>
                                        <td>{{ $student->profile->last_name }}</td>
                                        <td>{{ $student->profile->first_name }}</td>
                                        <td>{{ $course->course_code }}</td>
                                        <td>
                                            @if($student->status_id == 1)
                                                <span class="badge bg-success">Active</span>
                                            @elseif($student->status_id == 2)
                                                <span class="badge bg-success">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>


        <!-- Students Pie Chart -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body pb-0">
                    <h5 class="card-title">Student Distribution</h5>
                    <div id="studentsChartContainer" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const studentsData = @json($studentsChartData);

                            if (studentsData.length > 0) {
                                echarts.init(document.querySelector("#studentsChartContainer")).setOption({
                                    tooltip: {
                                        trigger: 'item'
                                    },
                                    legend: {
                                        top: '5%',
                                        left: 'center'
                                    },
                                    series: [{
                                        name: 'Students',
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
                                        data: studentsData
                                    }]
                                });
                            } else {
                                document.getElementById('studentsChartContainer').innerHTML = '<p class="text-center text-muted">No data available yet.</p>';
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>

    <!-- Third Section: Faculty Table and Graph -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Faculty in this Program</h5>
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>ID Number</th>
                                    <th>Last Name</th>
                                    <th>First Name</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($course->faculty->whereIn('status_id', [1, 2]) as $faculty)
                                <tr>
                                    <td>
                                        <a href="{{ route('faculty.show', $faculty->id) }}" class="btn btn-light btn-sm">
                                            {{ $faculty->profile->id_number }}
                                        </a>
                                    </td>
                                    <td>{{ $faculty->profile->last_name }}</td>
                                    <td>{{ $faculty->profile->first_name }}</td>
                                    <td>{{ $course->course_code }}</td>
                                    <!-- <td>{{ $faculty->profile->id_number }}</td> -->
                                    <!-- <td>{{ $faculty->email }}</td> -->
                                    <td>
                                    @if($faculty->status_id == 1)
                                        <span class="badge bg-success">Active</span>
                                    @elseif($faculty->status_id == 2)
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>      
                </div>
            </div>
        </div>

        <!-- Faculty Pie Chart -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body pb-0">
                    <h5 class="card-title">Faculty Distribution</h5>
                    <div id="facultyChartContainer" style="min-height: 400px;" class="echart"></div>

                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const facultyData = @json($facultyChartData);

                            if (facultyData.length > 0) {
                                echarts.init(document.querySelector("#facultyChartContainer")).setOption({
                                    tooltip: {
                                        trigger: 'item'
                                    },
                                    legend: {
                                        top: '5%',
                                        left: 'center'
                                    },
                                    series: [{
                                        name: 'Faculty',
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
                                        data: facultyData
                                    }]
                                });
                            } else {
                                document.getElementById('facultyChartContainer').innerHTML = '<p class="text-center text-muted">No data available yet.</p>';
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>


@endsection
