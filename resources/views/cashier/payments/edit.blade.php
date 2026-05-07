@extends('layouts.cashier')

@section('title', 'Update Payment')

@section('content')

<h1 class="page-title">UPDATE PAYMENT</h1>

@if(session('success'))
    <div class="alert alert-success" style="margin-bottom:20px;">
        {{ session('success') }}
        @if(session('show_receipt'))
            &nbsp;|&nbsp;
            <a href="{{ route('cashier.payments.receipt', session('receipt_id')) }}"
               target="_blank"
               style="color:#155724; font-weight:700; text-decoration:underline;">
                🧾 Print Receipt
            </a>
        @endif
    </div>
@endif

<div class="form-card">
    <form method="POST" action="{{ route('cashier.payments.update', $payment->payment_id) }}">
        @csrf
        @method('PATCH')

        <div class="form-grid-2" style="display:grid; grid-template-columns:1fr 1fr; gap:25px; margin-bottom:25px;">
            <div class="form-group">
                <label class="form-label">PAYMENT ID:</label>
                <input type="text" class="form-control" value="{{ $payment->payment_id }}" readonly
                       style="opacity:0.7; cursor:not-allowed;">
            </div>

            <div class="form-group">
                <label class="form-label">ORDER ID:</label>
                <input type="text" class="form-control" value="{{ $payment->order_id }}" readonly
                       style="opacity:0.7; cursor:not-allowed;">
            </div>

            <div class="form-group">
                <label class="form-label">CUSTOMER:</label>
                <input type="text" class="form-control"
                       value="{{ $payment->order->customer_name ?? 'N/A' }}" readonly
                       style="opacity:0.7; cursor:not-allowed;">
            </div>

            <div class="form-group">
                <label class="form-label">AMOUNT:</label>
                <input type="text" class="form-control"
                       value="₱{{ number_format($payment->amount, 2) }}" readonly
                       style="opacity:0.7; cursor:not-allowed;">
            </div>

            <div class="form-group">
                <label class="form-label" for="method">PAYMENT METHOD:</label>
                <select id="method" name="method" class="form-control" required>
                    <option value="Cash"  {{ strtolower($payment->method) == 'cash'  ? 'selected' : '' }}>Cash</option>
                    <option value="GCash" {{ strtolower($payment->method) == 'gcash' ? 'selected' : '' }}>GCash</option>
                    <option value="Maya"  {{ strtolower($payment->method) == 'maya'  ? 'selected' : '' }}>Maya</option>
                    <option value="Card"  {{ strtolower($payment->method) == 'card'  ? 'selected' : '' }}>Card</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">STATUS:</label>
                <select id="status" name="status" class="form-control" required>
                    <option value="unpaid"   {{ $payment->status == 'unpaid'   ? 'selected' : '' }}>Unpaid</option>
                    <option value="paid"     {{ $payment->status == 'paid'     ? 'selected' : '' }}>Paid</option>
                    <option value="refunded" {{ $payment->status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            {{-- Reference number field - shows for GCash/Maya/Card --}}
            <div class="form-group" id="referenceGroup"
                 style="{{ in_array(strtolower($payment->method), ['gcash','maya','card']) ? '' : 'display:none;' }} grid-column: span 2;">
                <label class="form-label">REFERENCE NUMBER:</label>
                <input type="text" name="reference_number" class="form-control"
                       value="{{ $payment->reference_number ?? '' }}"
                       placeholder="Enter reference number">
            </div>
        </div>

        <div class="form-footer" style="gap:12px; display:flex; justify-content:space-between; align-items:center;">
            <a href="{{ route('cashier.payments.index') }}" class="btn-secondary">← Back</a>
            <button type="submit" class="btn-primary">Save Payment</button>
        </div>
    </form>
</div>

{{-- Show receipt button if already paid with digital method --}}
@if($payment->status == 'paid' && in_array(strtolower($payment->method), ['gcash','maya','card']))
    <div style="margin-top:20px; text-align:right;">
        <a href="{{ route('cashier.payments.receipt', $payment->payment_id) }}"
           target="_blank" class="btn-primary"
           style="text-decoration:none; display:inline-block;">
            Print Receipt
        </a>
    </div>
@endif

<script>
    const methodSelect = document.getElementById('method');
    const refGroup     = document.getElementById('referenceGroup');

    methodSelect.addEventListener('change', function () {
        const digital = ['gcash', 'maya', 'card'];
        refGroup.style.display = digital.includes(this.value.toLowerCase()) ? '' : 'none';
    });
</script>

@endsection