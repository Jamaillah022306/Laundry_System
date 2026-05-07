@extends('layouts.cashier')

@section('title', 'Reports')

@section('content')

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 class="page-title" style="margin: 0;">REPORTS & ANALYTICS</h1>
    <a href="{{ route('cashier.reports.pdf') }}" target="_blank"
        style="background: #e53e3e; color: white; padding: 10px 22px; border-radius: 8px;
               text-decoration: none; font-weight: 600; font-size: 14px; display: flex;
               align-items: center; gap: 8px;">
        Export PDF
    </a>
</div>

<!-- STAT CARDS -->
<div class="stats-grid three-col" style="margin-bottom: 35px;">
    <div class="stat-card">
        <div class="stat-label">Monthly Revenue</div>
        <div class="stat-value">₱{{ number_format($monthlyRevenue, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">{{ $totalOrders }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Completed Orders</div>
        <div class="stat-value">{{ $completed }}</div>
    </div>
</div>

<!-- SALES SUMMARY TABLE -->
<h2 class="section-title">SALES SUMMARY</h2>
<div class="table-full" style="margin-bottom: 35px;">
    <table class="data-table">
        <thead>
            <tr>
                <th>DATE</th>
                <th style="text-align:center;">TOTAL ORDERS</th>
                <th style="text-align:center;">COMPLETED</th>
                <th style="text-align:center;">REVENUE</th>
                <th style="text-align:center;">AVG ORDER</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesSummary as $summary)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($summary->date)->format('M d, Y') }}</td>
                    <td style="text-align:center;">{{ $summary->total_orders }}</td>
                    <td style="text-align:center;">{{ $summary->completed }}</td>
                    <td style="text-align:center;">₱{{ number_format($summary->revenue, 2) }}</td>
                    <td style="text-align:center;">₱{{ $summary->total_orders > 0 ? number_format($summary->revenue / $summary->total_orders, 2) : '0.00' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty-table">No data available</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- TOP SERVICES -->
<h2 class="section-title">TOP SERVICES</h2>
<div class="table-full" style="margin-bottom: 35px;">
    <table class="data-table">
        <thead>
            <tr>
                <th>SERVICE</th>
                <th style="text-align:center;">TOTAL ORDERS</th>
                <th style="text-align:center;">REVENUE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topServices as $service)
                <tr>
                    <td>{{ $service->service }}</td>
                    <td style="text-align:center;">{{ $service->total }}</td>
                    <td style="text-align:center;">₱{{ number_format($service->revenue, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="empty-table">No data available</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- PAYMENT METHOD BREAKDOWN -->
<h2 class="section-title">PAYMENT METHOD BREAKDOWN</h2>
<div class="table-full" style="margin-bottom: 35px;">
    <table class="data-table">
        <thead>
            <tr>
                <th>METHOD</th>
                <th style="text-align:center;">COUNT</th>
                <th style="text-align:center;">TOTAL AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paymentMethods as $method)
                <tr>
                    <td>{{ $method->method }}</td>
                    <td style="text-align:center;">{{ $method->count }}</td>
                    <td style="text-align:center;">₱{{ number_format($method->total, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="empty-table">No data available</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- TOP CUSTOMERS -->
<h2 class="section-title">TOP CUSTOMERS</h2>
<div class="table-full">
    <table class="data-table">
        <thead>
            <tr>
                <th>CUSTOMER</th>
                <th style="text-align:center;">TOTAL ORDERS</th>
                <th style="text-align:center;">TOTAL SPENT</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topCustomers as $customer)
                <tr>
                    <td>{{ $customer->customer_name }}</td>
                    <td style="text-align:center;">{{ $customer->total_orders }}</td>
                    <td style="text-align:center;">₱{{ number_format($customer->total_spent, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="empty-table">No data available</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection