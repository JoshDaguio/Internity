<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Register</title>
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
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
    .card-body, .card-title, .text-center {
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
              </div>
              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Register Account</h5>
                    <p class="text-center small">Enter your student details to register account</p>
                  </div>
                  <form id="registrationForm" method="POST" action="{{ route('register') }}" class="row g-3 needs-validation" novalidate>
                    @csrf
                    <div class="col-12">
                        <label for="first_name" class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" id="first_name" value="{{ old('first_name') }}" required autofocus>
                        <div class="invalid-feedback">Please enter your first name!</div>
                    </div>
                    <div class="col-12 mt-4">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" id="last_name" value="{{ old('last_name') }}" required>
                        <div class="invalid-feedback">Please enter your last name!</div>
                    </div>

                    <div class="col-12 mt-4">
                        <label for="id_number" class="form-label">ID Number</label>
                        <input type="text" name="id_number" class="form-control @error('id_number') is-invalid @enderror" 
                              id="id_number" placeholder="00-0000-000" required pattern="\d{2}-\d{4}-\d{3}" 
                              value="{{ old('id_number') }}">
                        <div class="invalid-feedback">
                            @error('id_number') 
                                {{ $message }} 
                            @else 
                                Please enter your ID number! Format: 00-0000-000
                            @enderror
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <label for="email_username" class="form-label">School Email</label>
                        <div class="input-group">
                            <input type="text" name="email_username" class="form-control @error('email_username') is-invalid @enderror" 
                                  id="email_username" placeholder="lastname.firstname" pattern="^[a-zA-Z]+\.[a-zA-Z]+$" 
                                  value="{{ old('email_username') }}" required>
                            <span class="input-group-text">@auf.edu.ph</span>
                            <div class="invalid-feedback">
                                @error('email_username') 
                                    {{ $message }}
                                @else
                                    Please enter your school email in the format lastname.firstname@auf.edu.ph!
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <label for="course_id" class="form-label">Course</label>
                        <select name="course_id" class="form-select @error('course_id') is-invalid @enderror" id="course_id" required>
                            <option value="" disabled {{ old('course_id') ? '' : 'selected' }}>Select your course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_code }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">
                            @error('course_id') 
                                {{ $message }}
                            @else
                                Please select your course!
                            @enderror
                        </div>
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
  </main>

  <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script>
    (function () {
      'use strict';
      const form = document.getElementById('registrationForm');
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    })();
  </script>
</body>

</html>
