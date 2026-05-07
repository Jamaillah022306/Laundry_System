@extends('layouts.customer')

@section('title', 'Track Order')

@section('content')

<h1 class="page-title">TRACKING ORDERS</h1>

<!-- SEARCH -->
<div class="track-search-card">
    <div class="form-group" style="margin-bottom:0; flex:1;">
        <input
            type="text"
            id="order_id"
            style="width:100%; background:white; border:none; border-radius:8px; padding:14px 16px; font-size:15px; outline:none; color:#1a1a2e;"
            placeholder="Enter order ID..."
            value="{{ request('order_id') }}"
        >
    </div>
    <button onclick="trackOrder()" class="btn-primary" style="white-space:nowrap; height:50px; align-self:flex-end; padding: 0 30px;">
        Track
    </button>
</div>

<!-- RESULTS TABLE -->
<div class="table-wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>ORDER ID</th>
                <th>SERVICE</th>
                <th>STATUS</th>
                <th>PICK-UP DATE</th>
            </tr>
        </thead>
        <tbody id="trackResults">
            @forelse($trackedOrders ?? [] as $order)
                <tr>
                    <td>{{ $order->order_id }}</td>
                    <td>{{ $order->service }}</td>
                    <td>
                        <span class="status-text status-{{ strtolower($order->status) }}">
                            {{ $order->status === 'ready' ? 'Ready to Pick Up' : ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="padding:20px; color:#1a1a2e; text-align:center;">
                        Enter an Order ID to track your laundry
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function trackOrder() {
    const orderId = document.getElementById('order_id').value.trim();
    if (!orderId) return;
    window.location.href = '{{ route("customer.track") }}?order_id=' + encodeURIComponent(orderId);
}

document.getElementById('order_id').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') trackOrder();
});

// Auto-search kung naa'y order_id sa URL (gikan sa Track button sa dashboard)
document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);
    const orderId = params.get('order_id');
    if (orderId && '{{ count($trackedOrders ?? []) }}' === '0') {
        trackOrder();
    }
});
</script>

@endsection