@extends('layouts.cashier')

@section('title', 'Order Details')

@section('content')

<h1 class="page-title">ORDER DETAILS</h1>

<div class="form-wrapper" style="max-width: 600px;">

    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.2);">
        <span style="font-weight:700; color:#1a1a2e;">Order ID</span>
        <span>{{ $order->order_id }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.2);">
        <span style="font-weight:700; color:#1a1a2e;">Customer</span>
        <span>{{ $order->customer_name ?? $order->customer->name ?? 'Walk-in' }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.2);">
        <span style="font-weight:700; color:#1a1a2e;">Email</span>
        <span>{{ $order->customer_email ?? 'N/A' }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.2);">
        <span style="font-weight:700; color:#1a1a2e;">Service</span>
        <span>{{ $order->service }}</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.2);">
        <span style="font-weight:700; color:#1a1a2e;">Weight</span>
        <span>{{ $order->weight }} kg</span>
    </div>
    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.2);">
        <span style="font-weight:700; color:#1a1a2e;">Amount</span>
        <span>₱{{ number_format($order->amount, 2) }}</span>
    </div>

    {{-- Colored Status --}}
    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.2);">
        <span style="font-weight:700; color:#1a1a2e;">Status</span>
        <span style="font-weight:700;
            color:
            @if($order->status === 'claimed') #f59e0b
            @elseif($order->status === 'completed') #22c55e
            @elseif($order->status === 'cancelled') #ef4444
            @elseif($order->status === 'washing') #3b82f6
            @elseif($order->status === 'drying') #8b5cf6
            @elseif($order->status === 'ready') #06b6d4
            @else #6b7280
            @endif
        ;">
            {{ $order->status === 'ready' ? 'Ready to Pick Up' : ucfirst($order->status) }}
        </span>
    </div>

    <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid rgba(255,255,255,0.2);">
        <span style="font-weight:700; color:#1a1a2e;">Pick-up Date</span>
        <span>{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</span>
    </div>

    @if($order->payment_status)
    <div style="display:flex; justify-content:space-between; padding:12px 0;">
        <span style="font-weight:700; color:#1a1a2e;">Payment Status</span>
        <span class="status-text status-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span>
    </div>
    @endif

    <div style="margin-top: 2rem; display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('cashier.orders.index') }}" class="btn-secondary">← Back to Orders</a>

        {{-- Mark as Complete button — only show if status is claimed --}}
        @if($order->status === 'claimed')
        <form method="POST" action="{{ route('cashier.orders.complete', $order->order_id) }}">
            @csrf
            <button type="submit" class="btn-primary" style="background:#22c55e; border:none; cursor:pointer;"
                onclick="return confirm('Mark {{ $order->order_id }} as Completed?')">
                ✅ Mark as Complete
            </button>
        </form>
        @endif
    </div>
</div>

@endsection