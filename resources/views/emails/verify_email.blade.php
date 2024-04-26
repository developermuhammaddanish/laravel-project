<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body>
    <h2>Verify Your Email Address</h2>
    <p>Thank you for registering. Please confirm your OTP to verify your email address:</p>
    <h2>{{ $user->otp }}</h2>
    <p>If you did not request this verification, you can safely ignore this email.</p>
</body>
</html>
