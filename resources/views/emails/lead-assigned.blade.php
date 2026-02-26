<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Lead Assigned</title>
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
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
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
            color: #c7d2fe;
            font-size: 14px;
        }
        .email-body {
            padding: 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        .lead-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .lead-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .lead-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin-right: 15px;
        }
        .lead-name {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        .lead-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 4px;
        }
        .status-new { background-color: #dbeafe; color: #1d4ed8; }
        .status-contacted { background-color: #fef3c7; color: #d97706; }
        .status-qualified { background-color: #ede9fe; color: #7c3aed; }
        .status-won { background-color: #d1fae5; color: #059669; }
        .status-lost { background-color: #fee2e2; color: #dc2626; }

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

        .assigned-info {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .assigned-info-title {
            font-size: 14px;
            font-weight: 600;
            color: #166534;
            margin-bottom: 8px;
        }
        .assigned-info p {
            font-size: 14px;
            color: #15803d;
        }

        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #4338ca 0%, #6d28d9 100%);
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
            <h1>🎯 New Lead Assigned</h1>
            <p>You have been assigned a new lead to follow up</p>
        </div>

        <div class="email-body">
            <div class="greeting">
                Hello {{ $assignedUser->name }},
            </div>

            <p style="margin-bottom: 20px; color: #4b5563;">
                A new lead has been assigned to you by <strong>{{ $assignedBy->name }}</strong>. Please review the details below and take necessary action.
            </p>

            <div class="lead-card">
                <div class="lead-card-header">
                    <div class="lead-avatar">
                        {{ substr($lead->first_name, 0, 1) }}{{ $lead->last_name ? substr($lead->last_name, 0, 1) : '' }}
                    </div>
                    <div>
                        <div class="lead-name">{{ $lead->full_name }}</div>
                        <span class="lead-status status-{{ $lead->status }}">{{ ucfirst($lead->status) }}</span>
                    </div>
                </div>

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
                        <span class="detail-label">Job Title</span>
                        <span class="detail-value {{ empty($lead->job_title) ? 'empty' : '' }}">{{ $lead->job_title ?? 'Not provided' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Lead Source</span>
                        <span class="detail-value {{ empty($lead->leadSource?->name) ? 'empty' : '' }}">{{ $lead->leadSource?->name ?? 'Not specified' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Deal Value</span>
                        <span class="detail-value {{ empty($lead->value) ? 'empty' : '' }}">
                            @if($lead->value)
                                ${{ number_format($lead->value, 2) }}
                            @else
                                Not specified
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Lead Score</span>
                        <span class="detail-value">{{ $lead->score }}/100</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Country</span>
                        <span class="detail-value {{ empty($lead->country) ? 'empty' : '' }}">{{ $lead->country ?? 'Not specified' }}</span>
                    </div>
                </div>
            </div>

            @if($lead->notes)
            <div class="assigned-info" style="background-color: #fef3c7; border-color: #fcd34d;">
                <div class="assigned-info-title" style="color: #92400e;">📝 Notes</div>
                <p style="color: #b45309;">{{ $lead->notes }}</p>
            </div>
            @endif

            @if($lead->follow_up_date)
            <div class="assigned-info">
                <div class="assigned-info-title">📅 Follow-up Date</div>
                <p>{{ $lead->follow_up_date->format('F j, Y \a\t g:i A') }}</p>
            </div>
            @endif

            <div style="text-align: center; margin-top: 25px;">
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
