<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Slip - {{ $order->order_id }}</title>
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
        .wrapper { width: 420px; }
        .action-btns {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-blue { background: #4a90d9; color: white; }
        .btn-blue:hover { background: #2563b8; }
        .btn-green { background: #16a34a; color: white; }
        .btn-green:hover { background: #15803d; }

        .slip {
            background: white;
            width: 420px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .slip-header {
            background: #4a90d9;
            padding: 30px 25px;
            text-align: center;
            color: white;
        }
        .slip-header img {
            height: 70px;
            margin-bottom: 10px;
        }
        .slip-header h1 {
            font-size: 22px;
            font-weight: 900;
            letter-spacing: 1px;
        }
        .slip-header p {
            font-size: 13px;
            opacity: 0.85;
            margin-top: 4px;
        }
        .slip-badge {
            background: #fbbf24;
            color: #1a1a2e;
            font-size: 13px;
            font-weight: 700;
            padding: 6px 18px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 12px;
            letter-spacing: 1px;
        }
        .order-id-box {
            background: #1a1a2e;
            margin: 20px 25px 0 25px;
            border-radius: 10px;
            padding: 16px 20px;
            text-align: center;
        }
        .order-id-box .label {
            color: #aaa;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .order-id-box .order-id {
            color: #fbbf24;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: 2px;
        }
        .slip-body { padding: 20px 25px; }
        .slip-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .slip-row:last-child { border-bottom: none; }
        .slip-row .label {
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            min-width: 130px;
        }
        .slip-row .value {
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

        .slip-total {
            background: #f5e642;
            margin: 0 25px 20px 25px;
            border-radius: 10px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .slip-total .label {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a2e;
            text-transform: uppercase;
        }
        .slip-total .amount {
            font-size: 26px;
            font-weight: 900;
            color: #1a1a2e;
        }
        .slip-note {
            background: #fff8e1;
            border-left: 4px solid #fbbf24;
            margin: 0 25px 20px 25px;
            border-radius: 0 8px 8px 0;
            padding: 12px 16px;
            font-size: 13px;
            color: #92400e;
            line-height: 1.6;
        }
        .slip-note strong { display: block; margin-bottom: 4px; }
        .slip-footer {
            background: #4a90d9;
            padding: 18px 25px;
            text-align: center;
            color: white;
            font-size: 13px;
        }
        .slip-footer p { opacity: 0.9; line-height: 1.6; }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body { background: white; padding: 0; }
            .slip { box-shadow: none; border-radius: 0; width: 100%; }
            .action-btns { display: none; }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="action-btns">
        <a href="{{ route('cashier.orders.index') }}" class="btn btn-blue">
            ← Back to Orders
        </a>
        <button class="btn btn-green" onclick="window.print()">
            🖨 Print Claim Slip
        </button>
    </div>

    <div class="slip">
        <div class="slip-header">
            <img src="{{ asset('images/Bubble_Bee_Laundry_logo_design-removebg-preview.png') }}" alt="Bubble Bee Laundry">
            <h1>BUBBLE BEE LAUNDRY</h1>
            <p>Customer Claim Slip</p>
            <span class="slip-badge">PENDING</span>
        </div>

        {{-- Order ID Highlight --}}
        <div class="order-id-box">
            <div class="label">Your Order ID</div>
            <div class="order-id">{{ $order->order_id }}</div>
        </div>

        <div class="slip-body">
            <div class="slip-row">
                <span class="label">Customer</span>
                <span class="value">{{ $order->customer_name ?? 'Walk-in' }}</span>
            </div>
            <div class="slip-row">
                <span class="label">Service</span>
                <span class="value">{{ $order->service }}</span>
            </div>
            <div class="slip-row">
                <span class="label">Weight</span>
                <span class="value">{{ $order->weight }} kg</span>
            </div>
            <div class="slip-row">
                <span class="label">Pick-up Date</span>
                <span class="value">{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</span>
            </div>
            <div class="slip-row">
                <span class="label">Date Ordered</span>
                <span class="value">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A') }}</span>
            </div>
            <div class="slip-row">
                <span class="label">Status</span>
                <span class="value" style="color:#f59e0b; font-weight:700;">Pending</span>
            </div>
        </div>

        {{-- PRICE BREAKDOWN BOX --}}
        @php
            $pricePerKg = $order->amount / $order->weight;
        @endphp
        <div class="price-breakdown">
            <div class="breakdown-title">Price Breakdown</div>

            <div class="breakdown-row">
                <span class="bd-label">Service</span>
                <span class="bd-value">{{ $order->service }}</span>
            </div>
            <div class="breakdown-row">
                <span class="bd-label">Price per kg</span>
                <span class="bd-value">₱{{ number_format($pricePerKg, 2) }}</span>
            </div>
            <div class="breakdown-row">
                <span class="bd-label">Weight</span>
                <span class="bd-value">{{ $order->weight }} kg</span>
            </div>

            <hr class="breakdown-divider">

            <div class="breakdown-row total-row">
                <span class="bd-label">₱{{ number_format($pricePerKg, 2) }} × {{ $order->weight }} kg</span>
                <span class="bd-value">= ₱{{ number_format($order->amount, 2) }}</span>
            </div>
        </div>

        <div class="slip-total">
            <span class="label">Total Amount</span>
            <span class="amount">₱{{ number_format($order->amount, 2) }}</span>
        </div>

        <div class="slip-note">
            <strong>Important Reminder:</strong>
            Please keep this slip and present your <strong>Order ID ({{ $order->order_id }})</strong> when claiming your laundry. Payment will be collected upon pick-up.
        </div>

        <div class="slip-footer">
            <p>Thank you for choosing Bubble Bee Laundry!<br>
            We'll take good care of your laundry.</p>
        </div>
    </div>

</div>

</body>
</html>