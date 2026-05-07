@extends('layouts.customer')

@section('title', 'Dashboard')

@section('content')

<h1 class="page-title">Dashboard</h1>

<div class="stats-grid three-col">
    <div class="stat-card">
        <span class="stat-label">Total Orders</span>
        <span class="stat-value">{{ $totalOrders }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Total Spent</span>
        <span class="stat-value">₱{{ number_format($totalSpent, 0) }}</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Points Earned</span>
        <span class="stat-value">{{ $pointsEarned }}</span>
    </div>
</div>

<h2 class="section-title">My Active Orders</h2>
<div class="table-wrapper" style="margin-bottom: 30px;">
    <table class="data-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Service</th>
                <th>Weight</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Pick-Up Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activeOrders as $order)
            <tr>
                <td>{{ $order->order_id }}</td>
                <td>{{ $order->service }}</td>
                <td>{{ $order->weight }} kg</td>
                <td>₱{{ number_format($order->amount, 2) }}</td>
                <td>
                    <span class="status-text status-{{ strtolower($order->status) }}">
                        {{ $order->status === 'ready' ? 'Ready to Pick Up' : ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</td>
                <td>
                    @if($order->status === 'ready')
                        <form method="POST" action="{{ route('customer.orders.claim', $order->order_id) }}">
                            @csrf
                            <button type="submit" class="btn-action"
                                style="background:#9b59b6;"
                                onclick="return confirm('Confirm claim for {{ $order->order_id }}?')">
                                Claim
                            </button>
                        </form>
                    @else
                        <span style="color:#aaa; font-size:13px;">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:20px; color:#1a1a2e;">
                    No active orders at the moment.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection