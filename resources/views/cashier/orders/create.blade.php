@extends('layouts.cashier')

@section('title', 'New Order')

@section('content')

<style>
/* ===== SCALE WIDGET STYLES ===== */
.scale-widget {
    background: #EDE9E6;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 8px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.3), inset 0 1px 0 rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.08);
    position: relative;
    overflow: hidden;
}

.scale-widget::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle at 30% 30%, rgba(99,179,237,0.05) 0%, transparent 60%);
    pointer-events: none;
}

.scale-screen {
    background: #bfd4f3e1;
    border-radius: 10px;
    padding: 16px 20px;
    margin-bottom: 16px;
    border: 1px solid rgba(99,179,237,0.2);
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.5), 0 0 20px rgba(99,179,237,0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 70px;
}

.scale-label {
    font-size: 11px;
    color: rgba(0, 0, 0, 0.89);
    letter-spacing: 2px;
    text-transform: uppercase;
    font-family: 'Courier New', monospace;
    display: flex;
    align-items: center;
    gap: 6px;
}

.scale-label .dot {
    width: 6px;
    height: 6px;
    background: #48bb78;
    border-radius: 50%;
    box-shadow: 0 0 6px #48bb78;
    animation: pulse-dot 2s infinite;
}

@keyframes pulse-dot {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

.scale-value-display {
    text-align: right;
}

.scale-number {
    font-family: 'Courier New', monospace;
    font-size: 36px;
    font-weight: 700;
    color: #000000ee;
    text-shadow: 0 0 20px rgba(104,211,145,0.5);
    line-height: 1;
    letter-spacing: 2px;
    transition: color 0.3s;
}

.scale-number.reading {
    color: #020100ee;
    text-shadow: 0 0 20px #020100ee;
    animation: flicker 0.1s infinite;
}

.scale-number.error-val {
    color: #020100ee;
    text-shadow: 0 0 20px #020100ee;
}

@keyframes flicker {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.scale-unit {
    font-size: 16px;
    color: #020100ee;
    font-family: 'Courier New', monospace;
    margin-left: 4px;
}

.scale-status {
    font-size: 11px;
    color: rgba(0, 0, 0, 0.87);
    font-family: 'Courier New', monospace;
    margin-top: 2px;
    text-align: right;
}

.scale-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.btn-read-scale {
    background: linear-gradient(135deg, #2b6cb0, #3182ce);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(49,130,206,0.4);
    flex: 1;
    justify-content: center;
}

.btn-read-scale:hover:not(:disabled) {
    background: linear-gradient(135deg, #3182ce, #4299e1);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(49,130,206,0.5);
}

.btn-read-scale:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.btn-clear-scale {
    background: rgba(73, 238, 7, 0.88);
    color: rgb(0, 0, 0);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 10px 16px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.scale-warning {
    background: rgba(252,129,129,0.1);
    border: 1px solid rgba(252,129,129,0.3);
    border-radius: 8px;
    padding: 8px 12px;
    color: #fc8181;
    font-size: 12px;
    margin-top: 8px;
    display: none;
    align-items: center;
    gap: 6px;
}

.scale-success {
    background: rgba(72,187,120,0.1);
    border: 1px solid rgba(72,187,120,0.3);
    border-radius: 8px;
    padding: 8px 12px;
    color: #020100ee;
    font-size: 12px;
    margin-top: 8px;
    display: none;
    align-items: center;
    gap: 6px;
}

.scale-progress {
    height: 3px;
    background: rgba(255,255,255,0.05);
    border-radius: 2px;
    margin-top: 10px;
    overflow: hidden;
    display: none;
}

.scale-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #3182ce, #63b3ed, #3182ce);
    background-size: 200% 100%;
    border-radius: 2px;
    animation: shimmer 0.8s linear infinite;
    width: 0%;
    transition: width 0.1s linear;
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.estimated-amount-card {
    background: #EDE9E6;
    border: 2px solid #EDE9E6;
    border-radius: 12px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}

.estimated-label {
    font-size: 13px;
    color: #020100ee;
    font-weight: 600;
}

.estimated-amount {
    font-size: 28px;
    font-weight: 800;
    color: #020100ee;
    font-family: 'Courier New', monospace;
}
</style>

<h1 class="page-title">ORDERS INFORMATION</h1>

<div class="form-wrapper">
    <form method="POST" action="{{ route('cashier.orders.store') }}">
        @csrf

        {{-- REGISTERED CUSTOMER DROPDOWN --}}
        <div class="form-group" style="margin-bottom: 20px;">
            <label>Registered Customer</label>
            <select onchange="fillCustomerName(this)" class="form-control">
                <option value="" disabled selected hidden>-- Walk-in / Type manually --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">
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

        {{-- SERVICE DROPDOWN --}}
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

        {{-- LAUNDRY TYPE --}}
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

            <div id="laundry_type_other_group" style="margin-top: 10px; display: {{ old('laundry_type') == 'others' ? 'block' : 'none' }};">
                <input type="text" name="laundry_type_other" class="form-control"
                       style="width: 100%; box-sizing: border-box;"
                       placeholder="Please specify laundry type..."
                       value="{{ old('laundry_type_other') }}">
            </div>

            @error('laundry_type') <span class="error">{{ $message }}</span> @enderror
            @error('laundry_type_other') <span class="error">{{ $message }}</span> @enderror
        </div>

        {{-- PRICE PER KG --}}
        <div class="form-group" id="price-per-kg-group" style="margin-bottom: 20px; display:none;">
            <label>Price per KG:</label>
            <input type="text" id="price_per_kg_display" class="form-control" readonly
                   style="background:#e9ecef; cursor:not-allowed;">
        </div>

        {{-- ===== WEIGHING SCALE WIDGET ===== --}}
        <div class="form-group" style="margin-bottom: 20px;">
            <label style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                Weight (kg):
            </label>

            <div class="scale-widget">
                <div class="scale-screen">
                    <div>
                        <div class="scale-label">
                            <span class="dot"></span>
                            SCALE
                        </div>
                        <div class="scale-status" id="scale-status"></div>
                    </div>
                    <div class="scale-value-display">
                        <span class="scale-number" id="scale-display">- - -</span>
                        <span class="scale-unit">kg</span>
                    </div>
                </div>

                <div class="scale-progress" id="scale-progress">
                    <div class="scale-progress-bar" id="scale-progress-bar"></div>
                </div>

                <div class="scale-footer" style="margin-top:12px;">
                    <button type="button" class="btn-read-scale" id="btn-read-scale" onclick="readScale()">
                        Read Weight from Scale
                    </button>
                    <button type="button" class="btn-clear-scale" onclick="clearScale()">Reset</button>
                </div>

                <div class="scale-warning" id="scale-warning">
                    Below minimum! Weight must be at least <strong>7 kg</strong>. Please add more laundry.
                </div>
                <div class="scale-success" id="scale-success">
                    Weight accepted! Minimum requirement met.
                </div>
            </div>

            <input type="hidden" name="weight" id="weight" value="{{ old('weight') }}">
            @error('weight') <span class="error" style="color:#dc2626;">{{ $message }}</span> @enderror
        </div>

        {{-- ESTIMATED TOTAL --}}
        <div class="form-group" id="total-price-group" style="margin-bottom: 20px; display:none;">
            <label>Estimated Total Amount:</label>
            <div class="estimated-amount-card">
                <div>
                    <div class="estimated-label">Estimated Amount</div>
                    <div style="font-size:11px; color:#276749; margin-top:2px;">Based on weight × price per kg</div>
                </div>
                <div class="estimated-amount" id="total_price_display">₱0.00</div>
            </div>
            <input type="hidden" name="amount" id="amount">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Pick-up Date:</label>
            <input type="date" name="pickup_date" class="form-control"
                   value="{{ old('pickup_date') }}" required>
            @error('pickup_date') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-actions" style="display:flex; justify-content:flex-end; margin-top:2rem;">
            <button type="submit" id="submit-btn" class="btn-primary"
                    disabled style="opacity:0.5; cursor:not-allowed;">
                Place Order
            </button>
        </div>
    </form>
</div>

<script>
function fillCustomerName(select) {
    const option = select.options[select.selectedIndex];
    document.getElementById('customer_name').value = option.dataset.name ?? '';
    document.getElementById('customer_id').value = select.value;
}

let scaleReading = false;

function readScale() {
    if (scaleReading) return;
    scaleReading = true;

    const btn         = document.getElementById('btn-read-scale');
    const display     = document.getElementById('scale-display');
    const status      = document.getElementById('scale-status');
    const progress    = document.getElementById('scale-progress');
    const progressBar = document.getElementById('scale-progress-bar');
    const warning     = document.getElementById('scale-warning');
    const success     = document.getElementById('scale-success');

    warning.style.display = 'none';
    success.style.display = 'none';

    btn.disabled = true;
    btn.textContent = 'Reading...';

    progress.style.display = 'block';
    progressBar.style.width = '0%';

    status.textContent = 'Connecting to scale...';
    display.className = 'scale-number reading';
    display.textContent = '- - -';

    // Animate progress bar
    let prog = 0;
    const progInterval = setInterval(() => {
        prog += Math.random() * 8 + 2;
        if (prog >= 100) { prog = 100; clearInterval(progInterval); }
        progressBar.style.width = prog + '%';
    }, 80);

    // Phase 2: flickering numbers after 800ms
    setTimeout(() => {
        status.textContent = 'Measuring weight...';
        let flickerCount = 0;
        const flickerInterval = setInterval(() => {
            const randVal = (Math.random() * 12 + 5).toFixed(1);
            display.textContent = randVal;
            flickerCount++;

            if (flickerCount >= 18) {
                clearInterval(flickerInterval);

                // Final settled value: 7.0 – 15.0 kg
                const finalWeight = (Math.random() * 8 + 7).toFixed(1);
                display.textContent = finalWeight;
                progressBar.style.width = '100%';

                setTimeout(() => {
                    progress.style.display = 'none';

                    const w = parseFloat(finalWeight);
                    if (w < 7) {
                        display.className = 'scale-number error-val';
                        status.textContent = '⚠ Below minimum weight';
                        warning.style.display = 'flex';
                    } else {
                        display.className = 'scale-number';
                        status.textContent = 'Reading complete ✓';
                        success.style.display = 'flex';
                    }

                    document.getElementById('weight').value = finalWeight;
                    btn.disabled = false;
                    btn.textContent = 'Re-read Scale';
                    scaleReading = false;

                    calculateTotal();
                }, 400);
            }
        }, 80);
    }, 800);
}

function clearScale() {
    document.getElementById('weight').value = '';
    document.getElementById('scale-display').textContent = '- - -';
    document.getElementById('scale-display').className = 'scale-number';
    document.getElementById('scale-status').textContent = 'Ready to weigh';
    document.getElementById('scale-warning').style.display = 'none';
    document.getElementById('scale-success').style.display = 'none';
    document.getElementById('scale-progress').style.display = 'none';
    document.getElementById('btn-read-scale').textContent = '⚖ Read Weight from Scale';
    document.getElementById('btn-read-scale').disabled = false;
    document.getElementById('total-price-group').style.display = 'none';
    document.getElementById('amount').value = '';

    const submitBtn = document.getElementById('submit-btn');
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.5';
    submitBtn.style.cursor = 'not-allowed';
    scaleReading = false;
}

function calculateTotal() {
    const serviceSelect  = document.getElementById('service_id');
    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
    const weight         = parseFloat(document.getElementById('weight').value) || 0;
    const pricePerKg     = parseFloat(selectedOption?.getAttribute('data-price')) || 0;
    const submitBtn      = document.getElementById('submit-btn');
    const totalGroup     = document.getElementById('total-price-group');
    const priceGroup     = document.getElementById('price-per-kg-group');

    if (pricePerKg > 0) {
        priceGroup.style.display = 'block';
        document.getElementById('price_per_kg_display').value = '₱' + pricePerKg.toFixed(2) + ' / kg';
    } else {
        priceGroup.style.display = 'none';
    }

    if (pricePerKg > 0 && weight >= 7) {
        const total = pricePerKg * weight;
        totalGroup.style.display = 'block';
        document.getElementById('total_price_display').textContent = '₱' + total.toFixed(2);
        document.getElementById('amount').value = total.toFixed(2);
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
        submitBtn.style.cursor = 'pointer';
    } else {
        totalGroup.style.display = 'none';
        document.getElementById('amount').value = '';
        submitBtn.disabled = true;
        submitBtn.style.opacity = '0.5';
        submitBtn.style.cursor = 'not-allowed';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('service_id').addEventListener('change', calculateTotal);

    document.querySelectorAll('input[name="laundry_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const otherGroup = document.getElementById('laundry_type_other_group');
            otherGroup.style.display = this.value === 'others' ? 'block' : 'none';
            if (this.value !== 'others') {
                otherGroup.querySelector('input').value = '';
            }
        });
    });

    // Restore old() weight if validation failed
    const oldWeight = parseFloat("{{ old('weight', '0') }}") || 0;
    if (oldWeight > 0) {
        document.getElementById('scale-display').textContent = oldWeight.toFixed(1);
        document.getElementById('scale-status').textContent = 'Previous reading restored';
        if (oldWeight >= 7) {
            document.getElementById('scale-success').style.display = 'flex';
        } else {
            document.getElementById('scale-warning').style.display = 'flex';
            document.getElementById('scale-display').className = 'scale-number error-val';
        }
        calculateTotal();
    }
});
</script>

@endsection