@extends('layouts.app')
@section('body')
    <div class="pagetitle">
      <h1>Faculty Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">

    <!-- First Row -->
    <div class="row">

      <div class="col-md-3">
            <div class="card shadow-sm dashboard-info-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text mb-1">Course</div>
                        <div class="h5 mb-0">{{$faculty->course->course_code}}</div> 
                    </div>
                    <i class="bi bi-book"></i>
                </div>
            </div>
        </div> 
        <div class="col-md-3">
            <div class="card shadow-sm dashboard-info-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text mb-1">Active Student</div>
                        <div class="h5 mb-0">{{ $totalPopulation }}</div> <!-- Display number of active users -->
                    </div>
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm dashboard-info-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text mb-1">Active Internships</div>
                        <div class="h5 mb-0">{{ $activeInternshipsCount }}</div> <!-- Display number of accepted internships -->
                    </div>
                    <i class="bi bi-person-workspace"></i>
                </div>
            </div>
        </div> 
        
        <div class="col-md-3">
            <div class="card shadow-sm dashboard-info-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text mb-1">Current S.Y.</div>
                        <div class="h5 mb-0">{{ $schoolYear }}</div> <!-- Display current school year -->
                    </div>
                    <i class="bi bi-calendar3"></i>
                </div>
            </div>
        </div>

    </div>
    <!-- End First Row -->
     
  <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">

        <div class="row">
               
            <!-- Reports -->
            <div class="col-6">
              <div class="card">
                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                    <h5 class="card-title">Students with no DTR for Today</h5>

                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>DTR </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($studentsWithNoDTRToday as $student)
                                    <tr>
                                        <td>
                                            <a href="{{ route('students.show', $student->id) }}" class="btn btn-light btn-sm">
                                            {{ $student->profile->last_name }}, {{ $student->profile->first_name }}
                                            </a>
                                        </td>
                                        <td>{{ $student->course->course_code ?? 'N/A' }}</td>
                                        <td>
                                            <a href="{{ route('students.dtr', $student->id) }}" class="btn btn-success me-2 btn-sm {{ $student->status_id != 1 ? 'd-none' : '' }}">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No students to display.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div><!-- End Reports -->

            <div class="col-6">
              <div class="card">
              <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                    <h5 class="card-title">Students with No EOD for Today</h5>
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course</th>
                                <th>EOD</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($studentsWithNoEODToday as $student)
                                <tr>
                                    <td>
                                        <a href="{{ route('students.show', $student->id) }}" class="btn btn-light btn-sm">
                                            {{ $student->profile->last_name }}, {{ $student->profile->first_name }}
                                        </a>
                                    </td>
                                    <td>{{ $student->course->course_code }}</td>
                                    <td>
                                        <a href="{{ route('students.eod', $student->id) }}" class="btn btn-primary me-2 btn-sm {{ $student->status_id != 1 ? 'd-none' : '' }}">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">No students to display.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
              </div>
            </div><!-- EOD Reports -->

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Student Internship Status</span></h5>
                  <div class="table-responsive">

                    <table class="table datatable">
                      <thead>
                        <tr>
                          <th>Student</th>
                          <th>Course</th>
                          <th>Application</th>
                          <th>Internship</th>
                        </tr>
                      </thead>
                      <tbody>
                          @forelse($approvedStudents as $student)
                              <tr>
                                  <td>
                                      <a href="{{ route('students.show', $student->id) }}" class="btn btn-light btn-sm">
                                          {{ $student->profile->last_name }}, {{ $student->profile->first_name }}
                                      </a>
                                  </td>
                                  <td>
                                      {{ $student->course->course_code }}
                                  </td>
                                  <td>
                                        <span class="badge 
                                            @if($student->applicationStatus == 'Accepted') bg-success
                                            @elseif($student->applicationStatus == 'Pending') bg-warning
                                            @elseif($student->applicationStatus == 'Rejected') bg-danger
                                            @elseif($student->applicationStatus == 'For Interview') bg-primary
                                            @else bg-secondary
                                            @endif">
                                            {{ $student->applicationStatus }}
                                        </span>
                                    </td>
                                  <td>
                                      @if ($student->hasInternship)
                                          @if($student->remainingHours > 0)
                                              <p><span class="badge bg-warning"><strong>Internship Ongoing</strong></span></p>
                                          @else
                                              <p><span class="badge bg-success"><strong>Internship Hours Completed</strong></span></p>
                                          @endif
                                      @else
                                          <span class="badge bg-secondary">No Internship Yet</span>
                                      @endif
                                  </td>
                              </tr>
                          @empty
                              <tr>
                                  <td colspan="5">No students available.</td>
                              </tr>
                          @endforelse
                      </tbody>
                    </table>
                  </div>
                </div>

              </div>
            </div><!-- End Recent Sales -->

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">

          <div class="card">
              <div class="card-body">
                  <h5 class="card-title">Calendar</h5>
                  <div id="calendar"></div>
              </div>
          </div>

          <div class="card">
            <div class="card-body" style="min-height: 150px;">
              <h5 class="card-title">Pending Evaluation</h5>
              <div class="activity">
                  @forelse($pendingEvaluations as $evaluation)
                      <div class="activity-item d-flex">
                          <div class="activity-label">
                              {{ $evaluation->type_label }}
                          </div>
                          <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                          <div class="activity-content">
                            <a href="{{ route('evaluations.recipientIndex') }}" class="btn btn-light btn-sm">
                                {{ $evaluation->title }}
                            </a>
                          </div>
                      </div>
                  @empty
                      <p class="text-center"><strong>No pending evaluations.</strong></p>
                  @endforelse
              </div>
            </div>
          </div><!-- End Pending Evaluation -->

        </div><!-- End Right side columns -->

      </div>
                
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add event listeners to the student filter items
            document.querySelectorAll('.filter-item').forEach(function (item) {
                item.addEventListener('click', function (event) {
                    event.preventDefault();
                    let filter = event.target.getAttribute('data-filter');

                    fetch(`{{ route('super_admin.dashboard') }}?student_filter=${filter}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('total-students').textContent = data.totalStudents;
                        document.getElementById('student-percentage').textContent = `${data.studentPercentage}%`;
                        document.getElementById('selected-course').textContent = `| ${data.selectedStudentCourse}`;
                    })
                    .catch(error => console.error('Error:', error));
                });
            });

            // Add event listeners to the faculty filter items
            document.querySelectorAll('.filter-faculty-item').forEach(function (item) {
                item.addEventListener('click', function (event) {
                    event.preventDefault();
                    let filter = event.target.getAttribute('data-filter');

                    fetch(`{{ route('super_admin.dashboard') }}?faculty_filter=${filter}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        document.getElementById('total-faculty').textContent = data.totalFaculty;
                        document.getElementById('faculty-percentage').textContent = `${data.facultyPercentage}%`;
                        document.getElementById('selected-faculty-course').textContent = `| ${data.selectedFacultyCourse}`;
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });

        //Calendar Script
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'en-ph',
                headerToolbar: {
                    left: 'title',
                    center: '',
                    right: 'prev,next'
                },
                nowIndicator: true,
                now: '{{ \Carbon\Carbon::now("Asia/Manila")->format("Y-m-d") }}',
                height: 350, // Ensures the calendar fits within the card
                dateClick: function(info) {
                    alert('Clicked on: ' + info.dateStr);
                }
            });
            calendar.render();
        });

    </script>
    <style>
        #calendar {
            max-width: 100%;
            height: 450px; /* Adjust height to fit the whole month view */
            padding: 10px; 
            margin: 0 auto;
        }

        .fc {
            font-size: 0.75rem; /* Slightly reduce overall font size */
        }

        .fc-toolbar-title {
            font-size: 0.9rem; /* Reduce title size */
        }

        .fc-button {
            padding: 2px 6px; /* Make buttons smaller */
        }

        .fc-toolbar-chunk {
            flex: 0 0 auto; /* Prevents too much stretching */
        }

        .fc-daygrid-day-frame {
            padding: 2px; /* Adjust padding within day cells */
        }
    </style>

    </section>
@endsection