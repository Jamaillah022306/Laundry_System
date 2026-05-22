@extends('layouts.cashier')

@section('title', 'New Order')

@section('content')

<style>
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
    top: -50%; left: -50%;
    width: 200%; height: 200%;
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
.scale-label { font-size:11px; color:rgba(0,0,0,0.89); letter-spacing:2px; text-transform:uppercase; font-family:'Courier New',monospace; display:flex; align-items:center; gap:6px; }
.scale-label .dot { width:6px; height:6px; background:#48bb78; border-radius:50%; box-shadow:0 0 6px #48bb78; animation:pulse-dot 2s infinite; }
@keyframes pulse-dot { 0%,100%{opacity:1}50%{opacity:0.3} }
.scale-value-display { text-align: right; }
.scale-number { font-family:'Courier New',monospace; font-size:36px; font-weight:700; color:#000000ee; text-shadow:0 0 20px rgba(104,211,145,0.5); line-height:1; letter-spacing:2px; transition:color 0.3s; }
.scale-number.reading { color:#020100ee; animation:flicker 0.1s infinite; }
.scale-number.error-val { color:#020100ee; }
@keyframes flicker { 0%,100%{opacity:1}50%{opacity:0.7} }
.scale-unit { font-size:16px; color:#020100ee; font-family:'Courier New',monospace; margin-left:4px; }
.scale-status { font-size:11px; color:rgba(0,0,0,0.87); font-family:'Courier New',monospace; margin-top:2px; text-align:right; }
.scale-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; }
.btn-read-scale { background:linear-gradient(135deg,#2b6cb0,#3182ce); color:white; border:none; border-radius:8px; padding:10px 20px; font-size:13px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:8px; transition:all 0.2s; letter-spacing:0.5px; box-shadow:0 4px 12px rgba(49,130,206,0.4); flex:1; justify-content:center; }
.btn-read-scale:hover:not(:disabled) { background:linear-gradient(135deg,#3182ce,#4299e1); transform:translateY(-1px); }
.btn-read-scale:disabled { opacity:0.6; cursor:not-allowed; transform:none; }
.btn-clear-scale { background:rgba(73,238,7,0.88); color:rgb(0,0,0); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:10px 16px; font-size:12px; cursor:pointer; transition:all 0.2s; white-space:nowrap; }
.scale-warning { background:rgba(252,129,129,0.1); border:1px solid rgba(252,129,129,0.3); border-radius:8px; padding:8px 12px; color:#fc8181; font-size:12px; margin-top:8px; display:none; align-items:center; gap:6px; }
.scale-success { background:rgba(72,187,120,0.1); border:1px solid rgba(72,187,120,0.3); border-radius:8px; padding:8px 12px; color:#020100ee; font-size:12px; margin-top:8px; display:none; align-items:center; gap:6px; }
.scale-progress { height:3px; background:rgba(255,255,255,0.05); border-radius:2px; margin-top:10px; overflow:hidden; display:none; }
.scale-progress-bar { height:100%; background:linear-gradient(90deg,#3182ce,#63b3ed,#3182ce); background-size:200% 100%; border-radius:2px; animation:shimmer 0.8s linear infinite; width:0%; transition:width 0.1s linear; }
@keyframes shimmer { 0%{background-position:200% 0}100%{background-position:-200% 0} }
.estimated-amount-card { background:#EDE9E6; border:2px solid #EDE9E6; border-radius:12px; padding:16px 20px; display:flex; align-items:center; justify-content:space-between; animation:fadeInUp 0.3s ease; }
@keyframes fadeInUp { from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)} }
.estimated-label { font-size:13px; color:#020100ee; font-weight:600; }
.estimated-amount { font-size:28px; font-weight:800; color:#020100ee; font-family:'Courier New',monospace; }
.service-cards { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:10px; margin-top:8px; }
.service-card { border:2px solid #cbd5e1; border-radius:12px; padding:14px; cursor:pointer; transition:all 0.2s; background:white; position:relative; }
.service-card:hover { border-color:#3b82f6; background:#eff6ff; }
.service-card.selected { border-color:#16a34a; background:#f0fdf4; }
.service-card input[type="radio"] { display:none; }
.service-card-name { font-size:13px; font-weight:700; color:#1e293b; margin-bottom:4px; }
.service-card-rate { font-size:12px; color:#16a34a; font-weight:600; }
.service-card-cap { font-size:11px; color:#64748b; margin-top:2px; }
.service-card.selected::after { content:'✓'; position:absolute; top:8px; right:10px; color:#16a34a; font-weight:800; font-size:14px; }
.pricing-detail-box { display:none; margin-top:12px; background:#f0fdf4; border:1.5px solid #86efac; border-radius:10px; padding:14px 18px; animation:fadeInUp 0.2s ease; }
.pricing-detail-title { font-size:12px; font-weight:700; color:#166534; letter-spacing:1px; margin-bottom:10px; }
.pricing-detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.pricing-detail-item { background:white; border-radius:8px; padding:10px 12px; border:1px solid #bbf7d0; }
.pricing-detail-label { font-size:10px; color:#6b7280; margin-bottom:2px; text-transform:uppercase; letter-spacing:1px; }
.pricing-detail-value { font-size:15px; font-weight:700; color:#15803d; }
.pricing-detail-note { font-size:11px; color:#4b5563; margin-top:8px; line-height:1.5; }
.addons-section { background:#ffffff; border:1.5px solid #fde047; border-radius:12px; padding:16px 18px; margin-bottom:20px; }
.addons-title { font-size:13px; font-weight:700; color:#854d0e; margin-bottom:12px; letter-spacing:0.5px; }
.addon-row { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid #fef08a; }
.addon-row:last-child { border-bottom:none; }
.addon-info { display:flex; align-items:center; gap:10px; }
.addon-name { font-size:13px; font-weight:600; color:#000000; }
.addon-price { font-size:11px; color:#16a34a; font-weight:600; }
.addon-controls { display:flex; align-items:center; gap:8px; }
.addon-btn { width:28px; height:28px; border-radius:50%; border:1.5px solid #d1d5db; background:white; font-size:16px; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all 0.15s; line-height:1; color:#374151; }
.addon-btn:hover { border-color:#3b82f6; background:#eff6ff; color:#3b82f6; }
.addon-qty { font-size:14px; font-weight:700; color:#1e293b; min-width:20px; text-align:center; }
.addon-subtotal { font-size:12px; font-weight:600; color:#16a34a; min-width:45px; text-align:right; }
.addons-total-row { display:flex; justify-content:space-between; align-items:center; margin-top:10px; padding-top:10px; border-top:2px solid #fde047; }
.addons-total-label { font-size:12px; font-weight:700; color:#854d0e; }
.addons-total-amount { font-size:16px; font-weight:800; color:#854d0e; }
.laundry-type-label { display:flex; align-items:center; gap:8px; background:white; border:1.5px solid #cbd5e1; border-radius:8px; padding:8px 16px; cursor:pointer; font-size:14px; font-weight:500; transition:border-color .15s, background .15s; }
</style>

<h1 class="page-title">ORDERS INFORMATION</h1>

<div class="form-wrapper">
    <form method="POST" action="{{ route('cashier.orders.store') }}">
        @csrf

        <div class="form-group" style="margin-bottom:20px;">
            <label>Registered Customer</label>
            <select onchange="fillCustomerName(this)" class="form-control">
                <option value="" disabled selected hidden>-- Walk-in / Type manually --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" data-name="{{ $customer->name }}">{{ $customer->name }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="customer_id" id="customer_id" value="">

        <div class="form-group" style="margin-bottom:20px;">
            <label>Customer Name:</label>
            <input type="text" name="customer_name" id="customer_name" class="form-control"
                   value="{{ old('customer_name') }}" required>
            @error('customer_name') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-group" style="margin-bottom:20px;">
            <label>Service:</label>
            <div class="service-cards" id="service-cards-container">
                <label class="service-card" onclick="selectService(this, 'wash_only')">
                    <input type="radio" name="service_id" value="" data-service="wash_only">
                    <div class="service-card-name">Wash Only</div>
                    <div class="service-card-rate">₱40–₱60 / load</div>
                    <div class="service-card-cap">Up to 7–8 kg</div>
                </label>
                <label class="service-card" onclick="selectService(this, 'dry_only')">
                    <input type="radio" name="service_id" value="" data-service="dry_only">
                    <div class="service-card-name">Dry Only</div>
                    <div class="service-card-rate">₱30 / load</div>
                    <div class="service-card-cap">Per 20 minutes</div>
                </label>
                <label class="service-card" onclick="selectService(this, 'wash_dry')">
                    <input type="radio" name="service_id" value="" data-service="wash_dry">
                    <div class="service-card-name">Wash & Dry</div>
                    <div class="service-card-rate">₱60–₱90 / load</div>
                    <div class="service-card-cap">Up to 7–8 kg</div>
                </label>
                <label class="service-card" onclick="selectService(this, 'full_service')">
                    <input type="radio" name="service_id" value="" data-service="full_service">
                    <div class="service-card-name">Full Service</div>
                    <div class="service-card-rate">₱180–₱230 / load</div>
                    <div class="service-card-cap">Wash, Dry & Fold</div>
                </label>
                <label class="service-card" onclick="selectService(this, 'bulky')">
                    <input type="radio" name="service_id" value="" data-service="bulky">
                    <div class="service-card-name">Bulky Items</div>
                    <div class="service-card-rate">₱60–₱130 / kg</div>
                    <div class="service-card-cap">Comforters, blankets</div>
                </label>
            </div>
            <input type="hidden" name="service_id" id="service_id_hidden">
            @error('service_id') <span class="error">{{ $message }}</span> @enderror
            <div class="pricing-detail-box" id="pricing-detail-box">
                <div class="pricing-detail-title">PRICING DETAILS</div>
                <div class="pricing-detail-grid">
                    <div class="pricing-detail-item">
                        <div class="pricing-detail-label">Rate</div>
                        <div class="pricing-detail-value" id="pd-rate"></div>
                    </div>
                    <div class="pricing-detail-item">
                        <div class="pricing-detail-label">Capacity</div>
                        <div class="pricing-detail-value" id="pd-cap"></div>
                    </div>
                    <div class="pricing-detail-item">
                        <div class="pricing-detail-label">Min. Weight</div>
                        <div class="pricing-detail-value" id="pd-min"></div>
                    </div>
                    <div class="pricing-detail-item">
                        <div class="pricing-detail-label">Charge Basis</div>
                        <div class="pricing-detail-value" id="pd-basis"></div>
                    </div>
                </div>
                <div class="pricing-detail-note" id="pd-note"></div>
            </div>
        </div>

        {{-- LAUNDRY TYPE — dynamic based on service --}}
        <div class="form-group" style="margin-bottom:20px;">
            <label>Laundry Type:</label>
            <div id="laundry-type-options" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:8px;">
                <p style="color:#000000; font-size:13px; margin:0;">Please select a service first.</p>
            </div>
            <div id="laundry_type_other_group" style="margin-top:10px; display:none;">
                <input type="text" name="laundry_type_other" class="form-control"
                       style="width:100%; box-sizing:border-box;"
                       placeholder="Please specify laundry type..."
                       value="{{ old('laundry_type_other') }}">
            </div>
            @error('laundry_type') <span class="error">{{ $message }}</span> @enderror
        </div>

        {{-- ADD-ONS --}}
        <div class="form-group" style="margin-bottom:20px;">
            <label>Add-ons <span style="font-size:11px;color:#6b7280;font-weight:400;">(optional)</span></label>
            <div class="addons-section">
                <div class="addons-title">EXTRAS & ADD-ONS</div>
                <div class="addon-row">
                    <div class="addon-info"><div><div class="addon-name">Detergent</div><div class="addon-price">₱15 per sachet</div></div></div>
                    <div class="addon-controls">
                        <button type="button" class="addon-btn" onclick="changeAddon('detergent',-1)">−</button>
                        <span class="addon-qty" id="qty-detergent">0</span>
                        <button type="button" class="addon-btn" onclick="changeAddon('detergent',1)">+</button>
                        <span class="addon-subtotal" id="sub-detergent">₱0</span>
                    </div>
                </div>
                <div class="addon-row">
                    <div class="addon-info"><div><div class="addon-name">Fabcon / Softener</div><div class="addon-price">₱15 per sachet</div></div></div>
                    <div class="addon-controls">
                        <button type="button" class="addon-btn" onclick="changeAddon('fabcon',-1)">−</button>
                        <span class="addon-qty" id="qty-fabcon">0</span>
                        <button type="button" class="addon-btn" onclick="changeAddon('fabcon',1)">+</button>
                        <span class="addon-subtotal" id="sub-fabcon">₱0</span>
                    </div>
                </div>
                <div class="addon-row">
                    <div class="addon-info"><div><div class="addon-name">Bleach</div><div class="addon-price">₱10 per sachet</div></div></div>
                    <div class="addon-controls">
                        <button type="button" class="addon-btn" onclick="changeAddon('bleach',-1)">−</button>
                        <span class="addon-qty" id="qty-bleach">0</span>
                        <button type="button" class="addon-btn" onclick="changeAddon('bleach',1)">+</button>
                        <span class="addon-subtotal" id="sub-bleach">₱0</span>
                    </div>
                </div>
                <div class="addon-row">
                    <div class="addon-info"><div><div class="addon-name">Stain Remover</div><div class="addon-price">₱20 per sachet</div></div></div>
                    <div class="addon-controls">
                        <button type="button" class="addon-btn" onclick="changeAddon('stain',-1)">−</button>
                        <span class="addon-qty" id="qty-stain">0</span>
                        <button type="button" class="addon-btn" onclick="changeAddon('stain',1)">+</button>
                        <span class="addon-subtotal" id="sub-stain">₱0</span>
                    </div>
                </div>
                <div class="addons-total-row">
                    <span class="addons-total-label">Add-ons Total</span>
                    <span class="addons-total-amount" id="addons-total">₱0</span>
                </div>
            </div>
            <input type="hidden" name="addons_detergent" id="addons_detergent" value="0">
            <input type="hidden" name="addons_fabcon" id="addons_fabcon" value="0">
            <input type="hidden" name="addons_bleach" id="addons_bleach" value="0">
            <input type="hidden" name="addons_stain" id="addons_stain" value="0">
            <input type="hidden" name="addons_total" id="addons_total_hidden" value="0">
        </div>

        {{-- WEIGHING SCALE --}}
        <div class="form-group" style="margin-bottom:20px;">
            <label style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">Weight (kg):</label>
            <div style="display:flex;gap:8px;margin-bottom:10px;">
                <button type="button" id="btn-mode-scale" onclick="setScaleMode('scale')"
                    style="flex:1;padding:8px 12px;border-radius:8px;border:2px solid #3182ce;background:#ebf8ff;color:#2b6cb0;font-size:12px;font-weight:700;cursor:pointer;transition:all 0.2s;">
                    Read from Scale
                </button>
                <button type="button" id="btn-mode-manual" onclick="setScaleMode('manual')"
                    style="flex:1;padding:8px 12px;border-radius:8px;border:2px solid #cbd5e1;background:white;color:#64748b;font-size:12px;font-weight:700;cursor:pointer;transition:all 0.2s;">
                    Enter Manually
                </button>
            </div>
            <div id="scale-mode-panel">
                <div class="scale-widget">
                    <div class="scale-screen">
                        <div>
                            <div class="scale-label"><span class="dot"></span>SCALE</div>
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
                        <button type="button" class="btn-read-scale" id="btn-read-scale" onclick="readScale()">Read Weight from Scale</button>
                        <button type="button" class="btn-clear-scale" onclick="clearScale()">Reset</button>
                    </div>
                    <div class="scale-warning" id="scale-warning">Below minimum! Please add more laundry.</div>
                    <div class="scale-success" id="scale-success">Weight accepted! Minimum requirement met.</div>
                </div>
            </div>
            <div id="manual-mode-panel" style="display:none;">
                <div style="background:#EDE9E6;border-radius:16px;padding:20px;border:1px solid rgba(255,255,255,0.08);">
                    <div style="background:#bfd4f3e1;border-radius:10px;padding:16px 20px;margin-bottom:16px;border:1px solid rgba(99,179,237,0.2);box-shadow:inset 0 2px 8px rgba(0,0,0,0.3);">
                        <div style="font-size:11px;color:rgba(0,0,0,0.7);letter-spacing:2px;text-transform:uppercase;font-family:'Courier New',monospace;margin-bottom:10px;">MANUAL WEIGHT ENTRY</div>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <input type="number" id="manual-weight-input" min="0.1" max="100" step="0.1"
                                placeholder="0.0" oninput="applyManualWeight()"
                                style="flex:1;font-size:32px;font-weight:700;font-family:'Courier New',monospace;background:transparent;border:none;border-bottom:2px solid #3182ce;outline:none;color:#000;text-align:right;padding:4px 8px;width:100%;">
                            <span style="font-size:16px;color:#020100ee;font-family:'Courier New',monospace;">kg</span>
                        </div>
                    </div>
                    <div style="display:flex;gap:8px;">
                        <button type="button" onclick="confirmManualWeight()"
                            style="flex:1;background:linear-gradient(135deg,#2b6cb0,#3182ce);color:white;border:none;border-radius:8px;padding:10px 20px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 4px 12px rgba(49,130,206,0.4);">
                            ✓ Confirm Weight
                        </button>
                        <button type="button" onclick="clearScale()"
                            style="background:rgba(73,238,7,0.88);color:#000;border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:10px 16px;font-size:12px;cursor:pointer;">
                            Reset
                        </button>
                    </div>
                    <div id="manual-warning" style="display:none;background:rgba(252,129,129,0.1);border:1px solid rgba(252,129,129,0.3);border-radius:8px;padding:8px 12px;color:#e53e3e;font-size:12px;margin-top:8px;">
                        ⚠ Below minimum weight! Please add more laundry.
                    </div>
                    <div id="manual-success" style="display:none;background:rgba(72,187,120,0.1);border:1px solid rgba(72,187,120,0.3);border-radius:8px;padding:8px 12px;color:#276749;font-size:12px;margin-top:8px;">
                        ✓ Weight confirmed! Minimum requirement met.
                    </div>
                </div>
            </div>
            <input type="hidden" name="weight" id="weight" value="{{ old('weight') }}">
            @error('weight') <span class="error" style="color:#dc2626;">{{ $message }}</span> @enderror
        </div>

        {{-- ESTIMATED TOTAL --}}
        <div class="form-group" id="total-price-group" style="margin-bottom:20px;display:none;">
            <label>Estimated Total Amount:</label>
            <div class="estimated-amount-card">
                <div>
                    <div class="estimated-label">Estimated Amount</div>
                    <div style="font-size:11px;color:#276749;margin-top:2px;" id="total-basis-label">Based on weight × rate</div>
                    <div style="font-size:11px;color:#854d0e;margin-top:2px;" id="total-addons-label"></div>
                </div>
                <div class="estimated-amount" id="total_price_display">₱0.00</div>
            </div>
            <input type="hidden" name="amount" id="amount">
        </div>

        <div class="form-group" style="margin-bottom:20px;">
            <label>Pick-up Date:</label>
            <input type="date" name="pickup_date" class="form-control" value="{{ old('pickup_date') }}" required>
            @error('pickup_date') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="form-actions" style="display:flex;justify-content:flex-end;margin-top:2rem;">
            <button type="submit" id="submit-btn" class="btn-primary" disabled style="opacity:0.5;cursor:not-allowed;">
                Place Order
            </button>
        </div>
    </form>
</div>

<script>
const serviceData = {
    wash_only:    { rate:'₱40–₱60 / load', cap:'Up to 7–8 kg / load', min:'No minimum', basis:'Per load', note:'Washing only. No drying service included. No detergent or folding.', minKg:0, loadMin:40, loadMax:60, maxKg:7.5, type:'per_load' },
    dry_only:     { rate:'₱30 / load', cap:'Per 20 minutes', min:'No minimum', basis:'Per load', note:'Drying only. ₱30 per load or ₱30 per 20 minutes of drying time.', minKg:0, loadMin:30, loadMax:30, maxKg:7.5, type:'per_load' },
    wash_dry:     { rate:'₱60–₱90 / load', cap:'Up to 7–8 kg / load', min:'7 kg', basis:'Per load', note:'Wash & Dry combo. No soap or folding included. Great for quick turnaround.', minKg:7, loadMin:60, loadMax:90, maxKg:7.5, type:'per_load' },
    full_service: { rate:'₱180–₱230 / load', cap:'6–8 kg / load', min:'6 kg', basis:'Per load', note:'Includes washing, drying, and folding with detergent & fabcon. Per kilo rate ₱17–₱40/kg also applies for excess weight.', minKg:6, loadMin:180, loadMax:230, maxKg:7, type:'per_load' },
    bulky:        { rate:'₱60–₱130 / kg', cap:'Any weight', min:'1 kg', basis:'Per kilo', note:'For comforters, blankets, stuffed toys, and similar bulky items. Flat rate of ₱100–₱150 per piece also available.', minKg:1, kiloMin:60, kiloMax:130, type:'per_kilo' },
};

// ✅ Laundry types per service type
const laundryTypeOptions = {
    regular: [
        { value: 'clothes', label: 'Clothes' },
        { value: 'others',  label: 'Others'  },
    ],
    bulky: [
        { value: 'comforter', label: 'Comforter' },
        { value: 'blanket',   label: 'Blanket' },
        { value: 'curtains',  label: 'Curtains / Drapes' },
        { value: 'bedsheets', label: 'Bedsheets / Linens' },
        { value: 'stuffed',   label: 'Stuffed Toys' },
        { value: 'others',    label: 'Others' },
    ],
};

const serviceDbNames = {
    wash_only:    'Wash Only',
    dry_only:     'Dry Only',
    wash_dry:     'Wash & Dry',
    full_service: 'Full Service (Wash, Dry, Fold)',
    bulky:        'Bulky Items',
};

const dbServices = @json($services);
let currentService = null;

// ✅ Render laundry type options dynamically
function renderLaundryTypes(serviceKey) {
    const container  = document.getElementById('laundry-type-options');
    const otherGroup = document.getElementById('laundry_type_other_group');

    container.innerHTML = '';
    otherGroup.style.display = 'none';

    const options = serviceKey === 'bulky' ? laundryTypeOptions.bulky : laundryTypeOptions.regular;

    options.forEach(function(opt) {
        const label = document.createElement('label');
        label.className = 'laundry-type-label';
        label.innerHTML = `<input type="radio" name="laundry_type" value="${opt.value}" required style="accent-color:#16a34a;"> ${opt.label}`;
        label.querySelector('input').addEventListener('change', function() {
            otherGroup.style.display = this.value === 'others' ? 'block' : 'none';
            if (this.value !== 'others') {
                const inp = otherGroup.querySelector('input');
                if (inp) inp.value = '';
            }
        });
        container.appendChild(label);
    });
}

const addonPrices = { detergent:15, fabcon:15, bleach:10, stain:20 };
const addonQtys   = { detergent:0,  fabcon:0,  bleach:0,  stain:0  };

function changeAddon(key, delta) {
    addonQtys[key] = Math.max(0, addonQtys[key] + delta);
    const qty = addonQtys[key];
    document.getElementById('qty-' + key).textContent = qty;
    document.getElementById('sub-' + key).textContent = qty > 0 ? '₱' + (qty * addonPrices[key]) : '₱0';
    document.getElementById('addons_' + key).value = qty;
    updateAddonsTotal();
}

function updateAddonsTotal() {
    let total = 0;
    for (const k in addonQtys) total += addonQtys[k] * addonPrices[k];
    document.getElementById('addons-total').textContent = '₱' + total;
    document.getElementById('addons_total_hidden').value = total;
    calculateTotal();
}

function selectService(labelEl, serviceKey) {
    document.querySelectorAll('.service-card').forEach(c => c.classList.remove('selected'));
    labelEl.classList.add('selected');
    currentService = serviceKey;

    const dbName  = serviceDbNames[serviceKey];
    const matched = dbServices.find(s => s.service_name === dbName);
    document.getElementById('service_id_hidden').value = matched ? matched.id : '';

    const d = serviceData[serviceKey];
    document.getElementById('pd-rate').textContent  = d.rate;
    document.getElementById('pd-cap').textContent   = d.cap;
    document.getElementById('pd-min').textContent   = d.min;
    document.getElementById('pd-basis').textContent = d.basis;
    document.getElementById('pd-note').textContent  = d.note;
    document.getElementById('pricing-detail-box').style.display = 'block';

    // ✅ Update laundry types based on selected service
    renderLaundryTypes(serviceKey);
    calculateTotal();
}

function calculateTotal() {
    const weight     = parseFloat(document.getElementById('weight').value) || 0;
    const addonsAmt  = parseInt(document.getElementById('addons_total_hidden').value) || 0;
    const submitBtn  = document.getElementById('submit-btn');
    const totalGroup = document.getElementById('total-price-group');

    if (!currentService || weight <= 0) {
        totalGroup.style.display = 'none';
        document.getElementById('amount').value = '';
        submitBtn.disabled = true; submitBtn.style.opacity = '0.5'; submitBtn.style.cursor = 'not-allowed';
        return;
    }

    const d = serviceData[currentService];
    let estMin = 0, estMax = 0, basisText = '';

    if (d.type === 'per_load') {
        const loads = Math.max(1, Math.ceil(weight / (d.maxKg || 8)));
        estMin = loads * d.loadMin; estMax = loads * d.loadMax;
        basisText = `${loads} load${loads>1?'s':''} × ₱${d.loadMin}–₱${d.loadMax}`;
    } else {
        estMin = weight * d.kiloMin; estMax = weight * d.kiloMax;
        basisText = `${weight} kg × ₱${d.kiloMin}–₱${d.kiloMax}/kg`;
    }

    const totalMin = estMin + addonsAmt;
    const totalMax = estMax + addonsAmt;
    const estAvg   = ((totalMin + totalMax) / 2).toFixed(2);
    const displayText = totalMin === totalMax ? `₱${totalMin.toFixed(2)}` : `₱${totalMin.toFixed(2)} – ₱${totalMax.toFixed(2)}`;

    totalGroup.style.display = 'block';
    document.getElementById('total_price_display').textContent = displayText;
    document.getElementById('total-basis-label').textContent   = basisText;
    document.getElementById('amount').value = estAvg;

    const addonsLabel = document.getElementById('total-addons-label');
    if (addonsAmt > 0) { addonsLabel.textContent = `+ ₱${addonsAmt} add-ons`; addonsLabel.style.display = 'block'; }
    else { addonsLabel.style.display = 'none'; }

    if (weight >= (d.minKg || 0)) { submitBtn.disabled = false; submitBtn.style.opacity = '1'; submitBtn.style.cursor = 'pointer'; }
    else { submitBtn.disabled = true; submitBtn.style.opacity = '0.5'; submitBtn.style.cursor = 'not-allowed'; }
}

function fillCustomerName(select) {
    const option = select.options[select.selectedIndex];
    document.getElementById('customer_name').value = option.dataset.name ?? '';
    document.getElementById('customer_id').value   = select.value;
}

let scaleReading = false;

function readScale() {
    if (scaleReading) return;
    scaleReading = true;
    const btn=document.getElementById('btn-read-scale'),display=document.getElementById('scale-display'),
          status=document.getElementById('scale-status'),progress=document.getElementById('scale-progress'),
          progressBar=document.getElementById('scale-progress-bar'),warning=document.getElementById('scale-warning'),
          success=document.getElementById('scale-success');
    warning.style.display='none'; success.style.display='none';
    btn.disabled=true; btn.textContent='Reading...';
    progress.style.display='block'; progressBar.style.width='0%';
    status.textContent='Connecting to scale...';
    display.className='scale-number reading'; display.textContent='- - -';
    let prog=0;
    const progInterval=setInterval(()=>{ prog+=Math.random()*8+2; if(prog>=100){prog=100;clearInterval(progInterval);} progressBar.style.width=prog+'%'; },80);
    setTimeout(()=>{
        status.textContent='Measuring weight...';
        let fc=0;
        const fi=setInterval(()=>{
            display.textContent=(Math.random()*12+5).toFixed(1); fc++;
            if(fc>=18){
                clearInterval(fi);
                const fw=(Math.random()*8+7).toFixed(1);
                display.textContent=fw; progressBar.style.width='100%';
                setTimeout(()=>{
                    progress.style.display='none';
                    const w=parseFloat(fw);
                    const minKg=currentService?(serviceData[currentService]?.minKg||0):7;
                    if(w<minKg&&minKg>0){ display.className='scale-number error-val'; status.textContent='⚠ Below minimum weight'; warning.style.display='flex'; }
                    else { display.className='scale-number'; status.textContent='Reading complete ✓'; success.style.display='flex'; }
                    document.getElementById('weight').value=fw;
                    btn.disabled=false; btn.textContent='Re-read Scale'; scaleReading=false;
                    calculateTotal();
                },400);
            }
        },80);
    },800);
}

let currentMode = 'scale';
function setScaleMode(mode) {
    currentMode=mode;
    const scalePanel=document.getElementById('scale-mode-panel'),manualPanel=document.getElementById('manual-mode-panel');
    const btnScale=document.getElementById('btn-mode-scale'),btnManual=document.getElementById('btn-mode-manual');
    clearScale();
    if(mode==='scale'){
        scalePanel.style.display='block'; manualPanel.style.display='none';
        btnScale.style.borderColor='#3182ce'; btnScale.style.background='#ebf8ff'; btnScale.style.color='#2b6cb0';
        btnManual.style.borderColor='#cbd5e1'; btnManual.style.background='white'; btnManual.style.color='#64748b';
    } else {
        scalePanel.style.display='none'; manualPanel.style.display='block';
        btnManual.style.borderColor='#3182ce'; btnManual.style.background='#ebf8ff'; btnManual.style.color='#2b6cb0';
        btnScale.style.borderColor='#cbd5e1'; btnScale.style.background='white'; btnScale.style.color='#64748b';
        document.getElementById('manual-weight-input').focus();
    }
}

function applyManualWeight() {
    const val=parseFloat(document.getElementById('manual-weight-input').value)||0;
    document.getElementById('weight').value=val>0?val.toFixed(1):'';
    document.getElementById('manual-warning').style.display='none';
    document.getElementById('manual-success').style.display='none';
    calculateTotal();
}

function confirmManualWeight() {
    const val=parseFloat(document.getElementById('manual-weight-input').value)||0;
    if(val<=0){ document.getElementById('manual-warning').style.display='block'; document.getElementById('manual-warning').textContent='⚠ Please enter a valid weight.'; return; }
    const minKg=currentService?(serviceData[currentService]?.minKg||0):0;
    document.getElementById('weight').value=val.toFixed(1);
    document.getElementById('manual-warning').style.display='none';
    document.getElementById('manual-success').style.display='none';
    if(val<minKg&&minKg>0){ document.getElementById('manual-warning').style.display='block'; document.getElementById('manual-warning').textContent=`⚠ Below minimum! This service requires at least ${minKg} kg.`; }
    else { document.getElementById('manual-success').style.display='block'; }
    calculateTotal();
}

function clearScale() {
    document.getElementById('weight').value='';
    const sd=document.getElementById('scale-display'); if(sd){sd.textContent='- - -'; sd.className='scale-number';}
    const ss=document.getElementById('scale-status'); if(ss) ss.textContent='Ready to weigh';
    const sw=document.getElementById('scale-warning'); if(sw) sw.style.display='none';
    const sc=document.getElementById('scale-success'); if(sc) sc.style.display='none';
    const sp=document.getElementById('scale-progress'); if(sp) sp.style.display='none';
    const br=document.getElementById('btn-read-scale'); if(br){br.textContent='⚖ Read Weight from Scale'; br.disabled=false;}
    const mw=document.getElementById('manual-weight-input'); if(mw) mw.value='';
    const mwarn=document.getElementById('manual-warning'); if(mwarn) mwarn.style.display='none';
    const msucc=document.getElementById('manual-success'); if(msucc) msucc.style.display='none';
    document.getElementById('total-price-group').style.display='none';
    document.getElementById('amount').value='';
    const s=document.getElementById('submit-btn');
    s.disabled=true; s.style.opacity='0.5'; s.style.cursor='not-allowed';
    scaleReading=false;
}

document.addEventListener('DOMContentLoaded', function () {
    const oldWeight=parseFloat("{{ old('weight', '0') }}")||0;
    if(oldWeight>0){
        document.getElementById('scale-display').textContent=oldWeight.toFixed(1);
        document.getElementById('scale-status').textContent='Previous reading restored';
        document.getElementById('weight').value=oldWeight;
        if(oldWeight>=7){ document.getElementById('scale-success').style.display='flex'; }
        else { document.getElementById('scale-warning').style.display='flex'; document.getElementById('scale-display').className='scale-number error-val'; }
        calculateTotal();
    }
});
</script>

@endsection