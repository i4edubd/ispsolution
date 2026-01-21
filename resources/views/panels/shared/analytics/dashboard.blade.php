@extends('layouts.panel')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Analytics Dashboard</h3>
        </div>
        <div class="card-body">
            <!-- Date Range Filter -->
            <form method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">Apply</button>
                    </div>
                </div>
            </form>

            <!-- Revenue Summary -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Revenue</h5>
                            <h2>{{ number_format($analytics['revenue_analytics']['total_revenue'] ?? 0, 2) }} BDT</h2>
                            <small>Period: {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Total Customers</h5>
                            <h2>{{ $analytics['customer_analytics']['total_customers'] ?? 0 }}</h2>
                            <small>Active: {{ $analytics['customer_analytics']['active_customers'] ?? 0 }}</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Avg Daily Revenue</h5>
                            <h2>{{ number_format($analytics['revenue_analytics']['average_daily_revenue'] ?? 0, 2) }} BDT</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Growth Rate</h5>
                            <h2>{{ number_format($analytics['revenue_analytics']['growth_rate'] ?? 0, 2) }}%</h2>
                            <small>vs Previous Period</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue by Method -->
            @if(isset($analytics['revenue_analytics']['revenue_by_method']) && $analytics['revenue_analytics']['revenue_by_method']->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Revenue by Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th>Revenue</th>
                                    <th>Transactions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($analytics['revenue_analytics']['revenue_by_method'] as $method)
                                    <tr>
                                        <td>{{ ucfirst($method->payment_method) }}</td>
                                        <td>{{ number_format($method->revenue, 2) }} BDT</td>
                                        <td>{{ $method->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Quick Links -->
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex gap-2">
                        <a href="{{ route('analytics.revenue') }}" class="btn btn-outline-primary">Detailed Revenue Report</a>
                        <a href="{{ route('analytics.customers') }}" class="btn btn-outline-success">Customer Analytics</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
