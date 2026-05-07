@extends('layouts.cashier')

@section('title', 'Archived Orders')

@section('content')

<h1 class="page-title">ARCHIVED ORDERS</h1>

<div class="table-full">
    <table class="data-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer NAME</th>
                <th>Service</th>
                <th>Weight</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_id }}</td>
                    <td>{{ $order->customer_name ?? $order->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ $order->service }}</td>
                    <td>{{ $order->weight }} kg</td>
                    <td>₱{{ number_format($order->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</td>
                    <td>
                        <a href="{{ route('cashier.orders.show', $order->order_id) }}" class="btn-action">View</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 40px; color:#555;">No archived orders yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px; padding: 0 4px;">
    <div style="display:flex; gap:8px;">
        @if($orders->onFirstPage())
            <button disabled style="padding:6px 16px; background:#ccc; color:#fff; border:none; border-radius:6px; cursor:not-allowed; font-size:13px;">
                ← Previous
            </button>
        @else
            <a href="{{ $orders->previousPageUrl() }}"
               style="padding:6px 16px; background:#1a1a2e; color:#fff; border-radius:6px; text-decoration:none; font-size:13px;">
                ← Previous
            </a>
        @endif

        @if($orders->hasMorePages())
            <a href="{{ $orders->nextPageUrl() }}"
               style="padding:6px 16px; background:#1a1a2e; color:#fff; border-radius:6px; text-decoration:none; font-size:13px;">
                Next →
            </a>
        @else
            <button disabled style="padding:6px 16px; background:#ccc; color:#fff; border:none; border-radius:6px; cursor:not-allowed; font-size:13px;">
                Next →
            </button>
        @endif
    </div>
</div>

@endsection