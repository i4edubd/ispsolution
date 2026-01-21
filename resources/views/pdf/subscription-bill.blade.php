<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subscription Bill - {{ $bill->bill_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .bill-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name ?? 'Company Name' }}</h1>
        <h2>Subscription Bill</h2>
    </div>

    <div class="bill-info">
        <p><strong>Bill Number:</strong> {{ $bill->bill_number }}</p>
        <p><strong>Billing Period:</strong> {{ $bill->billing_period_start->format('M d, Y') }} - {{ $bill->billing_period_end->format('M d, Y') }}</p>
        <p><strong>Due Date:</strong> {{ $bill->due_date->format('M d, Y') }}</p>
        <p><strong>Status:</strong> {{ ucfirst($bill->status) }}</p>
    </div>

    <table>
        <tr>
            <th>Description</th>
            <th style="text-align: right;">Amount</th>
        </tr>
        <tr>
            <td>{{ $bill->subscription->plan->name ?? 'Subscription' }}</td>
            <td style="text-align: right;">{{ number_format($bill->amount, 2) }} {{ $bill->currency }}</td>
        </tr>
        <tr>
            <td>Tax</td>
            <td style="text-align: right;">{{ number_format($bill->tax, 2) }} {{ $bill->currency }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td style="text-align: right;">-{{ number_format($bill->discount, 2) }} {{ $bill->currency }}</td>
        </tr>
        <tr class="total">
            <td>Total Amount</td>
            <td style="text-align: right;">{{ number_format($bill->total_amount, 2) }} {{ $bill->currency }}</td>
        </tr>
    </table>

    @if($bill->notes)
    <div style="margin-top: 20px;">
        <p><strong>Notes:</strong> {{ $bill->notes }}</p>
    </div>
    @endif
</body>
</html>
