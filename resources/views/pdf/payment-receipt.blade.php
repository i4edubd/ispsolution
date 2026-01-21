<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .receipt-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .amount { font-weight: bold; font-size: 1.2em; color: green; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name ?? 'Company Name' }}</h1>
        <h2>Payment Receipt</h2>
    </div>

    <div class="receipt-info">
        <p><strong>Receipt Number:</strong> {{ $payment->payment_number }}</p>
        <p><strong>Date:</strong> {{ $payment->created_at->format('M d, Y H:i:s') }}</p>
        <p><strong>Customer:</strong> {{ $payment->user->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $payment->user->email ?? 'N/A' }}</p>
    </div>

    <table>
        <tr>
            <th>Description</th>
            <th style="text-align: right;">Amount</th>
        </tr>
        <tr>
            <td>
                Payment for Invoice: {{ $payment->invoice->invoice_number ?? 'N/A' }}
                <br>
                <small>Payment Method: {{ ucfirst($payment->payment_method ?? 'N/A') }}</small>
                @if($payment->payment_reference)
                <br>
                <small>Reference: {{ $payment->payment_reference }}</small>
                @endif
            </td>
            <td class="amount" style="text-align: right;">{{ number_format($payment->amount, 2) }} {{ $payment->currency ?? 'USD' }}</td>
        </tr>
    </table>

    <div style="margin-top: 30px; text-align: center;">
        <p><strong>Thank you for your payment!</strong></p>
        <p><small>Status: {{ ucfirst($payment->status) }}</small></p>
    </div>
</body>
</html>
