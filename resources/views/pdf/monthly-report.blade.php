<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Report - {{ $monthName }} {{ $year }}</title>
    <style nonce="{{ csp_nonce() }}">
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .stats { display: flex; flex-wrap: wrap; margin-bottom: 30px; }
        .stat-box { flex: 1; min-width: 200px; padding: 15px; margin: 10px; border: 1px solid #ddd; background-color: #f9f9f9; }
        .stat-value { font-size: 2em; font-weight: bold; color: #333; }
        .stat-label { color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name }}</h1>
        <h2>Monthly Report</h2>
        <p>{{ $monthName }} {{ $year }}</p>
    </div>

    <div class="stats">
        <div class="stat-box">
            <div class="stat-value">{{ $totalInvoices }}</div>
            <div class="stat-label">Total Invoices</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($totalInvoiceAmount, 2) }}</div>
            <div class="stat-label">Invoice Amount</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $totalPayments }}</div>
            <div class="stat-label">Total Payments</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ number_format($totalPaymentAmount, 2) }}</div>
            <div class="stat-label">Payment Amount</div>
        </div>
    </div>

    <h3>Invoice Status Breakdown</h3>
    <table>
        <tr>
            <th>Status</th>
            <th style="text-align: right;">Count</th>
        </tr>
        <tr>
            <td>Pending</td>
            <td style="text-align: right;">{{ $pendingInvoices }}</td>
        </tr>
        <tr>
            <td>Paid</td>
            <td style="text-align: right;">{{ $paidInvoices }}</td>
        </tr>
        <tr style="font-weight: bold;">
            <td>Total</td>
            <td style="text-align: right;">{{ $totalInvoices }}</td>
        </tr>
    </table>

    <div style="margin-top: 30px;">
        <p><strong>Collection Rate:</strong> {{ $totalInvoiceAmount > 0 ? number_format(($totalPaymentAmount / $totalInvoiceAmount) * 100, 2) : 0 }}%</p>
        <p><strong>Outstanding Amount:</strong> {{ number_format($totalInvoiceAmount - $totalPaymentAmount, 2) }}</p>
    </div>
</body>
</html>
