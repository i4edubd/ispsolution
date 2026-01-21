<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
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
    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
        <strong>Payment Overdue!</strong><br>
        Your payment for Invoice #{{ $invoice->invoice_number }} is overdue. Please make payment as soon as possible to avoid service interruption.
    </div>
    @elseif($type === 'reminder')
    <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
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

    <table style="width: 100%; background-color: #fff; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px;" cellpadding="20" cellspacing="0">
        <tr>
            <td style="border-bottom: 1px solid #f0f0f0; padding: 10px;">
                <strong>Invoice Number:</strong>
            </td>
            <td style="border-bottom: 1px solid #f0f0f0; padding: 10px; text-align: right;">
                #{{ $invoice->invoice_number }}
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #f0f0f0; padding: 10px;">
                <strong>Invoice Date:</strong>
            </td>
            <td style="border-bottom: 1px solid #f0f0f0; padding: 10px; text-align: right;">
                {{ $invoice->invoice_date?->format('F d, Y') }}
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #f0f0f0; padding: 10px;">
                <strong>Due Date:</strong>
            </td>
            <td style="border-bottom: 1px solid #f0f0f0; padding: 10px; text-align: right;">
                {{ $invoice->due_date?->format('F d, Y') }}
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #f0f0f0; padding: 10px;">
                <strong>Status:</strong>
            </td>
            <td style="border-bottom: 1px solid #f0f0f0; padding: 10px; text-align: right; color: {{ $invoice->status === 'paid' ? '#28a745' : ($invoice->status === 'overdue' ? '#dc3545' : '#ffc107') }};">
                {{ ucfirst($invoice->status) }}
            </td>
        </tr>
        <tr>
            <td style="padding: 10px; font-weight: bold; font-size: 1.2em; color: #007bff;">
                Total Amount:
            </td>
            <td style="padding: 10px; text-align: right; font-weight: bold; font-size: 1.2em; color: #007bff;">
                {{ number_format($invoice->total_amount, 2) }} BDT
            </td>
        </tr>
    </table>

    @if($invoice->status !== 'paid')
    <div style="text-align: center;">
        <a href="{{ config('app.url') }}/customer/invoices/{{ $invoice->id }}" style="display: inline-block; padding: 12px 30px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; margin-top: 20px;">
            View & Pay Invoice
        </a>
    </div>
    @endif

    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; font-size: 0.9em; color: #6c757d;">
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
