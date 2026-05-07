@extends('layouts.cashier')

@section('title', 'Payments')

@section('content')

<h1 class="page-title">PAYMENT LIST</h1>

<div class="table-full">
    <table class="data-table">
        <thead>
            <tr>
                <th>PAYMENT ID</th>
                <th>ORDER ID</th>
                <th>CUSTOMER NAME</th>
                <th>AMOUNT</th>
                <th>METHOD</th>
                <th>STATUS</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
                <tr>
                    <td>{{ $payment->payment_id }}</td>
                    <td>{{ $payment->order_id }}</td>
                    <td>{{ $payment->customer_name ?? 'Walk-in' }}</td>
                    <td>₱{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ ucfirst($payment->method) }}</td>
                    <td>
                        <span class="status-text status-{{ strtolower($payment->status) }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('cashier.payments.show', $payment->payment_id) }}" class="btn-action">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-table">No payments found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px; padding: 0 4px;">

    <div style="display:flex; gap:8px;">
        @if($payments->onFirstPage())
            <button disabled style="padding:6px 16px; background:#ccc; color:#fff; border:none; border-radius:6px; cursor:not-allowed; font-size:13px;">
                ← Previous
            </button>
        @else
            <a href="{{ $payments->previousPageUrl() }}"
               style="padding:6px 16px; background:#1a1a2e; color:#fff; border-radius:6px; text-decoration:none; font-size:13px;">
                ← Previous
            </a>
        @endif

        @if($payments->hasMorePages())
            <a href="{{ $payments->nextPageUrl() }}"
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