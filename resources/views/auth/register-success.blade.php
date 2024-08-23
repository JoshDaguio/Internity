<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Registration Successful</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('niceadmin/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('niceadmin/assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
            text-align: center;
        }
        h1 {
            color: #B30600;
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            font-size: 18px;
            margin-bottom: 15px;
        }
        p.red-text {
            color: #555;
        }
        p.red-text .email {
            color: red;
        }
        .btn-icon {
            font-size: 15px;
            color: white;
            background-color: #B30600;
            margin-top: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            border: 2px solid #B30600;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-icon:hover {
            background-color: #900400;
            border-color: #900400;
        }
        footer {
            position: fixed;
            bottom: 10px;
            width: 100%;
            text-align: center;
            color: #B30600;
        }
    </style>
</head>
<body class="antialiased">

    <h1>Thank you for registering with us!</h1>
    <p class="red-text">Your account request has been successfully submitted. Please await an <span class="email">email</span> confirmation regarding the approval of your account registration.</p>
    <p>We appreciate your patience and look forward to having you onboard soon!</p>
    <a href="{{ route('login') }}" class="btn-icon">
        Back to Login
    </a>

    <footer>
        AUF CCS
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
