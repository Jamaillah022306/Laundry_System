@extends('layouts.cashier')

@section('title', 'Orders')

@section('content')

<h1 class="page-title">ORDER LIST</h1>

{{-- UPDATE STATUS MODAL --}}
<div id="statusModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:white; border-radius:12px; padding:30px; width:420px; box-shadow:0 8px 30px rgba(0,0,0,0.2);">
        <h2 style="margin-bottom:6px; font-size:18px; color:#1a1a2e;">Update Order Status</h2>
        <p style="font-size:13px; color:#666; margin-bottom:20px;">Order: <strong id="modalOrderId"></strong></p>

        <form method="POST" action="{{ route('cashier.update-status.store') }}">
            @csrf
            <input type="hidden" name="order_id" id="modalOrderIdInput">

            <div style="margin-bottom:16px;">
                <label style="font-size:13px; font-weight:600; color:#444; display:block; margin-bottom:6px;">NEW STATUS:</label>
                <select name="status" id="statusSelect" required
                    style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                    <option value="" disabled selected>Select Status</option>
                    <option value="pending">Pending</option>
                    <option value="washing">Washing</option>
                    <option value="drying">Drying</option>
                    <option value="ready">Ready to Pick Up</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div style="margin-bottom:8px;">
                <label style="font-size:13px; font-weight:600; color:#444; display:block; margin-bottom:6px;">
                    MACHINE <span id="machineLabel" style="font-weight:400; color:#888;">(optional)</span>:
                </label>
                <select name="machine_number" id="machineSelect"
                    style="width:100%; padding:10px 12px; border:1px solid #ddd; border-radius:8px; font-size:14px;">
                    <option value="">-- No Machine --</option>
                    @foreach($machines as $machine)
                        <option value="{{ $machine->machine_number }}"
                            data-type="{{ $machine->type }}"
                            data-status="{{ $machine->status }}">
                            Machine {{ $machine->machine_number }}
                            ({{ ucfirst($machine->type) }} — {{ ucfirst(str_replace('_', ' ', $machine->status)) }})
                        </option>
                    @endforeach
                </select>
                <p id="machineHint" style="font-size:12px; color:#888; margin-top:5px; display:none;"></p>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button" onclick="closeModal()"
                    style="padding:10px 20px; background:#ccc; color:#333; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                    style="padding:10px 20px; background:#4a90d9; color:white; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

<div class="table-full">
    <table class="data-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Service</th>
                <th>Weight</th>
                <th>Amount</th>
                <th>Status</th>
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
                    <td>
                        <span class="status-text status-{{ strtolower($order->status) }}">
                            {{ $order->status === 'ready' ? 'Ready to Pick Up' : ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</td>
                    <td style="display:flex; gap:6px; flex-wrap:wrap;">

                        @if($order->status === 'claimed' && !$order->customer_id)
                            {{-- Walk-in claimed: one click Claimed & Complete --}}
                            <a href="{{ route('cashier.orders.show', $order->order_id) }}" class="btn-action">View</a>
                            <form method="POST" action="{{ route('cashier.orders.complete', $order->order_id) }}">
                                @csrf
                                <button type="submit" class="btn-action"
                                    style="background:#27ae60;"
                                    onclick="return confirm('Mark {{ $order->order_id }} as completed?')">
                                    Complete
                                </button>
                            </form>

                        @elseif($order->status === 'claimed' && $order->customer_id)
                            {{-- Registered customer claimed: show View + Complete --}}
                            <a href="{{ route('cashier.orders.show', $order->order_id) }}" class="btn-action">View</a>
                            <form method="POST" action="{{ route('cashier.orders.complete', $order->order_id) }}">
                                @csrf
                                <button type="submit" class="btn-action"
                                    style="background:#27ae60;"
                                    onclick="return confirm('Mark {{ $order->order_id }} as completed?')">
                                    Complete
                                </button>
                            </form>

                        @elseif(in_array($order->status, ['completed', 'cancelled']))
                            {{-- Completed/Cancelled: View + Archive --}}
                            <a href="{{ route('cashier.orders.show', $order->order_id) }}" class="btn-action">View</a>
                            <form method="POST" action="{{ route('cashier.orders.archive', $order->order_id) }}">
                                @csrf
                                <button type="submit" class="btn-action"
                                    style="background:#7f8c8d;"
                                    onclick="return confirm('Archive order {{ $order->order_id }}?')">
                                    Archive
                                </button>
                            </form>

                        @elseif($order->status === 'ready' && !$order->customer_id)
                            {{-- Walk-in ready: show Claimed & Complete button --}}
                            <a href="{{ route('cashier.orders.show', $order->order_id) }}" class="btn-action">View</a>
                            <form method="POST" action="{{ route('cashier.orders.complete', $order->order_id) }}"
                                onsubmit="return confirm('Mark {{ $order->order_id }} as Claimed & Completed?')">
                                @csrf
                                {{-- We need to set claimed first then complete, use a dedicated route or just complete directly --}}
                                <button type="submit" class="btn-action" style="background:#27ae60;">
                                    Done
                                </button>
                            </form>

                        @else
                            {{-- All other statuses: View + Update Status --}}
                            <a href="{{ route('cashier.orders.show', $order->order_id) }}" class="btn-action">View</a>
                            <button type="button" class="btn-action"
                                style="background:#f39c12;"
                                onclick="openModal('{{ $order->order_id }}')">
                                Update Status
                            </button>
                        @endif

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center; padding: 40px; color:#555;">No orders found.</td>
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

<script>
function openModal(orderId) {
    document.getElementById('modalOrderId').textContent = orderId;
    document.getElementById('modalOrderIdInput').value = orderId;

    document.getElementById('statusSelect').value = '';
    document.getElementById('machineSelect').value = '';
    filterMachines('');

    const modal = document.getElementById('statusModal');
    modal.style.display = 'flex';
}

function closeModal() {
    document.getElementById('statusModal').style.display = 'none';
}

document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function filterMachines(status) {
    const machineSelect = document.getElementById('machineSelect');
    const machineHint   = document.getElementById('machineHint');
    const machineLabel  = document.getElementById('machineLabel');
    const options       = machineSelect.querySelectorAll('option');

    machineSelect.value = '';

    if (status === 'washing') {
        options.forEach(opt => {
            if (!opt.value) return;
            const type    = opt.getAttribute('data-type');
            const mStatus = opt.getAttribute('data-status');
            opt.style.display = (type === 'washer') ? '' : 'none';
            opt.disabled = (mStatus !== 'available');
        });
        machineHint.textContent = 'Only washers are shown for Washing status.';
        machineHint.style.display = 'block';
        machineLabel.textContent = '(select a washer)';

    } else if (status === 'drying') {
        options.forEach(opt => {
            if (!opt.value) return;
            const type    = opt.getAttribute('data-type');
            const mStatus = opt.getAttribute('data-status');
            opt.style.display = (type === 'dryer') ? '' : 'none';
            opt.disabled = (mStatus !== 'available');
        });
        machineHint.textContent = 'Only dryers are shown for Drying status.';
        machineHint.style.display = 'block';
        machineLabel.textContent = '(select a dryer)';

    } else {
        options.forEach(opt => {
            if (!opt.value) return;
            opt.style.display = '';
            opt.disabled = false;
        });
        machineHint.style.display = 'none';
        machineLabel.textContent = '(optional)';
    }
}

document.getElementById('statusSelect').addEventListener('change', function() {
    filterMachines(this.value);
});
</script>

@endsection