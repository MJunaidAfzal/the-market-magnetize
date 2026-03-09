<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Assigned</title>
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
            background: linear-gradient(135deg, #059669 0%, #0891b2 100%);
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
            color: #a7f3d0;
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
        .order-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .order-avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #059669 0%, #0891b2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin-right: 15px;
        }
        .order-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        .order-number {
            font-size: 14px;
            color: #6b7280;
        }
        .order-details {
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
            font-size: 15px;
            color: #1f2937;
            font-weight: 500;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-confirmed { background-color: #dbeafe; color: #1e40af; }
        .status-in-progress { background-color: #fef3c7; color: #92400e; }
        .status-on-hold { background-color: #fee2e2; color: #991b1b; }
        .status-completed { background-color: #d1fae5; color: #065f46; }
        .status-cancelled { background-color: #f3f4f6; color: #374151; }

        .priority-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }
        .priority-low { background-color: #f3f4f6; color: #374151; }
        .priority-medium { background-color: #dbeafe; color: #1e40af; }
        .priority-high { background-color: #fee2e2; color: #991b1b; }
        .priority-urgent { background-color: #991b1b; color: #ffffff; }

        .assigned-info {
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        .assigned-info p {
            font-size: 14px;
            color: #065f46;
            margin-bottom: 5px;
        }
        .assigned-info strong {
            font-weight: 600;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .email-footer p {
            font-size: 13px;
            color: #6b7280;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #059669 0%, #0891b2 100%);
            color: #ffffff;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>🎯 New Order Assigned</h1>
            <p>You have been assigned to a new order</p>
        </div>
        
        <div class="email-body">
            <p class="greeting">Hello {{ $assignedUser->name }},</p>
            
            <p style="margin-bottom: 20px; color: #4b5563;">
                A new order has been assigned to you. Here are the details:
            </p>
            
            <div class="order-card">
                <div class="order-card-header">
                    <div class="order-avatar">#</div>
                    <div>
                        <div class="order-title">{{ $order->title }}</div>
                        <div class="order-number">{{ $order->order_number }}</div>
                    </div>
                </div>
                
                <div class="order-details">
                    <div class="detail-item">
                        <span class="detail-label">Status</span>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $order->status)) }}">{{ $order->status }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Priority</span>
                        <span class="priority-badge priority-{{ strtolower($order->priority) }}">{{ $order->priority }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Due Date</span>
                        <span class="detail-value">{{ $order->due_date ? $order->due_date->format('M d, Y') : 'Not set' }}</span>
                    </div>
                    @if($order->lead)
                    <div class="detail-item" style="grid-column: span 2;">
                        <span class="detail-label">Client</span>
                        <span class="detail-value">{{ $order->lead->full_name }}</span>
                    </div>
                    @endif
                    @if($order->orderCategory)
                    <div class="detail-item" style="grid-column: span 2;">
                        <span class="detail-label">Category</span>
                        <span class="detail-value">{{ $order->orderCategory->name }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($order->description)
            <div class="order-card">
                <div class="detail-item">
                    <span class="detail-label">Description</span>
                    <p style="color: #4b5563; font-size: 14px; margin-top: 5px;">{{ $order->description }}</p>
                </div>
            </div>
            @endif
            
            <div class="assigned-info">
                <p><strong>Assigned By:</strong> {{ $assignedBy->name }}</p>
                <p><strong>Assigned At:</strong> {{ now()->format('M d, Y H:i A') }}</p>
            </div>
            
            <div style="text-align: center;">
                <a href="{{ url('/admin/orders/' . $order->id . '/edit') }}" class="btn">View Order Details</a>
            </div>
        </div>
        
        <div class="email-footer">
            <p>This is an automated notification from The Market Magnetize.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>
