<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Forgot Password</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">

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
                  <h5 class="card-title text-center pb-0 fs-4">Forgot Password?</h5>
                  <p class="text-center small">Enter your email to reset password</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="row g-3 needs-validation" novalidate>
                    @csrf

                    <!-- Email Address -->
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
                        <button class="btn btn-primary w-100" type="submit">{{ __('Email Password Reset Link') }}</button>
                    </div>

                    <div class="col-12 mt-4">
                        <p class="small mb-0">Back to <a href="{{ route('login') }}">Log in</a></p>
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

<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

</body>
</html>
