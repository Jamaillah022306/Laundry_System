<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $payment->payment_id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
            min-height: 100vh;
        }
        .receipt-wrapper { width: 420px; }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #4a90d9;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 16px;
            transition: background 0.2s;
        }
        .back-btn:hover { background: #2563b8; color: white; }
        .receipt {
            background: white;
            width: 420px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .receipt-header {
            background: #4a90d9;
            padding: 30px 25px;
            text-align: center;
            color: white;
        }
        .receipt-header img {
            height: 70px;
            margin-bottom: 10px;
        }
        .receipt-header h1 {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 1px;
        }
        .receipt-header p {
            font-size: 13px;
            opacity: 0.85;
            margin-top: 4px;
        }
        .receipt-badge {
            background: #2ecc71;
            color: #1a1a2e;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 18px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 12px;
            letter-spacing: 1px;
        }
        .receipt-body { padding: 25px; }
        .receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .receipt-row:last-child { border-bottom: none; }
        .receipt-row .label {
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            min-width: 130px;
        }
        .receipt-row .value {
            color: #1a1a2e;
            font-weight: 600;
            text-align: right;
        }

        /* Price Breakdown Box */
        .price-breakdown {
            background: #f0f7ff;
            border: 1px dashed #4a90d9;
            margin: 0 25px 16px 25px;
            border-radius: 10px;
            padding: 14px 18px;
        }
        .price-breakdown .breakdown-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #4a90d9;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #444;
            margin-bottom: 6px;
        }
        .breakdown-row .bd-label { font-weight: 500; }
        .breakdown-row .bd-value { font-weight: 700; color: #1a1a2e; }
        .breakdown-divider {
            border: none;
            border-top: 1px dashed #b0c8e8;
            margin: 8px 0;
        }
        .breakdown-row.total-row .bd-label {
            font-weight: 700;
            color: #1a1a2e;
            font-size: 14px;
        }
        .breakdown-row.total-row .bd-value {
            font-size: 15px;
            color: #16a34a;
        }

        .receipt-total {
            background: #f5e642;
            margin: 0 25px 25px 25px;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .receipt-total .label {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a2e;
            text-transform: uppercase;
        }
        .receipt-total .amount {
            font-size: 26px;
            font-weight: 900;
            color: #1a1a2e;
        }
        .receipt-footer {
            background: #4a90d9;
            padding: 18px 25px;
            text-align: center;
            color: white;
            font-size: 13px;
        }
        .receipt-footer p { opacity: 0.9; line-height: 1.6; }
        .print-btn {
            display: block;
            width: 420px;
            margin: 20px auto 0;
            background: #4a90d9;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }
        .print-btn:hover { background: #2563b8; }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            body { background: white; padding: 0; }
            .receipt { box-shadow: none; border-radius: 0; width: 100%; }
            .print-btn { display: none; }
            .back-btn { display: none; }
        }
    </style>
</head>
<body>

<div class="receipt-wrapper">

    {{-- BACK BUTTON --}}
    <a href="{{ route('customer.history') }}" class="back-btn">
        ← Back to Order History
    </a>

    <div class="receipt">
        <div class="receipt-header">
            <img src="{{ asset('images/Bubble_Bee_Laundry_logo_design-removebg-preview.png') }}" alt="Bubble Bee Laundry">
            <h1>BUBBLE BEE LAUNDRY</h1>
            <p>Official Payment Receipt</p>
            <span class="receipt-badge">✓ PAID</span>
        </div>

        <div class="receipt-body">
            <div class="receipt-row">
                <span class="label">Receipt No.</span>
                <span class="value">{{ $payment->payment_id }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Order ID</span>
                <span class="value">{{ $payment->order_id }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Customer</span>
                <span class="value">{{ $payment->customer_name ?? 'Walk-in' }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Service</span>
                <span class="value">{{ $payment->service ?? 'N/A' }}</span>
            </div>
            <div class="receipt-row">
                <span class="label">Weight</span>
                <span class="value">{{ $payment->weight ?? 'N/A' }} kg</span>
            </div>
            <div class="receipt-row">
                <span class="label">Payment Method</span>
                <span class="value">{{ ucfirst($payment->method) }}</span>
            </div>
            @if($payment->reference_number)
            <div class="receipt-row">
                <span class="label">Reference No.</span>
                <span class="value">{{ $payment->reference_number }}</span>
            </div>
            @endif
            <div class="receipt-row">
                <span class="label">Date Paid</span>
                <span class="value">{{ \Carbon\Carbon::parse($payment->updated_at)->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        {{-- PRICE BREAKDOWN BOX --}}
        @php
            $pricePerKg = ($payment->weight > 0) ? ($payment->amount / $payment->weight) : 0;
        @endphp
        <div class="price-breakdown">
            <div class="breakdown-title">Price Breakdown</div>
            <div class="breakdown-row">
                <span class="bd-label">Service</span>
                <span class="bd-value">{{ $payment->service ?? 'N/A' }}</span>
            </div>
            <div class="breakdown-row">
                <span class="bd-label">Price per kg</span>
                <span class="bd-value">₱{{ number_format($pricePerKg, 2) }}</span>
            </div>
            <div class="breakdown-row">
                <span class="bd-label">Weight</span>
                <span class="bd-value">{{ $payment->weight ?? 0 }} kg</span>
            </div>
            <hr class="breakdown-divider">
            <div class="breakdown-row total-row">
                <span class="bd-label">₱{{ number_format($pricePerKg, 2) }} × {{ $payment->weight ?? 0 }} kg</span>
                <span class="bd-value">= ₱{{ number_format($payment->amount, 2) }}</span>
            </div>
        </div>

        <div class="receipt-total">
            <span class="label">Total Amount</span>
            <span class="amount">₱{{ number_format($payment->amount, 2) }}</span>
        </div>

        <div class="receipt-footer">
            <p>Thank you for choosing Bubble Bee Laundry!<br>
            Please keep this receipt for your records.</p>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>

</div>

</body>
</html>