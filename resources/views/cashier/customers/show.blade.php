@extends('layouts.cashier')

@section('title', 'Customer Profile')

@section('content')

<div style="display:flex; align-items:center; gap:16px; margin-bottom:25px;">
    <a href="{{ route('cashier.customers.index') }}"
       style="background:#7EB4E8; padding:10px 18px; border-radius:8px; text-decoration:none; color:#1a1a1a; font-weight:600; font-size:14px;">
        ← Back
    </a>
    <h1 class="page-title" style="margin-bottom:0;">CUSTOMER PROFILE</h1>
</div>

<div class="form-card" style="margin-bottom:25px;">
    <div class="form-row">
        <div class="form-group">
            <label>FULL NAME:</label>
            <input type="text" value="{{ $customer->name }}" readonly style="opacity:0.8;">
        </div>
        <div class="form-group">
            <label>USERNAME:</label>
            <input type="text" value="{{ $customer->username }}" readonly style="opacity:0.8;">
        </div>
        <div class="form-group">
            <label>EMAIL:</label>
            <input type="text" value="{{ $customer->email ?? 'N/A' }}" readonly style="opacity:0.8;">
        </div>
        <div class="form-group">
            <label>REGISTERED:</label>
            <input type="text" value="{{ $customer->created_at->format('F d, Y') }}" readonly style="opacity:0.8;">
        </div>
    </div>
</div>

{{-- ORDER HISTORY --}}
<h2 class="section-title">Order History</h2>
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>ORDER ID</th>
                <th>SERVICE</th>
                <th>WEIGHT</th>
                <th>AMOUNT</th>
                <th>STATUS</th>
                <th>DATE</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customer->orders as $order)
            <tr>
                <td>{{ $order->order_id }}</td>
                <td>{{ $order->service }}</td>
                <td>{{ $order->weight }} kg</td>
                <td>₱{{ number_format($order->amount, 2) }}</td>
                <td>
                    <span class="badge badge-{{ strtolower($order->status) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align:center; padding:20px; color:#1a1a2e;">No orders yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
