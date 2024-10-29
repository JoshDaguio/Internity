@extends('layouts.app')
@section('body')
    <div class="pagetitle">
      <h1>Super Admin Dashboard</h1>
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

        <div class="col-md-4">
            <div class="card shadow-sm dashboard-info-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text mb-1">Active Users</div>
                        <div class="h5 mb-0">{{ $totalActiveUsers }}</div> <!-- Display number of active users -->
                    </div>
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm dashboard-info-card">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text mb-1">Active Internships</div>
                        <div class="h5 mb-0">{{ $totalAcceptedInternships }}</div> <!-- Display number of accepted internships -->
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
     
  <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">

        <div class="row">
              <!-- Admin Card -->
              <div class="col-6">
                  <div class="card info-card admins-card" style="min-height: 100px;">
                      <div class="card-body">
                          <h5 class="card-title">Admin</h5>
                          <div class="d-flex align-items-center">
                              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                  <i class="bi bi-shield-lock"></i>
                              </div>
                              <div class="ps-3">
                                  <h6>{{ $totalAdmins }}</h6>
                                  <span class="text-primary small pt-1 fw-bold">{{ $adminPercentage }}%</span>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <!-- End Admin Card -->

              <!-- Faculty Card -->
              <div class="col-6">
                  <div class="card info-card faculty-card" style="min-height: 100px;">
                      <div class="filter">
                          <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                              <li class="dropdown-header text-start">
                                  <h6>Filter</h6>
                              </li>
                              <li><a class="dropdown-item filter-faculty-item" data-filter="all">All</a></li>
                              @foreach($courses as $course)
                                  <li><a class="dropdown-item filter-faculty-item" data-filter="{{ $course->id }}">{{ $course->course_code }}</a></li>
                              @endforeach
                          </ul>
                      </div>
                      <div class="card-body">
                          <h5 class="card-title">Faculty <span id="selected-faculty-course">| {{ $selectedFacultyCourse }}</span></h5>
                          <div class="d-flex align-items-center">
                              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                  <i class="bi bi-person-badge"></i>
                              </div>
                              <div class="ps-3">
                                  <h6 id="total-faculty">{{ $totalFaculty }}</h6>
                                  <span id="faculty-percentage" class="text-primary small pt-1 fw-bold">{{ $facultyPercentage }}%</span>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <!-- End Faculty Card -->

              <!-- Company Card -->
              <div class="col-6">
                  <div class="card info-card company-card" style="min-height: 100px;">
                      <div class="card-body">
                          <h5 class="card-title">Company</h5>
                          <div class="d-flex align-items-center">
                              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                  <i class="bi bi-building"></i>
                              </div>
                              <div class="ps-3">
                                  <h6>{{ $totalCompanies }}</h6>
                                  <span class="text-primary small pt-1 fw-bold">{{ $companyPercentage }}%</span>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <!-- End Company Card -->

              <!-- Student Card -->
              <div class="col-6">
                  <div class="card info-card student-card" style="min-height: 100px;">
                      <div class="filter">
                          <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                              <li class="dropdown-header text-start">
                                  <h6>Filter</h6>
                              </li>
                              <li><a class="dropdown-item filter-item" data-filter="all">All</a></li>
                              @foreach($courses as $course)
                                  <li><a class="dropdown-item filter-item" data-filter="{{ $course->id }}">{{ $course->course_code }}</a></li>
                              @endforeach
                          </ul>
                      </div>
                      <div class="card-body">
                          <h5 class="card-title">Student <span id="selected-course">| {{ $selectedStudentCourse }}</span></h5>
                          <div class="d-flex align-items-center">
                              <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                  <i class="bi bi-mortarboard"></i>
                              </div>
                              <div class="ps-3">
                                  <h6 id="total-students">{{ $totalStudents }}</h6>
                                  <span id="student-percentage" class="text-primary small pt-1 fw-bold">{{ $studentPercentage }}%</span>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <!-- End Student Card -->
               
            <!-- Reports -->
            <div class="col-12">
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
                  <h5 class="card-title">Reports <span>/Today</span></h5>

                  <!-- Line Chart -->
                  <div id="reportsChart"></div>

                  <script>
                    document.addEventListener("DOMContentLoaded", () => {
                      new ApexCharts(document.querySelector("#reportsChart"), {
                        series: [{
                          name: 'Sales',
                          data: [31, 40, 28, 51, 42, 82, 56],
                        }, {
                          name: 'Revenue',
                          data: [11, 32, 45, 32, 34, 52, 41]
                        }, {
                          name: 'Customers',
                          data: [15, 11, 32, 18, 9, 24, 11]
                        }],
                        chart: {
                          height: 350,
                          type: 'area',
                          toolbar: {
                            show: false
                          },
                        },
                        markers: {
                          size: 4
                        },
                        colors: ['#4154f1', '#2eca6a', '#ff771d'],
                        fill: {
                          type: "gradient",
                          gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.3,
                            opacityTo: 0.4,
                            stops: [0, 90, 100]
                          }
                        },
                        dataLabels: {
                          enabled: false
                        },
                        stroke: {
                          curve: 'smooth',
                          width: 2
                        },
                        xaxis: {
                          type: 'datetime',
                          categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z", "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z", "2018-09-19T06:30:00.000Z"]
                        },
                        tooltip: {
                          x: {
                            format: 'dd/MM/yy HH:mm'
                          },
                        }
                      }).render();
                    });
                  </script>
                  <!-- End Line Chart -->

                </div>

              </div>
            </div><!-- End Reports -->

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
                  <h5 class="card-title">Recent Sales <span>| Today</span></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Product</th>
                        <th scope="col">Price</th>
                        <th scope="col">Status</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row"><a href="#">#2457</a></th>
                        <td>Brandon Jacob</td>
                        <td><a href="#" class="text-primary">At praesentium minu</a></td>
                        <td>$64</td>
                        <td><span class="badge bg-success">Accepted</span></td>
                      </tr>
                      <tr>
                        <th scope="row"><a href="#">#2147</a></th>
                        <td>Bridie Kessler</td>
                        <td><a href="#" class="text-primary">Blanditiis dolor omnis similique</a></td>
                        <td>$47</td>
                        <td><span class="badge bg-warning">To Review</span></td>
                      </tr>
                      <tr>
                        <th scope="row"><a href="#">#2049</a></th>
                        <td>Ashleigh Langosh</td>
                        <td><a href="#" class="text-primary">At recusandae consectetur</a></td>
                        <td>$147</td>
                        <td><span class="badge bg-success">Accepted</span></td>
                      </tr>
                      <tr>
                        <th scope="row"><a href="#">#2644</a></th>
                        <td>Angus Grady</td>
                        <td><a href="#" class="text-primar">Ut voluptatem id earum et</a></td>
                        <td>$67</td>
                        <td><span class="badge bg-danger">Rejected</span></td>
                      </tr>
                      <tr>
                        <th scope="row"><a href="#">#2644</a></th>
                        <td>Raheem Lehner</td>
                        <td><a href="#" class="text-primary">Sunt similique distinctio</a></td>
                        <td>$165</td>
                        <td><span class="badge bg-success">Accepted</span></td>
                      </tr>
                    </tbody>
                  </table>

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

          <!-- Recent Activity -->
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
              <h5 class="card-title">Recent Activity <span>| Today</span></h5>

              <div class="activity">

                <div class="activity-item d-flex">
                  <div class="activite-label">32 min</div>
                  <i class='bi bi-circle-fill activity-badge text-success align-self-start'></i>
                  <div class="activity-content">
                    Quia quae rerum <a href="#" class="fw-bold text-dark">explicabo officiis</a> beatae
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">56 min</div>
                  <i class='bi bi-circle-fill activity-badge text-danger align-self-start'></i>
                  <div class="activity-content">
                    Voluptatem blanditiis blanditiis eveniet
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">2 hrs</div>
                  <i class='bi bi-circle-fill activity-badge text-primary align-self-start'></i>
                  <div class="activity-content">
                    Voluptates corrupti molestias voluptatem
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">1 day</div>
                  <i class='bi bi-circle-fill activity-badge text-info align-self-start'></i>
                  <div class="activity-content">
                    Tempore autem saepe <a href="#" class="fw-bold text-dark">occaecati voluptatem</a> tempore
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">2 days</div>
                  <i class='bi bi-circle-fill activity-badge text-warning align-self-start'></i>
                  <div class="activity-content">
                    Est sit eum reiciendis exercitationem
                  </div>
                </div><!-- End activity item-->

                <div class="activity-item d-flex">
                  <div class="activite-label">4 weeks</div>
                  <i class='bi bi-circle-fill activity-badge text-muted align-self-start'></i>
                  <div class="activity-content">
                    Dicta dolorem harum nulla eius. Ut quidem quidem sit quas
                  </div>
                </div><!-- End activity item-->

              </div>

            </div>
          </div><!-- End Recent Activity -->

          <!-- Budget Report -->
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

            <div class="card-body pb-0">
              <h5 class="card-title">Budget Report <span>| This Month</span></h5>

              <div id="budgetChart" style="min-height: 400px;" class="echart"></div>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  var budgetChart = echarts.init(document.querySelector("#budgetChart")).setOption({
                    legend: {
                      data: ['Allocated Budget', 'Actual Spending']
                    },
                    radar: {
                      // shape: 'circle',
                      indicator: [{
                          name: 'Sales',
                          max: 6500
                        },
                        {
                          name: 'Administration',
                          max: 16000
                        },
                        {
                          name: 'Information Technology',
                          max: 30000
                        },
                        {
                          name: 'Customer Support',
                          max: 38000
                        },
                        {
                          name: 'Development',
                          max: 52000
                        },
                        {
                          name: 'Marketing',
                          max: 25000
                        }
                      ]
                    },
                    series: [{
                      name: 'Budget vs spending',
                      type: 'radar',
                      data: [{
                          value: [4200, 3000, 20000, 35000, 50000, 18000],
                          name: 'Allocated Budget'
                        },
                        {
                          value: [5000, 14000, 28000, 26000, 42000, 21000],
                          name: 'Actual Spending'
                        }
                      ]
                    }]
                  });
                });
              </script>

            </div>
          </div><!-- End Budget Report -->

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