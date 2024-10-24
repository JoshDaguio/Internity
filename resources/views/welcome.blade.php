<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Internity</title>

    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link href="{{ asset('assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('niceadmin/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('niceadmin/assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            background-color: #B30600;
            font-family: 'Poppins', sans-serif;
        }
        .main-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 30px;
            max-width: 600px;
            margin: 100px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #B30600;
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        h1 img {
            width: 50px;
            height: 50px;
            vertical-align: middle;
            margin-right: 5px;
        }
        p {
            color: #555;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .btn-icon {
            font-size: 18px;
            color: #B30600;
            margin: 5px 15px;
            padding: 10px 20px;
            border-radius: 5px;
            border: 2px solid #B30600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-icon i {
            margin-right: 10px;
        }
        .btn-icon:hover {
            color: white;
            background-color: #B30600;
            border: 2px solid #B30600;
        }
        footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body class="antialiased">

    <div class="main-container">
        <h1>
            <img src="{{ asset('assets/img/logo-red.png') }}" alt="Logo">
            Internity
        </h1>
        <p>Internship Made Simple</p>
        @if (Route::has('login'))
            <div>
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-icon">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-icon">
                        <i class="fas fa-sign-in-alt"></i>
                        Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-icon">
                            <i class="fas fa-user-plus"></i>
                            Register
                        </a>
                    @endif
                @endauth
            </div>
        @endif
    </div>

    <footer>
        AUF CCS | Internity
    </footer>

    <!-- Vendor JS Files -->
    <script src="{{ asset('niceadmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('niceadmin/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('niceadmin/assets/vendor/quill/quill.min.js') }}"></script>
    <script src="{{ asset('niceadmin/assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('niceadmin/assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('niceadmin/assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('niceadmin/assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('niceadmin/assets/js/main.js') }}"></script>

</body>
</html>
