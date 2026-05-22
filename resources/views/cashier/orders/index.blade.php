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
                    {{-- Options are dynamically populated via JS based on service type --}}
                </select>
            </div>

            <div id="machineGroup" style="margin-bottom:8px; display:none;">
                <label style="font-size:13px; font-weight:600; color:#444; display:block; margin-bottom:6px;">
                    MACHINE <span id="machineLabel" style="font-weight:400; color:#888;">(select a washer)</span>:
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
                <p id="machineHint" style="font-size:12px; color:#888; margin-top:5px;"></p>
                <p id="noMachineWarning" style="font-size:12px; color:#dc2626; margin-top:5px; display:none;">
                    ⚠ No available machine found. Please make a machine available first.
                </p>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:20px;">
                <button type="button" onclick="closeModal()"
                    style="padding:10px 20px; background:#ccc; color:#333; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer;">
                    Cancel
                </button>
                <button type="submit" id="modalSubmitBtn"
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
                <th>Order Date</th>
                <th>Pick-up Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->order_id }}</td>
                    <td>{{ $order->customer_name ?? $order->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ str_replace('Self-Service ', '', $order->service) }}</td>
                    <td>{{ $order->weight }} kg</td>
                    <td>₱{{ number_format($order->amount, 2) }}</td>
                    <td>
                        <span class="status-text status-{{ strtolower($order->status) }}">
                            {{ $order->status === 'ready' ? 'Ready to Pick Up' : ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y') }}</td>
                    <td style="display:flex; gap:6px; flex-wrap:wrap;">

                        @if($order->status === 'claimed')
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
                            <a href="{{ route('cashier.orders.show', $order->order_id) }}" class="btn-action">View</a>
                            <form method="POST" action="{{ route('cashier.orders.complete', $order->order_id) }}"
                                onsubmit="return confirm('Mark {{ $order->order_id }} as Claimed & Completed?')">
                                @csrf
                                <button type="submit" class="btn-action" style="background:#27ae60;">
                                    Done
                                </button>
                            </form>

                        @else
                            <a href="{{ route('cashier.orders.show', $order->order_id) }}" class="btn-action">View</a>
                            <button type="button" class="btn-action"
                                style="background:#f39c12;"
                                onclick="openModal('{{ $order->order_id }}', '{{ strtolower($order->service) }}')">
                                Update Status
                            </button>
                        @endif

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding: 40px; color:#555;">No orders found.</td>
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
function getStatusOptions(serviceName) {
    const s = serviceName.toLowerCase();

    if (s.includes('wash only') || (s.includes('wash') && !s.includes('dry') && !s.includes('wash & dry'))) {
        return [
            { value: 'washing',   label: 'Washing' },
            { value: 'ready',     label: 'Ready to Pick Up' },
            { value: 'cancelled', label: 'Cancelled' },
        ];
    } else if (s.includes('dry only')) {
        return [
            { value: 'drying',    label: 'Drying' },
            { value: 'ready',     label: 'Ready to Pick Up' },
            { value: 'cancelled', label: 'Cancelled' },
        ];
    } else {
        return [
            { value: 'washing',   label: 'Washing' },
            { value: 'drying',    label: 'Drying' },
            { value: 'ready',     label: 'Ready to Pick Up' },
            { value: 'cancelled', label: 'Cancelled' },
        ];
    }
}

function openModal(orderId, serviceName) {
    document.getElementById('modalOrderId').textContent = orderId;
    document.getElementById('modalOrderIdInput').value  = orderId;

    // Reset status dropdown
    const statusSelect = document.getElementById('statusSelect');
    statusSelect.innerHTML = '<option value="" disabled selected>Select Status</option>';
    const options = getStatusOptions(serviceName || '');
    options.forEach(function(opt) {
        const el = document.createElement('option');
        el.value       = opt.value;
        el.textContent = opt.label;
        statusSelect.appendChild(el);
    });

    // Reset machine section
    document.getElementById('machineGroup').style.display   = 'none';
    document.getElementById('machineSelect').value          = '';
    document.getElementById('machineSelect').required       = false;
    document.getElementById('noMachineWarning').style.display = 'none';
    document.getElementById('modalSubmitBtn').disabled      = false;
    document.getElementById('modalSubmitBtn').style.background = '#4a90d9';

    document.getElementById('statusModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('statusModal').style.display = 'none';
}

// Close modal when clicking backdrop
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Status change handler
document.getElementById('statusSelect').addEventListener('change', function() {
    filterMachines(this.value);
});

function filterMachines(status) {
    const machineGroup   = document.getElementById('machineGroup');
    const machineSelect  = document.getElementById('machineSelect');
    const machineHint    = document.getElementById('machineHint');
    const machineLabel   = document.getElementById('machineLabel');
    const noMachineWarn  = document.getElementById('noMachineWarning');
    const submitBtn      = document.getElementById('modalSubmitBtn');
    const options        = machineSelect.querySelectorAll('option[data-type]');

    // Reset machine select
    machineSelect.value = '';

    if (status === 'washing' || status === 'drying') {
        const neededType = status === 'washing' ? 'washer' : 'dryer';

        // Show/hide options based on type AND availability
        options.forEach(opt => {
            const isMatch = opt.getAttribute('data-type') === neededType
                         && opt.getAttribute('data-status') === 'available';
            opt.hidden   = !isMatch;
            opt.disabled = !isMatch;
        });

        // Update label/hint
        machineLabel.textContent = status === 'washing' ? '(select a washer)' : '(select a dryer)';
        machineHint.textContent  = status === 'washing'
            ? 'Only available washers are shown for Washing status.'
            : 'Only available dryers are shown for Drying status.';

        machineGroup.style.display = 'block';

        // Auto-select first available matching machine
        const firstMatch = Array.from(options).find(opt => !opt.hidden && !opt.disabled);

        if (firstMatch) {
            machineSelect.value      = firstMatch.value;
            machineSelect.required   = true;
            noMachineWarn.style.display = 'none';
            submitBtn.disabled       = false;
            submitBtn.style.background = '#4a90d9';
        } else {
            // No available machine — block submission
            machineSelect.required   = false;
            noMachineWarn.style.display = 'block';
            submitBtn.disabled       = true;
            submitBtn.style.background = '#9ca3af';
        }

    } else {
        // Non-machine statuses (ready, cancelled, etc.)
        options.forEach(opt => {
            opt.hidden   = false;
            opt.disabled = false;
        });
        machineGroup.style.display      = 'none';
        machineSelect.required          = false;
        noMachineWarn.style.display     = 'none';
        submitBtn.disabled              = false;
        submitBtn.style.background      = '#4a90d9';
    }
}
</script>

@endsection