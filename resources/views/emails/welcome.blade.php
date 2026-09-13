<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Skill Marketplace</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 36px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 36px 30px;
        }
        .greeting {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
        }
        .btn {
            display: inline-block;
            background-color: #0284c7;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 12px;
            margin: 20px 0;
            text-align: center;
        }
        .feature-box {
            background-color: #f1f5f9;
            border-radius: 12px;
            padding: 18px;
            margin: 24px 0;
            border-left: 4px solid #0284c7;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Skill Marketplace</h1>
        </div>
        <div class="content">
            <h2 class="greeting">Welcome, {{ $user->name }}! 🎉</h2>
            <p>Your email address <strong>{{ $user->email }}</strong> has been successfully verified.</p>
            <p>You now have full access to connect with verified artisans, local professionals, and academic tutors across Nigeria.</p>
            
            <div class="feature-box">
                <strong style="color: #0f172a; display: block; margin-bottom: 6px;">Next Step: Complete Your Profile</strong>
                <span style="font-size: 13px; color: #475569;">Complete your profile to list your tutoring or trade services, or browse open job opportunities right in your neighborhood.</span>
            </div>

            <div style="text-align: center;">
                <a href="{{ url('/dashboard') }}" class="btn">Go to Your Dashboard →</a>
            </div>

            <p style="font-size: 13px; color: #64748b; margin-top: 30px;">If you have any questions or need support, reply directly to this email or reach out to our support team.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Skill Marketplace. All rights reserved. • Nigeria
        </div>
    </div>
</body>
</html>
