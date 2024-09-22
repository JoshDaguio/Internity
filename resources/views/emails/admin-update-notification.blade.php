<!DOCTYPE html>
<html>
<head>
    <title>Admin Account Updated</title>
</head>
<body>
    <h1>Your Admin Account Has Been Updated!</h1>

    <p>Hello {{ $name }},</p>

    <p>Your admin account has been updated. Here are the updated details:</p>

    @if(in_array('email', $updatedFields))
        <p><strong>New Email:</strong> {{ $email }}</p>
    @endif

    @if(in_array('password', $updatedFields))
        <p><strong>New Password:</strong> {{ $newPassword }}</p>
    @endif

    <p>Thank you!</p>
</body>
</html>
