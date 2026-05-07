@extends('layouts.cashier')

@section('title', 'Customers')

@section('content')

<h1 class="page-title">CUSTOMER LIST</h1>

{{-- SEARCH BAR + REGISTER BUTTON --}}
<div style="background:#7aaed4; border-radius:12px; padding:20px 25px; margin-bottom:20px; display:flex; gap:15px; align-items:flex-end;">
    <div style="flex:1;">
        <input type="text" id="searchInput" onkeyup="searchTable()"
               placeholder="Search by name, username, or phone..."
               style="width:100%; background:white; border:none; border-radius:8px; padding:12px 16px; font-size:14px; outline:none;">
    </div>
    <a href="{{ route('cashier.customers.create') }}" class="btn-primary" style="text-decoration:none; padding:12px 24px; white-space:nowrap;">
        + Register Customer
    </a>
</div>

<div class="table-wrapper">
    <table class="data-table" id="customerTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>CUSTOMER NAME</th>
                <th>USERNAME</th>
                <th>EMAIL</th>
                <th>TOTAL ORDERS</th>
                <th>ACTION</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $customer)
            <tr>
                <td>{{ $customer->id }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->username }}</td>
                <td>{{ $customer->email ?? 'N/A' }}</td>
                <td>{{ $customer->orders_count ?? 0 }}</td>
                <td>
                    <a href="{{ route('cashier.customers.show', $customer->id) }}" class="btn-action">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; padding:30px; color:#1a1a2e;">
                    No customers registered yet.
                    <a href="{{ route('cashier.customers.create') }}" style="color:#1a4fa0; font-weight:700;">Register one now →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
function searchTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#customerTable tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? '' : 'none';
    });
}
</script>

@endsection