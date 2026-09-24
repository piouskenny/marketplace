<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Opportunity Match — Skill Link NG</title>
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
        .badge-category {
            display: inline-block;
            background-color: rgba(255,255,255,0.15);
            color: #38bdf8;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 36px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
        }
        .job-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            margin: 24px 0;
        }
        .job-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 12px 0;
        }
        .meta-grid {
            display: table;
            width: 100%;
            margin-bottom: 16px;
        }
        .meta-item {
            display: table-cell;
            font-size: 13px;
            color: #475569;
            padding-right: 12px;
        }
        .meta-label {
            font-weight: 700;
            color: #0f172a;
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .meta-val {
            font-weight: 600;
            color: #0284c7;
        }
        .description-box {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 16px;
            font-size: 14px;
            color: #334155;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background-color: #0f172a;
            color: #ffffff !important;
            text-decoration: none;
            padding: 14px 28px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 12px;
            margin: 20px 0;
            text-align: center;
        }
        .btn:hover {
            background-color: #1e293b;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
        .unsubscribe-link {
            color: #64748b;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Skill Link NG</h1>
            <span class="badge-category">{{ $opportunity->category->name ?? 'New Opportunity Match' }}</span>
        </div>
        <div class="content">
            <h2 class="greeting">Hello {{ $user->name }},</h2>
            <p>A new job opportunity matching your professional profile and skills was just published on <strong>Skill Link NG</strong>.</p>
            
            <div class="job-card">
                <h3 class="job-title">{{ $opportunity->title }}</h3>
                
                <div class="meta-grid">
                    <div class="meta-item">
                        <span class="meta-label">Location</span>
                        <span class="meta-val">{{ $opportunity->location }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Budget</span>
                        <span class="meta-val">{{ $budgetText }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Type</span>
                        <span class="meta-val">{{ ucfirst($opportunity->opportunity_type) }}</span>
                    </div>
                </div>

                @if($opportunity->educationDetails)
                    <div style="background-color: #e0f2fe; border-radius: 8px; padding: 10px 14px; margin-bottom: 16px; font-size: 12px; color: #0369a1;">
                        <strong>Tutoring Details:</strong> 
                        {{ optional($opportunity->educationDetails->subject)->name ?? 'General Subject' }} 
                        @if($opportunity->educationDetails->educationLevel)
                            • {{ $opportunity->educationDetails->educationLevel->name }}
                        @endif
                        @if($opportunity->educationDetails->teaching_mode)
                            • {{ ucfirst($opportunity->educationDetails->teaching_mode->value ?? $opportunity->educationDetails->teaching_mode) }} Mode
                        @endif
                    </div>
                @endif

                <div class="description-box">
                    <strong>Opportunity Overview:</strong><br>
                    {{ Str::limit($opportunity->description, 280) }}
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ $opportunityUrl }}" class="btn">View Opportunity & Apply →</a>
            </div>

            <p style="font-size: 12px; color: #64748b; margin-top: 24px; text-align: center;">
                Connect safely through Skill Link NG. Client contact information remains protected until a connection request is accepted.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Skill Link NG. All rights reserved. • Nigeria<br>
            You are receiving this email because your profile matches open job requests.<br>
            <a href="{{ $unsubscribeUrl }}" class="unsubscribe-link">Unsubscribe from job alert emails</a>
        </div>
    </div>
</body>
</html>
