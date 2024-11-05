@extends('layouts.app')
@section('body')
  <div class="pagetitle">
      <h1>Student Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

  <section class="section dashboard">

    <!-- First Row -->
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm dashboard-info-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text mb-1">Course</div>
                        <div class="h5 mb-0">{{$student->course->course_code}}</div> 
                    </div>
                    <i class="bi bi-book"></i>
                </div>
            </div>
        </div> 

        <div class="col-md-4">
            <div class="card shadow-sm dashboard-info-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text mb-1">Current Date & Time</div>
                        <div class="h5 mb-0">
                          <iframe src="https://free.timeanddate.com/clock/i9mdm14j/n145/fn14/fs17/ftb/tt0/tw1/tm1/th2/tb2" frameborder="0" width="280" height="22"></iframe>
                        </div> <!-- Display number of accepted internships -->
                    </div>
                    <i class="bi bi-briefcase"></i>
                </div>
            </div>
        </div> 
        
        <div class="col-md-4">
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

    @if(isset($noInternship) && $noInternship)
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body">
                <h5 class="card-title">Internship Prerequisite Requirements</h5>
                <div class="mb-3">
                  <div class="form-text">
                  <strong>Step 1:</strong>
                        <ul>
                            <li>Submit <strong>Waiver Form:</strong> (See <strong>Files</strong> Section)</li>      
                            <li>Submit <strong>Medical Certificate</strong></li>
                        </ul>
                  <strong>Step 2:</strong>
                        <ul>
                            <li><strong>Prerequisite:</strong> Accomplish <strong>Step 1</strong> Requirements</li>      
                            <li><strong>Endorsement Letters:</strong> To be uploaded by <strong>Coordinators</strong></li>
                        </ul>
                        <br>
                        Before proceeding with the Application and Internship Process please submit the following in <strong>Requirements</strong> tab. 
                        <br>
                        <br>
                        If <strong>Requirements</strong> are already accepted, please apply on the <strong>Internship</strong> tab, and wait for your application to be processed.                 
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    @else
      <!-- Second Row -->
      <div class="row">
        <div class="col-md-6">
          <div class="card mb-3" style="max-height: 485px; min-height: 485px;">
            <div class="card-body">
                <h5 class="card-title">Total Hours Worked Per Month</h5>
                <a href="{{ route('dtr.index') }}" class="btn btn-primary btn-sm mb-3"><i class="bi bi-clock"></i> Login</a>
                <a href="{{ route('dtr.reports') }}" class="btn btn-success btn-sm mb-3"><i class="bi bi-graph-up-arrow"></i> Reports</a>

                <canvas id="lineChart" style="max-height: 485px;"></canvas>
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        new Chart(document.querySelector('#lineChart'), {
                            type: 'line',
                            data: {
                                labels: {!! json_encode(array_keys($monthlyHours)) !!},
                                datasets: [{
                                    label: 'Total Hours Worked',
                                    data: {!! json_encode(array_values($monthlyHours)) !!},
                                    fill: false,
                                    borderColor: 'rgb(75, 192, 192)',
                                    tension: 0.1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Hours Worked'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Months'
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
            </div>
          </div>
        </div><!-- EOD Reports -->

        <div class="col-md-6">
          <div class="card">
              <div class="card-body">
                  <h5 class="card-title">Calendar</h5>
                  <div id="calendar"></div>
              </div>
          </div>
        </div>
    </div>
    <!-- End Second Row -->

    <div class="row">
      <!-- Left side columns -->
      <div class="col-lg-8">
      <div class="row">
               
            <!-- Reports -->
            <div class="col-6">
              <div class="card" style="max-height: 500; min-height: 200px;">
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
                    <h5 class="card-title">No DTR For Today</h5>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
            </div><!-- End Reports -->

            <div class="col-6">
              <div class="card" style="max-height: 500; min-height: 200px;">
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
                    <h5 class="card-title">No EOD for Today</h5>
                    <table class="table">
                        <thead>
                        </thead>
                        <tbody>
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

              </div>
            </div><!-- End Recent Sales -->

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">
          <!-- Pending Requests -->
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Pending Evaluation</h5>

              <div class="activity">
                  @forelse($pendingEvaluations as $evaluation)
                      <div class="activity-item d-flex">
                          <div class="activity-label">
                              {{ $evaluation->type_label }}
                          </div>
                          <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                          <div class="activity-content">
                              {{ $evaluation->title }}
                          </div>
                      </div>
                  @empty
                      <p class="text-center"><strong>No pending evaluations.</strong></p>
                  @endforelse
              </div>


            </div>
          </div><!-- End Recent Activity -->

        </div><!-- End Right side columns -->

      </div>

    @endif
    

    <script>
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
                height: 400, // Ensures the calendar fits within the card
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