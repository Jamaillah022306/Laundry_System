@extends('layouts.cashier')

@section('title', 'Payment Details')

@section('content')

<div style="display:flex; align-items:center; gap:16px; margin-bottom:25px;">
    <a href="{{ route('cashier.payments.index') }}"
       style="background:#7EB4E8; padding:10px 18px; border-radius:8px; text-decoration:none; color:#1a1a1a; font-weight:600; font-size:14px;">
        ← Back
    </a>
    <h1 class="page-title" style="margin-bottom:0;">PAYMENT DETAILS</h1>
</div>

<div class="form-card">
    <div class="form-row">
        <div class="form-group">
            <label>PAYMENT ID:</label>
            <input type="text" value="{{ $payment->payment_id }}" readonly style="opacity:0.8;">
        </div>
        <div class="form-group">
            <label>ORDER ID:</label>
            <input type="text" value="{{ $payment->order_id }}" readonly style="opacity:0.8;">
        </div>
        <div class="form-group">
            <label>CUSTOMER NAME:</label>
            {{-- ✅ FIXED: Use $payment->customer_name from the JOIN query (COALESCE handles walk-in) --}}
            <input type="text" value="{{ $payment->customer_name ?? 'Walk-in' }}" readonly style="opacity:0.8;">
        </div>
        <div class="form-group">
            <label>AMOUNT:</label>
            <input type="text" value="₱{{ number_format($payment->amount, 2) }}" readonly style="opacity:0.8;">
        </div>
        <div class="form-group">
            <label>METHOD:</label>
            <input type="text" value="{{ ucfirst($payment->method) }}" readonly style="opacity:0.8;">
        </div>
        <div class="form-group">
            <label>STATUS:</label>
            {{-- ✅ FIXED: Colored text — green for paid, red for unpaid --}}
            <div style="margin-top:10px; margin-bottom:8px;">
                <span style="
                    font-size: 15px;
                    font-weight: 700;
                    color: {{ $payment->status === 'paid' ? '#16a34a' : '#dc2626' }};
                ">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
        </div>
        @if($payment->reference_number)
        <div class="form-group">
            <label>REFERENCE NUMBER:</label>
            <input type="text" value="{{ $payment->reference_number }}" readonly style="opacity:0.8;">
        </div>
        @endif

        <div class="form-group">
            <label>DATE PAID:</label>
            <input type="text"
                   value="{{ $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->setTimezone('Asia/Manila')->format('F d, Y h:i A') : 'Not yet paid' }}"
                   readonly style="opacity:0.8;">
        </div>
    </div>

    {{-- MARK AS PAID --}}
    @if($payment->status === 'unpaid')
    <div style="border-top: 2px solid rgba(255,255,255,0.3); padding-top:25px; margin-top:10px;">
        <h3 style="font-size:16px; font-weight:800; color:#1a1a2e; margin-bottom:20px; text-transform:uppercase;">
            Process Payment
        </h3>
        <form action="{{ route('cashier.payments.mark-paid', $payment->payment_id) }}" method="POST">
            @csrf

            <div class="form-group" style="margin-bottom:20px;">
                <label>PAYMENT METHOD:</label>
                <select name="method" id="methodSelect" onchange="handleMethodChange()" required>
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="Maya">Maya</option>
                    <option value="Card">Card</option>
                </select>
            </div>

            {{-- REFERENCE NUMBER - shows for GCash, Maya, Card --}}
            <div class="form-group" id="refNumberGroup" style="display:none; margin-bottom:20px;">
                <label>REFERENCE NUMBER:</label>
                <input type="text"
                       name="reference_number"
                       id="refNumberInput"
                       placeholder="Auto-generated or type manually"
                       style="width:100%; box-sizing:border-box;">

            </div>

            <div class="form-footer" style="margin-top:20px;">
                <button type="submit" class="btn-primary">✔ Mark as Paid</button>
            </div>
        </form>
    </div>
    @endif
</div>

<script>
// Prefix per payment method
const refPrefixes = {
    GCash: 'GC',
    Maya:  'MY',
    Card:  'CD',
};

function generateRefNumber() {
    const method  = document.getElementById('methodSelect').value;
    const prefix  = refPrefixes[method] || 'REF';
    const date    = new Date();

    // Format: GC-20260507-A3F9 (prefix + date + 4 random hex chars)
    const dateStr = date.getFullYear().toString()
        + String(date.getMonth() + 1).padStart(2, '0')
        + String(date.getDate()).padStart(2, '0');
    const random  = Math.random().toString(16).substring(2, 6).toUpperCase();

    document.getElementById('refNumberInput').value = `${prefix}-${dateStr}-${random}`;
}


function handleMethodChange() {
    const method   = document.getElementById('methodSelect').value;
    const refGroup = document.getElementById('refNumberGroup');
    const refInput = document.getElementById('refNumberInput');

    if (method === 'GCash' || method === 'Maya' || method === 'Card') {
        refGroup.style.display = 'block';
        refInput.required = true;

        // Auto-generate on method switch
        generateRefNumber();
    } else {
        refGroup.style.display = 'none';
        refInput.required = false;
        refInput.value = '';
    }
}
</script>

@endsection