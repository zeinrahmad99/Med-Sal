<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333333;
            margin-top: 0;
        }

        h2 {
            color: #555555;
        }

        p {
            color: #777777;
            margin-bottom: 20px;
        }

        .verification-code {
            font-size: 24px;
            color: #333333;
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .thank-you {
            color: #555555;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Email Verification</h1>
        <p>Thank you for registering. Please use the following verification code to confirm your email:</p>
        <div class="verification-code">{{ $verificationCode }}</div>
        <p class="thank-you">Thank you!</p>
    </div>
</body>
</html>