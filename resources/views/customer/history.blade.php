@extends('layouts.customer')

@section('title', 'Order History')

@section('content')

<h1 class="page-title">LIST OF ORDER HISTORY</h1>

<div class="table-full">
    <table class="data-table">
        <thead>
            <tr>
                <th>ORDER ID</th>
                <th>SERVICE</th>
                <th>AMOUNT</th>
                <th>STATUS</th>
                <th>DATE</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_id }}</td>
                    <td>{{ $order->service }}</td>
                    <td>₱{{ number_format($order->amount ?? 0, 2) }}</td>
                    <td>
                        <span class="status-text status-{{ strtolower($order->status) }}">
                            {{ $order->status === 'ready' ? 'Ready to Pick Up' : ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
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
                        @elseif($order->status === 'completed' && $order->payment_status === 'paid')
                            <a href="{{ route('customer.receipt', $order->payment_id) }}"
                               target="_blank"
                               class="btn-receipt">
                                View Receipt
                            </a>
                        @else
                            <span style="color:#aaa; font-size:13px;">—</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="empty-table">No order history found</td></tr>
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