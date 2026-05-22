@extends('layouts.cashier')

@section('title', 'Dashboard')

@section('content')

<h1 class="page-title">Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Today's Sales</div>
        <div class="stat-value">₱{{ number_format($todaySales, 0) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Payment</div>
        <div class="stat-value">{{ $pendingPayment }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">New Today</div>
        <div class="stat-value">{{ $newToday }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Avg Order Value</div>
        <div class="stat-value">₱{{ number_format($avgOrderValue, 0) }}</div>
    </div>
</div>

<div class="two-col-grid">
    <div>
        <h2 class="section-title">Recent Orders</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer NAME</th>
                        <th>Service</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                        <tr>
                            <td>{{ $order->order_id }}</td>
                            <td>{{ $order->customer_name ?? 'Walk-in' }}</td>
                            <td>{{ str_replace('Self-Service ', '', $order->service) }}</td>
                            <td>
                                <span class="status-text status-{{ strtolower($order->status) }}">
                                    {{ $order->status === 'ready' ? 'Ready to Pick Up' : ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-table">No recent orders</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h2 class="section-title">Pending Pickup</h2>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Pick-up Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPickup as $order)
                        <tr>
                            <td>{{ $order->order_id }}</td>
                            <td>{{ $order->customer_name ?? 'Walk-in' }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="empty-table">No pending pickups</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection