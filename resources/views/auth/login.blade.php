<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Log In</title>
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
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
                  <span class="logo d-flex align-items-center w-auto">Internity</span>
                </a>
              </div>
              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username & password to login</p>
                  </div>
                  <x-auth-session-status class="mb-4" :status="session('status')" />
                  <form id="loginForm" method="POST" action="{{ route('login') }}" class="row g-3 needs-validation" novalidate>
                    @csrf
                    <div class="col-12">
                      <label for="email" class="form-label">{{ __('Email') }}</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">@</span>
                        <input type="email" name="email" class="form-control" id="email" :value="old('email')" required autofocus>
                        <div class="invalid-feedback">Please enter your email.</div>
                      </div>
                      <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>
                    <div class="col-12 mt-4">
                      <label for="password" class="form-label">{{ __('Password') }}</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">*</span>
                        <input type="password" name="password" class="form-control" id="password" required>
                        <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                          <i class="bi bi-eye-slash" id="togglePasswordIcon"></i>
                        </span>
                        <div class="invalid-feedback">Please enter your password!</div>
                      </div>
                      <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div class="col-12 mt-4">
                      @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                          {{ __('Forgot your password?') }}
                        </a>
                      @endif
                    </div>
                    <div class="col-12 mt-4">
                      <button class="btn btn-primary w-100" type="submit">{{ __('Log in') }}</button>
                    </div>
                    <div class="col-12 mt-4">
                      <p class="small mb-0">Student with no account? <a href="{{ route('register') }}">Register an account</a></p>
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
      const form = document.getElementById('loginForm');
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    })();

    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const togglePasswordIcon = document.querySelector('#togglePasswordIcon');

    togglePassword.addEventListener('click', function () {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);
      togglePasswordIcon.classList.toggle('bi-eye');
      togglePasswordIcon.classList.toggle('bi-eye-slash');
    });
  </script>
</body>

</html>
