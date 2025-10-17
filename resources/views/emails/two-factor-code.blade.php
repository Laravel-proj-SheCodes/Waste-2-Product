<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0d6efd; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f8f9fa; padding: 20px; border: 1px solid #dee2e6; }
        .code-box { background-color: white; border: 2px solid #0d6efd; padding: 20px; text-align: center; margin: 20px 0; border-radius: 5px; }
        .code { font-size: 32px; font-weight: bold; color: #0d6efd; letter-spacing: 5px; }
        .footer { background-color: #e9ecef; padding: 15px; text-align: center; font-size: 12px; color: #666; border-radius: 0 0 5px 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Two-Factor Authentication</h2>
        </div>
        <div class="content">
            <p>Hello {{ $user->name }},</p>
            <p>You requested a two-factor authentication code. Use the code below to verify your identity:</p>
            
            <div class="code-box">
                <div class="code">{{ $code }}</div>
            </div>

            <p><strong>This code will expire in 10 minutes.</strong></p>
            <p>If you didn't request this code, please ignore this email and your account will remain secure.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
