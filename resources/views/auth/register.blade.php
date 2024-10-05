<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Register</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

  <style>
    body {
      background-color: #B30600;
    }

    .logo span {
      color: white;
      font-family: 'Poppins', sans-serif;
      font-size: 40px;
    }

    .card-body,
    .card-title,
    .text-center {
      color: black;
    }

    .btn-primary {
      background-color: #B30600;
      border-color: #B30600;
    }

    .btn-primary:hover {
      background-color: #900400;
      border-color: #900400;
    }

    a {
      color: #B30600;
    }

    a:hover {
      color: #900400;
    }
  </style>
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a class="logo d-flex align-items-center w-auto">
                  <span class="d-none d-lg-block">Internity</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Register Account</h5>
                    <p class="text-center small">Enter your student details to register account</p>
                  </div>

                  <form method="POST" action="{{ route('register') }}" class="row g-3 needs-validation" novalidate>
                      @csrf

                      <!-- First Name -->
                      <div class="col-12">
                          <label for="first_name" class="form-label">First Name</label>
                          <div class="input-group has-validation">
                              <input type="text" name="first_name" class="form-control" id="first_name" :value="old('first_name')" required autofocus autocomplete="first_name">
                              <div class="invalid-feedback">Please enter your first name!</div>
                          </div>
                          <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                      </div>

                      <!-- Last Name -->
                      <div class="col-12 mt-4">
                          <label for="last_name" class="form-label">Last Name</label>
                          <div class="input-group has-validation">
                              <input type="text" name="last_name" class="form-control" id="last_name" :value="old('last_name')" required autocomplete="last_name">
                              <div class="invalid-feedback">Please enter your last name!</div>
                          </div>
                          <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                      </div>

                      <!-- ID Number -->
                      <div class="col-12 mt-4">
                          <label for="id_number" class="form-label">ID Number</label>
                          <div class="input-group has-validation">
                              <input type="text" name="id_number" class="form-control" id="id_number" :value="old('id_number')" required autocomplete="id_number" placeholder="00-0000-000">
                              <div class="invalid-feedback">Please enter your ID number! Format: 00-0000-000</div>
                          </div>
                          <x-input-error :messages="$errors->get('id_number')" class="mt-2" />
                      </div>

                      <!-- Email Address -->
                      <div class="col-12 mt-4">
                          <label for="email" class="form-label">School Email</label>
                          <div class="input-group has-validation">
                              <input type="text" name="email_username" class="form-control" id="email_username" placeholder="lastname.firstname" value="{{ old('email_username') }}" required>
                              <span class="input-group-text">@auf.edu.ph</span>
                              <div class="invalid-feedback">Please enter your school email in the format lastname.firstname@auf.edu.ph!</div>
                          </div>
                          <x-input-error :messages="$errors->get('email_username')" class="mt-2" />
                      </div>

                      <!-- Course -->
                      <div class="col-12 mt-4">
                          <label for="course_id" class="form-label">Course</label>
                          <div class="input-group has-validation">
                              <select name="course_id" class="form-select" id="course_id" required>
                                  <option value="" disabled selected>Select your course</option>
                                  @foreach($courses as $course)
                                      <option value="{{ $course->id }}">{{ $course->course_code }}</option>
                                  @endforeach
                              </select>
                              <div class="invalid-feedback">Please select your course!</div>
                          </div>
                          <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                      </div>

                      <div class="col-12 mt-4">
                          <button class="btn btn-primary w-100" type="submit">Register</button>
                      </div>

                      <div class="col-12 mt-4">
                          <p class="small mb-0">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
                      </div>
                  </form>

                </div>
              </div>
            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
  <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
  <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
  <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
  <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

</body>

</html>
