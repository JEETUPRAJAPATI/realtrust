<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Template</title>
    <style>
        /* Inline styles for email compatibility */
        body {
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .email-header img {
            max-width: 100%;
            height: auto;
        }
        .email-body {
            padding: 20px;
            text-align: center;
        }
        .email-body h1 {
            color: #333333;
        }
        .email-body p {
            color: #555555;
            font-size: 16px;
            line-height: 1.5;
        }
        .btn-verify {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 4px;
        }
        .email-footer {
            text-align: center;
            padding: 10px;
            background-color: #f1f1f1;
            border-top: 1px solid #e0e0e0;
            color: #666666;
            font-size: 14px;
        }
        .email-footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header with Logo -->
        <div class="email-header">
            <img src="https://real-trust-rose.vercel.app/assets/Real%20Trust%20Logo-Es3DpLkB.jpg" alt="Logo">
        </div>

        <!-- Body -->
        <div class="email-body">
            <h1>Hello <?= $user ?></h1>
            <p>Congratulations! You just created a new business.</p>

            <h1>Hello <?= $timing ?></h1>
            <a href="[Verification Link]" class="btn-verify">Verify Email</a>
            <p>If you don't know why you got this email, please get in touch with us <a href="[Contact Link]">here</a>.</p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p>Best regards,<br><?= $user ?></p>
        </div>
    </div>
</body>
</html>
