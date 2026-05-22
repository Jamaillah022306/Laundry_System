@extends('layouts.cashier')

@section('title', 'Update Status')

@section('content')

<h1 class="page-title">UPDATE STATUS INFORMATION</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="form-card">
    <form method="POST" action="{{ route('cashier.update-status.store') }}" id="updateStatusForm">
        @csrf

        <!-- Order ID -->
        <div class="form-group" style="margin-bottom: 25px;">
            <label class="form-label" for="order_id">ORDER ID:</label>
            <input
                type="text"
                id="order_id"
                name="order_id"
                class="form-control"
                placeholder="Enter Order ID"
                value="{{ request('order_id') ?? old('order_id') }}"
                required
                oninput="fetchOrderWeight(this.value)"
            >
            @error('order_id')
                <span style="color:#991B1B; font-size:12px;">{{ $message }}</span>
            @enderror

            {{-- Order weight display (auto-filled after typing order ID) --}}
            <div id="order_weight_display" style="display:none; margin-top:6px;
                font-size:13px; color:#374151; font-weight:500;">
                📦 Order Weight: <span id="order_weight_value">—</span> kg
            </div>
        </div>

        <!-- New Status -->
        <div class="form-group" style="margin-bottom: 25px;">
            <label class="form-label" for="status">NEW STATUS:</label>
            <select id="status" name="status" class="form-control" required onchange="filterMachines()">
                <option value="" disabled selected hidden>Select Status</option>
                <option value="pending"   {{ old('status') == 'pending'   ? 'selected' : '' }}>Pending</option>
                <option value="washing"   {{ old('status') == 'washing'   ? 'selected' : '' }}>Washing</option>
                <option value="drying"    {{ old('status') == 'drying'    ? 'selected' : '' }}>Drying</option>
                <option value="ready"     {{ old('status') == 'ready'     ? 'selected' : '' }}>Ready for Pick-up</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')
                <span style="color:#991B1B; font-size:12px;">{{ $message }}</span>
            @enderror
        </div>

        <!-- Machine Number Dropdown -->
        <div class="form-group" id="machine_group" style="margin-bottom: 25px; display:none;">
            <label class="form-label" for="machine_number">MACHINE NUMBER:</label>
            <select id="machine_number" name="machine_number" class="form-control" onchange="checkCapacity()">
                <option value="" disabled selected hidden>-- None --</option>
                @foreach($machines as $machine)
                    <option value="{{ $machine->machine_number }}"
                        data-type="{{ $machine->type }}"
                        data-status="{{ $machine->status }}"
                        data-capacity="{{ $machine->capacity_kg }}"
                        {{ old('machine_number') == $machine->machine_number ? 'selected' : '' }}>
                        {{ $machine->machine_number }} — {{ ucfirst($machine->type) }}
                        ({{ $machine->status == 'available' ? 'Available' : ($machine->status == 'in_use' ? 'In Use - ' . $machine->current_order_id : ucfirst($machine->status)) }})
                        — Max: {{ number_format($machine->capacity_kg, 1) }} kg
                    </option>
                @endforeach
            </select>
            @error('machine_number')
                <span style="color:#991B1B; font-size:12px;">{{ $message }}</span>
            @enderror

            {{-- Capacity Warning Banner --}}
            <div id="capacity_warning" style="display:none; margin-top:10px;
                background:#FEF3C7; border:2px solid #F59E0B;
                border-radius:8px; padding:12px 16px;">
                <div style="font-weight:700; color:#92400E; font-size:14px; margin-bottom:2px;">
                    ⚠️ Capacity Exceeded!
                </div>
                <div id="capacity_warning_text" style="color:#92400E; font-size:13px;"></div>
                <div style="color:#92400E; font-size:12px; margin-top:4px; font-style:italic;">
                    Please choose a different machine or split the order into two.
                </div>
            </div>

            {{-- Capacity OK Banner --}}
            <div id="capacity_ok" style="display:none; margin-top:10px;
                background:#DCFCE7; border:2px solid #16a34a;
                border-radius:8px; padding:10px 16px;">
                <div style="font-weight:700; color:#166534; font-size:13px;">
                    ✅ <span id="capacity_ok_text"></span>
                </div>
            </div>
        </div>

        <div class="form-footer" style="margin-top: 150px;">
            <button type="submit" id="submitBtn" style="
                background-color: #16a34a;
                color: white;
                padding: 10px 30px;
                border: none;
                border-radius: 8px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                transition: background-color 0.2s;
            "
            onmouseover="if(!this.disabled) this.style.backgroundColor='#15803d'"
            onmouseout="if(!this.disabled) this.style.backgroundColor='#16a34a'">
                Update Status
            </button>
        </div>
    </form>
</div>

<script>
// FIX: Use $orders passed from controller instead of calling DB::select() in blade
const ordersData = @json(collect($orders)->keyBy('order_id'));

let currentOrderWeight = null;

// Fetch order weight when order ID is typed
function fetchOrderWeight(orderId) {
    orderId = orderId.trim().toUpperCase();
    const order = ordersData[orderId];
    if (order) {
        currentOrderWeight = parseFloat(order.weight);
        document.getElementById('order_weight_display').style.display = 'block';
        document.getElementById('order_weight_value').textContent = currentOrderWeight.toFixed(2);
        checkCapacity();
    } else {
        currentOrderWeight = null;
        document.getElementById('order_weight_display').style.display = 'none';
        hideCapacityBanners();
    }
}

function filterMachines() {
    const status = document.getElementById('status').value;
    const machineGroup = document.getElementById('machine_group');
    const options = document.querySelectorAll('#machine_number option[data-type]');

    if (status === 'washing' || status === 'drying') {
        machineGroup.style.display = 'block';

        options.forEach(option => {
            const type    = option.getAttribute('data-type');
            const mStatus = option.getAttribute('data-status');

            const isRightType = (status === 'washing' && type === 'washer') ||
                                (status === 'drying'  && type === 'dryer');

            if (isRightType && mStatus === 'available') {
                option.style.display = '';
                option.disabled      = false;
                option.hidden        = false;
            } else {
                option.style.display = 'none';
                option.disabled      = true;
                option.hidden        = true;
                option.selected      = false;
            }
        });

        // Auto-select first available matching machine
        const firstMatch = document.querySelector(
            '#machine_number option[data-type]:not([disabled]):not([hidden])'
        );
        if (firstMatch) {
            firstMatch.selected = true;
            checkCapacity();
        } else {
            document.getElementById('machine_number').value = '';
            hideCapacityBanners();
        }

    } else {
        machineGroup.style.display = 'none';
        document.getElementById('machine_number').value = '';
        hideCapacityBanners();
        enableSubmit();
    }
}

function checkCapacity() {
    const select   = document.getElementById('machine_number');
    const selected = select.options[select.selectedIndex];

    if (!selected || !selected.getAttribute('data-capacity')) {
        hideCapacityBanners();
        enableSubmit();
        return;
    }

    const capacity    = parseFloat(selected.getAttribute('data-capacity'));
    const machineName = selected.value;

    if (currentOrderWeight === null) {
        hideCapacityBanners();
        enableSubmit();
        return;
    }

    if (currentOrderWeight > capacity) {
        const excess = (currentOrderWeight - capacity).toFixed(2);
        document.getElementById('capacity_warning_text').textContent =
            `${machineName} max capacity is ${capacity.toFixed(1)} kg, but this order is ${currentOrderWeight.toFixed(2)} kg (exceeds by ${excess} kg).`;
        document.getElementById('capacity_warning').style.display = 'block';
        document.getElementById('capacity_ok').style.display      = 'none';
        blockSubmit();
    } else {
        const remaining = (capacity - currentOrderWeight).toFixed(2);
        document.getElementById('capacity_ok_text').textContent =
            `${machineName} can handle this order. (${currentOrderWeight.toFixed(2)} kg / ${capacity.toFixed(1)} kg — ${remaining} kg remaining)`;
        document.getElementById('capacity_ok').style.display      = 'block';
        document.getElementById('capacity_warning').style.display = 'none';
        enableSubmit();
    }
}

function hideCapacityBanners() {
    document.getElementById('capacity_warning').style.display = 'none';
    document.getElementById('capacity_ok').style.display      = 'none';
}

function blockSubmit() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.style.backgroundColor = '#9CA3AF';
    btn.style.cursor = 'not-allowed';
}

function enableSubmit() {
    const btn = document.getElementById('submitBtn');
    btn.disabled = false;
    btn.style.backgroundColor = '#16a34a';
    btn.style.cursor = 'pointer';
}
</script>

@endsection