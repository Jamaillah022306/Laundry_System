@extends('layouts.cashier')

@section('title', 'New Order')

@section('content')

<h1 class="page-title">Orders Information</h1>

<div class="form-card">
    <form action="{{ route('cashier.orders.store') }}" method="POST">
        @csrf

        {{-- CUSTOMER SELECTION --}}
        <div class="form-row" style="margin-bottom:30px;">
            <div class="form-group">
                <label>CUSTOMER:</label>
                <select name="customer_id" id="customerSelect" onchange="fillCustomerInfo()" required>
                    <option value="" disabled selected>-- Select Registered Customer --</option>
                    <option value="walk_in" {{ old('customer_id') == 'walk_in' ? 'selected' : '' }}>
                        Walk-in (Not Registered)
                    </option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                                data-name="{{ $customer->name }}"
                                data-phone="{{ $customer->phone }}"
                                {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} ({{ $customer->username }})
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('cashier.customers.create') }}"
                   style="font-size:12px; color:#1a4fa0; margin-top:5px; display:inline-block;">
                    + Register New Customer
                </a>
                @error('customer_id')
                    <span style="color:#c0392b; font-size:13px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>PHONE NUMBER:</label>
                <input type="text" name="phone_number" id="phoneInput"
                       value="{{ old('phone_number') }}"
                       placeholder="Auto-filled or enter manually" required>
                @error('phone_number')
                    <span style="color:#c0392b; font-size:13px;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>CUSTOMER NAME:</label>
                <input type="text" name="customer_name" id="nameInput"
                       value="{{ old('customer_name') }}"
                       placeholder="Auto-filled or enter manually" required>
                @error('customer_name')
                    <span style="color:#c0392b; font-size:13px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>SERVICE:</label>
                <select name="service" required>
                    <option value="" disabled selected></option>
                    <option value="Wash Only" {{ old('service') == 'Wash Only' ? 'selected' : '' }}>Wash Only</option>
                    <option value="Dry Only" {{ old('service') == 'Dry Only' ? 'selected' : '' }}>Dry Only</option>
                    <option value="Wash & Dry" {{ old('service') == 'Wash & Dry' ? 'selected' : '' }}>Wash & Dry</option>
                    <option value="Wash, Dry & Fold" {{ old('service') == 'Wash, Dry & Fold' ? 'selected' : '' }}>Wash, Dry & Fold</option>
                    <option value="Full Service" {{ old('service') == 'Full Service' ? 'selected' : '' }}>Full Service</option>
                </select>
                @error('service')
                    <span style="color:#c0392b; font-size:13px;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>WEIGHT (kg):</label>
                <input type="number" name="weight" step="0.1" min="0"
                       value="{{ old('weight') }}" required>
                @error('weight')
                    <span style="color:#c0392b; font-size:13px;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>PICK-UP DATE:</label>
                <input type="date" name="pickup_date"
                       value="{{ old('pickup_date') }}" required>
                @error('pickup_date')
                    <span style="color:#c0392b; font-size:13px;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-footer" style="margin-top:40px;">
            <button type="submit" class="btn-primary">Place Order</button>
        </div>
    </form>
</div>

<script>
function fillCustomerInfo() {
    const select = document.getElementById('customerSelect');
    const selectedOption = select.options[select.selectedIndex];

    if (selectedOption.value === 'walk_in' || selectedOption.value === '') {
        document.getElementById('nameInput').value = '';
        document.getElementById('phoneInput').value = '';
        document.getElementById('nameInput').readOnly = false;
        document.getElementById('phoneInput').readOnly = false;
    } else {
        const name = selectedOption.getAttribute('data-name');
        const phone = selectedOption.getAttribute('data-phone');
        document.getElementById('nameInput').value = name || '';
        document.getElementById('phoneInput').value = phone || '';
        document.getElementById('nameInput').readOnly = true;
        document.getElementById('phoneInput').readOnly = true;
    }
}
</script>

@endsection
