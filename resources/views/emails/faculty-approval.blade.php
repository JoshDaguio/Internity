<!DOCTYPE html>
<html>
<head>
    <title>Faculty Account Created</title>
</head>
<body>
    <h1>Welcome to Internity CCS!</h1>

    <p>Hello {{ $name }},</p>

    <p>Your faculty account has been created. Here are your login details:</p>
    <ul>
        <li><strong>Email:</strong> {{ $email }}</li>
        <li><strong>Password:</strong> {{ $password }}</li>
    </ul>

    <p>Your assigned course: {{ $course }}</p>

    <p>We recommend that you change your password after logging in.</p>

    <p>Thank you!</p>
</body>
</html>
