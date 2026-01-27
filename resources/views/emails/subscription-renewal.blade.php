<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Renewal Reminder</title>
    <style nonce="{{ csp_nonce() }}">
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .subscription-details {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .detail-row {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            font-size: 0.9em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">🔔 Subscription Renewal Reminder</h2>
    </div>

    <div class="alert">
        <strong>Your subscription is expiring soon!</strong><br>
        Your subscription will expire in {{ $daysUntilExpiry }} day(s). Please renew to continue using our services.
    </div>

    <p>Hello,</p>

    <p>This is a friendly reminder that your subscription is due for renewal soon.</p>

    <div class="subscription-details">
        <div class="detail-row">
            <strong>Plan:</strong> {{ $subscription->plan->name ?? 'N/A' }}
        </div>
        <div class="detail-row">
            <strong>Status:</strong> {{ ucfirst($subscription->status) }}
        </div>
        <div class="detail-row">
            <strong>Expires On:</strong> {{ $subscription->end_date?->format('F d, Y') }}
        </div>
        <div class="detail-row">
            <strong>Days Remaining:</strong> {{ $daysUntilExpiry }}
        </div>
    </div>

    <p>To avoid any interruption in your service, please renew your subscription before it expires.</p>

    <center>
        <a href="{{ config('app.url') }}/subscriptions/{{ $subscription->id }}/renew" class="button">
            Renew Subscription
        </a>
    </center>

    <div class="footer">
        <p>
            If you have any questions about your subscription, please contact our support team.
        </p>
        <p style="margin-bottom: 0;">
            Thank you for your business!<br>
            {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
