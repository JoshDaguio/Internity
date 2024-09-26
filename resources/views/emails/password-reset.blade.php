<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            background-color: #B30600;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: white; /* Text color white */
            text-align: center; /* Center-align the text */
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #333333;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #B30600;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #900400;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #aaaaaa;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Password Reset Request</h1>
        </div>
        <div class="content">
            <p>Hi there,</p>
            <p>You are receiving this email because we received a password reset request for your account.</p>
            
            <a href="{{ $url }}" class="button">Reset Password</a>

            <p>This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required.</p>
        </div>
        <div class="footer">
            <p>Thank you for using {{ config('app.name') }}!</p>
        </div>
    </div>

</body>
</html>
