<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribed from Job Alerts — Skill Link NG</title>
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
            max-width: 550px;
            margin: 60px auto;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
        }
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 32px 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
        }
        .content {
            padding: 36px 30px;
        }
        .icon-box {
            width: 56px;
            height: 56px;
            background-color: #fef2f2;
            color: #ef4444;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 12px;
            margin-top: 20px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Skill Link NG</h1>
        </div>
        <div class="content">
            <div class="icon-box">✓</div>
            <h2 class="title">Successfully Unsubscribed</h2>
            <p>You will no longer receive automated email alerts for new job opportunities.</p>
            <p style="font-size: 13px; color: #64748b;">
                You can manage your email notification preferences at any time in your Skill Link NG Profile Settings.
            </p>

            <a href="{{ url('/profile/edit') }}" class="btn">Manage Profile Settings →</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Skill Link NG. All rights reserved.
        </div>
    </div>
</body>
</html>
