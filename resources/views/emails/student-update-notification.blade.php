<!DOCTYPE html>
<html>
<head>
    <title>Student Account Updated</title>
</head>
<body>
    <h1>Your Student Account Has Been Updated!</h1>

    <p>Hello {{ $name }},</p>

    <p>Your student account has been updated. Here are the updated details:</p>

    @if(in_array('email', $updatedFields))
        <p><strong>New Email:</strong> {{ $email }}</p>
    @endif

    @if(in_array('password', $updatedFields))
        <p><strong>New Password:</strong> {{ $newPassword }}</p> <!-- Show the new auto-generated password -->
    @endif

    <p>Thank you!</p>
</body>
</html>
