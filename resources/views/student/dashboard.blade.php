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
                  Before proceeding with the <strong>Internship Program</strong> please submit the following in <strong>Requirements</strong> tab. 
                        <br> 
                        <br>
                  <strong>Step 1:</strong>
                        <ul>
                            <li>Upload your <strong>Curriculum Vitae</strong> on your <strong>Profile</strong></li>      
                        </ul>       
                  <strong>Step 2:</strong>
                        <ul>
                            <li><strong>Endorsement Letter:</strong> To be uploaded by <strong>Coordinators</strong></li>
                        </ul>
                  <strong>Step 3:</strong>
                        <ul>
                            <li>Submit <strong>Waiver Form</strong>: (See <strong>Files</strong> Section)</li>      
                            <li>Submit <strong>Medical Certificate</strong></li>
                        </ul>
                        <br>
                        If <strong>Requirements</strong> are already complete, please apply on the <strong>Internship</strong> tab, and wait for your application to be processed.                 
                  </div>
                </div>
              </div>
            </div>
        </div>
      </div>
    @else
    
      <!-- Second Row -->
      <div class="row">
    </div>
    <!-- End Second Row -->

    <div class="row">
      <!-- Left side columns -->
      <div class="col-lg-8">
      <div class="row">
               
          <!-- Reports (DTR) -->
          <div class="col-12 col-md-6">
              <div class="card" style="max-height: 500px; min-height: 290px;">
                  <div class="card-body">
                      @if($hasSubmittedDTRToday)
                          <h5 class="card-title">Daily Time Record (DTR) - Submission Completed</h5>
                          <div class="alert alert-success">
                              <p><strong>Great job!</strong> You've already submitted your DTR for today.</p>
                              <p><a href="{{ route('dtr.reports', ['date' => $currentDate->format('Y-m-d')]) }}" class="btn btn-outline-primary btn-sm">View Submission</a></p>
                          </div>
                      @else
                          <h5 class="card-title">Daily Time Record (DTR) - Pending Submission</h5>
                          <div class="alert alert-warning">
                              <p><strong>Reminder:</strong> You haven’t submitted your DTR for today. Please log your hours to stay up-to-date.</p>
                              <p><a href="{{ route('dtr.index') }}" class="btn btn-primary btn-sm">Log DTR</a></p>
                          </div>
                      @endif
                  </div>
              </div>
          </div>

          <!-- EOD Reports -->
          <div class="col-12 col-md-6">
              <div class="card" style="max-height: 500px; min-height: 290px;">
                  <div class="card-body">
                      @if($hasSubmittedToday)
                          <h5 class="card-title">End of Day (EOD) Report - Submission Completed</h5>
                          <div class="alert alert-success">
                              <p><strong>Great job!</strong> You've already submitted your EOD report for today.</p>
                              <p><a href="{{ route('end_of_day_reports.compile.monthly') }}" class="btn btn-outline-primary btn-sm">View Reports</a></p>
                          </div>
                      @else
                          <h5 class="card-title">End of Day (EOD) Report - Pending Submission</h5>
                          <div class="alert alert-warning">
                              <p><strong>Reminder:</strong> You haven’t submitted your EOD report for today. Please complete it to keep your daily reports consistent.</p>
                              <p><a href="{{ route('end_of_day_reports.create') }}" class="btn btn-primary btn-sm">Create Report</a></p>
                          </div>
                      @endif
                  </div>
              </div>
          </div>


          <div class="col-md-12">
            <div class="card" style="height: 480px;">
              <div class="card-body" style="display: flex; flex-direction: column; height: 100%;">
                <h5 class="card-title">Internship Calendar</h5>
                <div id="calendar" style="flex-grow: 1; overflow-y: auto;"></div>
              </div>
            </div>
          </div>

          <div class="col-md-12">
            <div class="card mb-3" style="max-height: 480px; min-height: 480px;">
              <div class="card-body">
                  <h5 class="card-title">Total Hours Worked Per Month</h5>
                  <a href="{{ route('dtr.index') }}" class="btn btn-primary btn-sm mb-3"><i class="bi bi-clock"></i> Login</a>
                  <a href="{{ route('dtr.reports') }}" class="btn btn-success btn-sm mb-3"><i class="bi bi-graph-up-arrow"></i> Reports</a>

                  <canvas id="lineChart" style="max-height: 480px;"></canvas>
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
          </div>

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">
        <div class="card">
            <div class="card-body" style="min-height: 150px;">
              <h5 class="card-title">Your Pending Requests</h5>

              <div class="activity">
                  @forelse($pendingRequests as $request)
                      <div class="activity-item d-flex">
                          <div class="activity-label">
                            {{ \Carbon\Carbon::parse($request->absence_date)->format('M d') }}
                          </div>
                          <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                          <div class="activity-content">
                            <a href="{{ route('requests.studentIndex') }}" class="btn btn-light btn-sm">
                                {{ $request->subject }}
                            </a>
                          </div>
                      </div>
                  @empty
                      <p class="text-center"><strong>No pending request.</strong></p>
                  @endforelse
              </div>

            </div>
          </div><!-- End Pending Request -->

          <!-- Pending Requests -->
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

          <!-- Penalties Gained -->
          <div class="card">
            <div class="card-body" style="min-height: 200px;">
              <h5 class="card-title">Penalties Gained</h5>
              @forelse($penaltiesGained as $penalty)
                <table class="table">
                  <thead>
                      <tr>
                          <th>Penalty</th>
                          <th>Hours</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                        <td>{{ $penalty->penalty->violation }}</td>
                        <td>{{ $penalty->penalty_hours }} hrs</td>
                      </tr>
                </table>
                @empty
                    <p class="text-center"><strong>No penalties gained.</strong></p>
                @endforelse
              </div>
            </div>
            <!-- End Penalties Gained -->

        </div><!-- End Right side columns -->

      </div>


      <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'title',
                    center: '',
                    right: 'prev,next'
                },
                events: [
                    // Internship start date (green)
                    {
                        title: 'Start',
                        start: '{{ $startDate->format("Y-m-d") }}',
                        backgroundColor: 'green'
                    },
                    // Submitted reports (blue)
                    @foreach($reports as $report)
                    {
                        title: 'View',
                        start: '{{ $report->date_submitted->format("Y-m-d") }}',
                        backgroundColor: 'blue',
                        url: '{{ route("end_of_day_reports.show", $report->id) }}'
                    },
                    @endforeach
                    // Missing submissions (red), only if remaining hours
                    @if($remainingHours > 0)
                        @foreach($missingDates as $missingDate)
                        {
                            title: 'Missing',
                            start: '{{ \Carbon\Carbon::parse($missingDate)->format("Y-m-d") }}',
                            backgroundColor: '#B30600'
                        },
                        @endforeach
                    @endif
                    // Today's submission (yellow), only if scheduled and not submitted
                    @if(!$hasSubmittedToday && $isScheduledDay)
                    {
                        title: 'Submit',
                        start: '{{ $currentDate->format("Y-m-d") }}',
                        backgroundColor: '#FFD700',
                        url: '{{ route("end_of_day_reports.create") }}'
                    }
                    @endif
                ],
                validRange: {
                    start: '{{ $startDate->format("Y-m-d") }}',
                    end: '{{ \Carbon\Carbon::now()->addMonth(1)->startOfMonth()->format("Y-m-d") }}'
                },
                dateClick: function(info) {
                    var currentDate = '{{ $currentDate->format("Y-m-d") }}';
                    if (info.dateStr > currentDate || info.dateStr < '{{ $startDate->format("Y-m-d") }}') return false;
                    const allowedDays = {!! json_encode($scheduledDays) !!};
                    const clickedDay = new Date(info.dateStr).toLocaleDateString('en-US', { weekday: 'long' });
                    if (!allowedDays.includes(clickedDay)) {
                        alert("You can only submit reports on your scheduled days.");
                    }
                },
                nowIndicator: true,
                now: '{{ $currentDate->format("Y-m-d") }}'
            });

            calendar.render();
        });
    </script>

    <style>
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


    @endif


  </section>
@endsection