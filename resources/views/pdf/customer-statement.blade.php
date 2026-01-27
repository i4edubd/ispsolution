<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Statement - {{ $user->name }}</title>
    <style nonce="{{ csp_nonce() }}">
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .customer-info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-top: 30px; padding: 15px; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Customer Statement</h1>
        <p>{{ $startDate }} to {{ $endDate }}</p>
    </div>

    <div class="customer-info">
        <p><strong>Customer Name:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        @if($user->phone)
        <p><strong>Phone:</strong> {{ $user->phone }}</p>
        @endif
    </div>

    <h3>Invoices</h3>
    <table>
        <tr>
            <th>Invoice #</th>
            <th>Date</th>
            <th>Status</th>
            <th style="text-align: right;">Amount</th>
        </tr>
        @forelse($invoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->created_at->format('M d, Y') }}</td>
            <td>{{ ucfirst($invoice->status) }}</td>
            <td style="text-align: right;">{{ number_format($invoice->total_amount, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align: center;">No invoices found</td>
        </tr>
        @endforelse
    </table>

    <h3 style="margin-top: 30px;">Payments</h3>
    <table>
        <tr>
            <th>Payment #</th>
            <th>Date</th>
            <th>Method</th>
            <th style="text-align: right;">Amount</th>
        </tr>
        @forelse($payments as $payment)
        <tr>
            <td>{{ $payment->payment_number }}</td>
            <td>{{ $payment->created_at->format('M d, Y') }}</td>
            <td>{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>
            <td style="text-align: right;">{{ number_format($payment->amount, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align: center;">No payments found</td>
        </tr>
        @endforelse
    </table>

    <div class="summary">
        <h3>Summary</h3>
        <p><strong>Total Invoices:</strong> {{ number_format($totalInvoices, 2) }}</p>
        <p><strong>Total Payments:</strong> {{ number_format($totalPayments, 2) }}</p>
        <p><strong>Balance:</strong> {{ number_format($totalInvoices - $totalPayments, 2) }}</p>
    </div>
</body>
</html>
