<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Lead Created</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f3f4f6;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .email-header p {
            color: #fecaca;
            font-size: 14px;
        }
        .email-body {
            padding: 30px;
        }
        .lead-card {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .lead-name {
            font-size: 20px;
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 15px;
        }
        .lead-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        .detail-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .detail-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 500;
        }
        .detail-value.empty {
            color: #9ca3af;
            font-style: italic;
        }
        .cta-section {
            text-align: center;
            margin-top: 20px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🚨 New Lead Created!</h1>
            <p>A new lead has been submitted through the API</p>
        </div>

        <div class="email-body">
            <div class="lead-card">
                <div class="lead-name">{{ $lead->full_name }}</div>
                
                <div class="lead-details">
                    <div class="detail-item">
                        <span class="detail-label">Email Address</span>
                        <span class="detail-value {{ empty($lead->email) ? 'empty' : '' }}">{{ $lead->email ?? 'Not provided' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone Number</span>
                        <span class="detail-value {{ empty($lead->phone) ? 'empty' : '' }}">{{ $lead->phone ?? 'Not provided' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Company</span>
                        <span class="detail-value {{ empty($lead->company) ? 'empty' : '' }}">{{ $lead->company ?? 'Not provided' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Lead Source</span>
                        <span class="detail-value {{ empty($lead->leadSource?->name) ? 'empty' : '' }}">{{ $lead->leadSource?->name ?? 'Website' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status</span>
                        <span class="detail-value">{{ ucfirst($lead->status) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Created At</span>
                        <span class="detail-value">{{ $lead->created_at->format('F j, Y \a\t g:i A') }}</span>
                    </div>
                </div>
            </div>

            @if($lead->notes)
            <div class="lead-card" style="background-color: #f9fafb; border-color: #e5e7eb;">
                <div class="detail-label" style="color: #374151; margin-bottom: 8px;">📝 Notes</div>
                <p style="color: #4b5563;">{{ $lead->notes }}</p>
            </div>
            @endif

            <div class="cta-section">
                <a href="{{ url('/admin/leads/' . $lead->id . '/edit') }}" class="cta-button">
                    View Lead Details →
                </a>
            </div>
        </div>

        <div class="email-footer">
            <p>This is an automated notification from The Market Magnetize.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
