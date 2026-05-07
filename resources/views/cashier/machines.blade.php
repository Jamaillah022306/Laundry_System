@extends('layouts.cashier')

@section('title', 'Machines')

@section('content')

<h1 class="page-title">MACHINES AVAILABILITY</h1>

{{-- ===== WARNING BANNER: Due for maintenance soon ===== --}}
@if(count($maintenanceDueSoon) > 0)
    <div style="background: #fff3cd; border: 2px solid #f39c12; border-radius: 10px;
                padding: 14px 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px;">
        <span style="font-size: 22px;"></span>
        <div>
            <strong style="color: #856404; font-size: 15px;">Maintenance Due Soon!</strong>
            <div style="color: #856404; font-size: 13px; margin-top: 2px;">
                The following machines are due for maintenance within 7 days:
                <strong>
                    {{ implode(', ', array_map(fn($m) => $m->machine_number, $maintenanceDueSoon)) }}
                </strong>
                — Please schedule inspection.
            </div>
        </div>
    </div>
@endif

{{-- ===== LEGEND ===== --}}
<div style="display:flex; gap:16px; margin-bottom:20px; flex-wrap:wrap;">
    <span style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:600;">
        <span style="width:16px;height:16px;border-radius:4px;background:#7aaed4;display:inline-block;"></span> Available
    </span>
    <span style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:600;">
        <span style="width:16px;height:16px;border-radius:4px;background:#e74c3c;display:inline-block;"></span> In Use
    </span>
    <span style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:600;">
        <span style="width:16px;height:16px;border-radius:4px;background:#f39c12;display:inline-block;"></span> Under Maintenance
    </span>
    <span style="display:flex; align-items:center; gap:6px; font-size:13px; font-weight:600;">
        <span style="width:16px;height:16px;border-radius:4px;background:#f0c040;display:inline-block;"></span> Due Soon
    </span>
</div>

<div class="machines-grid">
    @forelse($machines as $machine)

        @php
            $isDueSoon = collect($maintenanceDueSoon)->contains('id', $machine->id);
        @endphp

        <div class="machine-card
            {{ $machine->status == 'in_use' ? 'machine-in-use' : '' }}
            {{ $machine->status == 'under_maintenance' ? 'machine-maintenance' : '' }}
            {{ ($isDueSoon && $machine->status == 'available') ? 'machine-due-soon' : '' }}">

            {{-- Header --}}
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div class="machine-number">{{ $machine->machine_number }}</div>
                <div style="display:flex; align-items:center; gap:5px;">
                    <span style="font-size: 11px; font-weight: 700;
                        background: rgba(255,255,255,0.3); color: inherit;
                        padding: 2px 7px; border-radius: 20px; white-space:nowrap;">
                        {{ number_format($machine->capacity_kg, 1) }} kg
                    </span>
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase;
                        background: {{ $machine->type == 'washer' ? '#2ecc71' : '#9b59b6' }};
                        color: white; padding: 3px 8px; border-radius: 20px;">
                        {{ ucfirst($machine->type) }}
                    </span>
                </div>
            </div>

            {{-- Status --}}
            <div style="margin-top: 8px;">
                <span style="font-size: 14px; font-weight: 700;">
                    @if($machine->status == 'available')
                        @if($isDueSoon) Due Soon @else Available @endif
                    @elseif($machine->status == 'in_use')
                        In Use
                    @elseif($machine->status == 'under_maintenance')
                        Under Maintenance
                    @endif
                </span>
            </div>

            {{-- Current Order --}}
            @if($machine->current_order_id)
                <div style="font-size: 12px; margin-top: 4px;">
                    Order: {{ $machine->current_order_id }}
                </div>
            @endif

            {{-- Maintenance Note --}}
            @if($machine->maintenance_note)
                <div style="font-size: 11px; margin-top: 4px; font-style: italic; opacity: 0.9;">
                    {{ $machine->maintenance_note }}
                </div>
            @endif

            {{-- Last Maintained --}}
            @if($machine->last_maintained_at)
                <div style="font-size: 11px; margin-top: 4px; opacity: 0.85;">
                    Last maintained: {{ \Carbon\Carbon::parse($machine->last_maintained_at)->format('M d, Y') }}
                </div>
            @endif

            {{-- Next Due --}}
            @if($machine->maintenance_due_at && $machine->status !== 'under_maintenance')
                <div style="font-size: 11px; margin-top: 2px; opacity: 0.85;">
                    Next due: {{ \Carbon\Carbon::parse($machine->maintenance_due_at)->format('M d, Y') }}
                </div>
            @endif

            {{-- ===== ACTIONS (hidden if in_use) ===== --}}
            @if($machine->status !== 'in_use')
                <div style="margin-top: 12px; display:flex; flex-direction:column; gap:6px;">

                    {{-- Toggle Maintenance / Mark Available --}}
                    <form method="POST" action="{{ route('cashier.machines.maintenance', $machine->id) }}">
                        @csrf @method('PATCH')
                        @if($machine->status !== 'under_maintenance')
                            <input type="text" name="note" placeholder="Reason (optional)"
                                style="width:100%; margin-bottom:5px; font-size:12px; padding:4px 8px;
                                       border-radius:6px; border:none; outline:none;">
                        @endif
                        <button type="submit" style="
                            font-size: 12px; padding: 6px 12px; border-radius: 6px;
                            border: none; cursor: pointer; font-weight: 700; color: white; width: 100%;
                            background: {{ $machine->status == 'under_maintenance' ? '#2ecc71' : '#f39c12' }};">
                            {{ $machine->status == 'under_maintenance' ? 'Mark as Available' : 'Set to Maintenance' }}
                        </button>
                    </form>

                    {{-- Report Issue (only if available or due soon) --}}
                    @if($machine->status === 'available')
                        <form method="POST" action="{{ route('cashier.machines.report-issue', $machine->id) }}">
                            @csrf
                            <input type="text" name="issue" placeholder="Describe the issue..." required
                                style="width:100%; margin-bottom:5px; font-size:12px; padding:4px 8px;
                                       border-radius:6px; border:none; outline:none;">
                            <button type="submit" style="
                                font-size: 12px; padding: 6px 12px; border-radius: 6px;
                                border: none; cursor: pointer; font-weight: 700; color: white; width: 100%;
                                background: #e74c3c;">
                                Report Issue
                            </button>
                        </form>
                    @endif

                </div>
            @endif

        </div>
    @empty
        @for($i = 1; $i <= 12; $i++)
            <div class="machine-card">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div class="machine-number">Machine {{ $i }}</div>
                    <span style="font-size: 11px; font-weight: 700; text-transform: uppercase;
                        background: {{ $i <= 6 ? '#2ecc71' : '#9b59b6' }};
                        color: white; padding: 3px 8px; border-radius: 20px;">
                        {{ $i <= 6 ? 'Washer' : 'Dryer' }}
                    </span>
                </div>
                <div style="margin-top: 8px;">
                    <span style="font-size: 14px; font-weight: 700;">Available</span>
                </div>
            </div>
        @endfor
    @endforelse
</div>

@endsection