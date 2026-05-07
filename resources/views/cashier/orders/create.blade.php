@extends('layouts.cashier')

@section('title', 'New Order')

@section('content')

<h1 class="page-title">ORDERS INFORMATION</h1>

<div class="form-wrapper">
    <form method="POST" action="{{ route('cashier.orders.store') }}">
        @csrf

        {{-- REGISTERED CUSTOMER DROPDOWN --}}
        <div class="form-group" style="margin-bottom: 20px;">
            <label>Registered Customer <span style="font-weight:400; color:#555;"></span></label>
            <select onchange="fillCustomerName(this)" class="form-control">
                <option value="" disabled selected hidden>-- Walk-in / Type manually --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}"
                        data-name="{{ $customer->name }}">
                        {{ $customer->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="customer_id" id="customer_id" value="">

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Customer Name:</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control"
                   value="{{ old('customer_name') }}" required>
            @error('customer_name') <span class="error">{{ $message }}</span> @enderror
        </div>

        {{-- SERVICE DROPDOWN with data-price --}}
        <div class="form-group" style="margin-bottom: 20px;">
            <label>Service:</label>
            <select name="service_id" id="service_id" required>
                <option value="" disabled selected hidden>Select service</option>
                @foreach($services as $service)
                    <option value="{{ $service->id }}"
                        data-price="{{ $service->price_per_kg }}"
                        {{ old('service_id') == $service->id ? 'selected' : '' }}>
                        {{ $service->service_name }}
                    </option>
                @endforeach
            </select>
            @error('service_id') <span class="error">{{ $message }}</span> @enderror
        </div>

        {{-- LAUNDRY TYPE RADIO BUTTONS --}}
        <div class="form-group" style="margin-bottom: 20px;">
            <label>Laundry Type:</label>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px;">

                @foreach([
                    'clothes'   => 'Clothes',
                    'bedsheets' => 'Bedsheets / Linens',
                    'curtains'  => 'Curtains / Drapes',
                    'stuffed'   => 'Stuffed Toys',
                    'others'    => 'Others',
                ] as $value => $label)
                <label style="
                    display: flex; align-items: center; gap: 8px;
                    background: white; border: 1.5px solid #cbd5e1;
                    border-radius: 8px; padding: 8px 16px;
                    cursor: pointer; font-size: 14px; font-weight: 500;
                    transition: border-color .15s, background .15s;
                " class="laundry-type-label">
                    <input type="radio" name="laundry_type" value="{{ $value }}"
                           {{ old('laundry_type') == $value ? 'checked' : '' }}
                           required style="accent-color: #16a34a;">
                    {{ $label }}
                </label>
                @endforeach

            </div>

            {{-- "Others" specify field --}}
            <div id="laundry_type_other_group" style="margin-top: 10px; display: {{ old('laundry_type') == 'others' ? 'block' : 'none' }};">
                <input type="text" name="laundry_type_other" class="form-control" style="width: 100%; box-sizing: border-box;"
                       placeholder="Please specify laundry type..."
                       value="{{ old('laundry_type_other') }}">
            </div>

            @error('laundry_type') <span class="error">{{ $message }}</span> @enderror
            @error('laundry_type_other') <span class="error">{{ $message }}</span> @enderror
        </div>

        {{-- PRICE PER KG DISPLAY (read-only, auto-filled) --}}
        <div class="form-group" id="price-per-kg-group" style="margin-bottom: 20px; display:none;">
            <label>Price per KG:</label>
            <input type="text" id="price_per_kg_display" class="form-control" readonly
                   style="background:#e9ecef; cursor:not-allowed;">
        </div>

        {{-- WEIGHT INPUT --}}
        <div class="form-group" style="margin-bottom: 20px;">
            <label>Weight(kg):</label>
            <input type="number" name="weight" id="weight" class="form-control" step="0.1" min="0.1"
                   value="{{ old('weight') }}" required>
            @error('weight') <span class="error">{{ $message }}</span> @enderror
        </div>

        {{-- TOTAL AMOUNT DISPLAY (read-only, auto-calculated) --}}
        <div class="form-group" id="total-price-group" style="margin-bottom: 20px; display:none;">
            <label>Total Amount:</label>
            <input type="text" id="total_price_display" class="form-control" readonly
                   style="background:#e9ecef; cursor:not-allowed;">
            {{-- This hidden field saves the computed amount to the DB --}}
            <input type="hidden" name="amount" id="amount">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Pick-up Date:</label>
            <input type="date" name="pickup_date" class="form-control"
                   value="{{ old('pickup_date') }}" required>
            @error('pickup_date') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-actions" style="display:flex; justify-content:flex-end; margin-top:2rem;">
            <button type="submit" class="btn-primary">Place Order</button>
        </div>
    </form>
</div>

<script>
// Fill customer name when selecting from registered customers
function fillCustomerName(select) {
    const option = select.options[select.selectedIndex];
    const name = option.dataset.name ?? '';
    const id = select.value;
    document.getElementById('customer_name').value = name;
    document.getElementById('customer_id').value = id;
}

// Auto-calculate total amount based on service price x weight
function calculateTotal() {
    const serviceSelect  = document.getElementById('service_id');
    const weightInput    = document.getElementById('weight');
    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];

    const pricePerKg = parseFloat(selectedOption?.getAttribute('data-price')) || 0;
    const weight     = parseFloat(weightInput.value) || 0;

    const priceGroup = document.getElementById('price-per-kg-group');
    const totalGroup = document.getElementById('total-price-group');

    // Show price per kg if a service is selected
    if (pricePerKg > 0) {
        priceGroup.style.display = 'block';
        document.getElementById('price_per_kg_display').value = '₱' + pricePerKg.toFixed(2) + ' / kg';
    } else {
        priceGroup.style.display = 'none';
    }

    // Show total amount if both service and weight are filled
    if (pricePerKg > 0 && weight > 0) {
        const total = pricePerKg * weight;
        totalGroup.style.display = 'block';
        document.getElementById('total_price_display').value = '₱' + total.toFixed(2);
        document.getElementById('amount').value = total.toFixed(2); // saved to DB
    } else {
        totalGroup.style.display = 'none';
        document.getElementById('amount').value = '';
    }
}

// Show/hide "Others" input based on laundry type selection
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('service_id').addEventListener('change', calculateTotal);
    document.getElementById('weight').addEventListener('input', calculateTotal);

    document.querySelectorAll('input[name="laundry_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const otherGroup = document.getElementById('laundry_type_other_group');
            otherGroup.style.display = this.value === 'others' ? 'block' : 'none';
            if (this.value !== 'others') {
                otherGroup.querySelector('input').value = '';
            }
        });
    });
});
</script>

@endsection