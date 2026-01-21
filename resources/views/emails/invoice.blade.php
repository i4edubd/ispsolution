<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
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
        .invoice-details {
            background-color: #fff;
            border: 1px solid #dee2e6;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .invoice-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .invoice-row:last-child {
            border-bottom: none;
        }
        .total-row {
            font-weight: bold;
            font-size: 1.2em;
            color: #007bff;
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
        @if($type === 'overdue')
        .alert {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        @elseif($type === 'reminder')
        .alert {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        @endif
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0;">
            @if($type === 'overdue')
                ⚠️ Overdue Payment Notice
            @elseif($type === 'reminder')
                🔔 Payment Reminder
            @else
                📄 New Invoice
            @endif
        </h2>
    </div>

    @if($type === 'overdue')
    <div class="alert">
        <strong>Payment Overdue!</strong><br>
        Your payment for Invoice #{{ $invoice->invoice_number }} is overdue. Please make payment as soon as possible to avoid service interruption.
    </div>
    @elseif($type === 'reminder')
    <div class="alert">
        <strong>Payment Due Soon</strong><br>
        This is a friendly reminder that your payment for Invoice #{{ $invoice->invoice_number }} is due on {{ $invoice->due_date?->format('F d, Y') }}.
    </div>
    @endif

    <p>Dear {{ $user->name }},</p>

    @if($type === 'new')
    <p>We have generated a new invoice for your account. Please find the details below:</p>
    @elseif($type === 'reminder')
    <p>This is a reminder about your upcoming invoice payment:</p>
    @else
    <p>We noticed that your invoice payment is overdue. Please review the details:</p>
    @endif

    <div class="invoice-details">
        <div class="invoice-row">
            <span><strong>Invoice Number:</strong></span>
            <span>#{{ $invoice->invoice_number }}</span>
        </div>
        <div class="invoice-row">
            <span><strong>Invoice Date:</strong></span>
            <span>{{ $invoice->invoice_date?->format('F d, Y') }}</span>
        </div>
        <div class="invoice-row">
            <span><strong>Due Date:</strong></span>
            <span>{{ $invoice->due_date?->format('F d, Y') }}</span>
        </div>
        <div class="invoice-row">
            <span><strong>Status:</strong></span>
            <span style="color: {{ $invoice->status === 'paid' ? '#28a745' : ($invoice->status === 'overdue' ? '#dc3545' : '#ffc107') }};">
                {{ ucfirst($invoice->status) }}
            </span>
        </div>
        <div class="invoice-row total-row">
            <span>Total Amount:</span>
            <span>{{ number_format($invoice->total_amount, 2) }} BDT</span>
        </div>
    </div>

    @if($invoice->status !== 'paid')
    <center>
        <a href="{{ config('app.url') }}/customer/invoices/{{ $invoice->id }}" class="button">
            View & Pay Invoice
        </a>
    </center>
    @endif

    <div class="footer">
        <p>
            If you have any questions about this invoice, please contact our support team.
        </p>
        <p style="margin-bottom: 0;">
            Thank you for your business!<br>
            {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
