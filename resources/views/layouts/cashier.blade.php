<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bubble Bee Laundry - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <style>
        .navbar-nav .nav-link.active {
            background-color: rgba(0, 0, 0, 0.849) !important;
            text-decoration: none !important;
            color: yellow !important;
            font-weight: 700 !important;
            
            padding-bottom: 4px !important;
            margin-bottom: -4px !important;
        }

        .notif-wrapper { position: relative; display: inline-block; }

        .notif-bell {
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 8px;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            position: relative;
            transition: background 0.2s;
        }
        .notif-bell:hover { background: rgba(255,255,255,0.35); }

        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #e74c3c;
            color: white;
            font-size: 11px;
            font-weight: 700;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .notif-dropdown {
            display: none;
            position: absolute;
            top: 52px;
            right: 0;
            width: 320px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            z-index: 999;
            overflow: hidden;
        }
        .notif-dropdown.open { display: block; }

        .notif-header {
            background: #4a90d9;
            padding: 12px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif-header span { color: white; font-weight: 700; font-size: 14px; }

        .notif-mark-all {
            color: #f5e642;
            font-size: 12px;
            cursor: pointer;
            background: none;
            border: none;
            font-weight: 600;
        }
        .notif-mark-all:hover { text-decoration: underline; }

        .notif-list { max-height: 320px; overflow-y: auto; }

        .notif-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background 0.15s;
            display: flex;
            gap: 10px;
            align-items: flex-start;
            text-decoration: none;
            color: inherit;
        }
        .notif-item:hover { background: #f8f9fa; }
        .notif-item.unread { background: #eef4ff; border-left: 3px solid #4a90d9; }
        .notif-item.unread:hover { background: #ddeeff; }

        .notif-icon { font-size: 20px; margin-top: 2px; }
        .notif-content { flex: 1; }
        .notif-message { font-size: 13px; color: #1a1a2e; font-weight: 500; line-height: 1.4; }
        .notif-time { font-size: 11px; color: #888; margin-top: 4px; }
        .notif-view-link {
            font-size: 11px;
            color: #4a90d9;
            font-weight: 700;
            margin-top: 4px;
            display: inline-block;
        }

        .notif-empty { padding: 30px; text-align: center; color: #888; font-size: 14px; }
        .notif-empty-icon { font-size: 32px; display: block; margin-bottom: 8px; }
    </style>
</head>
<body>
<nav class="navbar">
    <a href="{{ route('cashier.dashboard') }}" class="navbar-brand">
        <img src="{{ asset('images/Bubble_Bee_Laundry_logo_design-removebg-preview.png') }}" alt="Bubble Bee Laundry">
    </a>
    <ul class="navbar-nav">
        <li>
            <a href="{{ route('cashier.dashboard') }}" class="nav-link {{ request()->routeIs('cashier.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i> Home
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.orders.create') }}" class="nav-link {{ request()->routeIs('cashier.orders.create') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i> New Order
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.orders.index') }}" class="nav-link {{ request()->routeIs('cashier.orders.index') || request()->routeIs('cashier.orders.show') ? 'active' : '' }}">
                <i class="fa-solid fa-box"></i> Orders
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.machines') }}" class="nav-link {{ request()->routeIs('cashier.machines') ? 'active' : '' }}">
                <i class="fa-solid fa-gear"></i> Machines
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.customers.index') }}" class="nav-link {{ request()->routeIs('cashier.customers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i> Customers
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.payments.index') }}" class="nav-link {{ request()->routeIs('cashier.payments.*') ? 'active' : '' }}">
                <i class="fa-solid fa-credit-card"></i> Payments
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.orders.archived') }}" class="nav-link {{ request()->routeIs('cashier.orders.archived') ? 'active' : '' }}">
                <i class="fa-solid fa-box-archive"></i> Archived
            </a>
        </li>
        <li>
            <a href="{{ route('cashier.reports') }}" class="nav-link {{ request()->routeIs('cashier.reports') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-bar"></i> Reports
            </a>
        </li>
    </ul>
    <div class="navbar-right">

        {{-- NOTIFICATION BELL --}}
        <div class="notif-wrapper">
            <button class="notif-bell" id="notifBell" onclick="toggleNotif()">
                <i class="fa-solid fa-bell"></i>
                <span class="notif-badge" id="notifBadge">0</span>
            </button>

            <div class="notif-dropdown" id="notifDropdown">
                <div class="notif-header">
                    <span><i class="fa-solid fa-bell"></i> Notifications</span>
                    <button class="notif-mark-all" onclick="markAllRead()">Mark all as read</button>
                </div>
                <div class="notif-list" id="notifList">
                    <div class="notif-empty">
                        <span class="notif-empty-icon"><i class="fa-solid fa-bell"></i></span>
                        No notifications yet
                    </div>
                </div>
            </div>
        </div>

        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <span class="user-role">CASHIER</span>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Log out
            </button>
        </form>
    </div>
</nav>

<div class="main-content">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @yield('content')
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

function toggleNotif() {
    const dropdown = document.getElementById('notifDropdown');
    dropdown.classList.toggle('open');
    if (dropdown.classList.contains('open')) {
        loadNotifications();
    }
}

document.addEventListener('click', function(e) {
    const wrapper = document.querySelector('.notif-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('notifDropdown').classList.remove('open');
    }
});

function loadNotifications() {
    fetch('/cashier/notifications')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('notifList');
            if (data.length === 0) {
                list.innerHTML = `
                    <div class="notif-empty">
                        <span class="notif-empty-icon"><i class="fa-solid fa-bell"></i></span>
                        No notifications yet
                    </div>`;
                return;
            }
            list.innerHTML = data.map(n => `
                <div class="notif-item ${n.is_read ? '' : 'unread'}"
                     onclick="viewNotif(${n.id}, '${n.order_id}', this)">
                    <div class="notif-icon">${getIcon(n.message)}</div>
                    <div class="notif-content">
                        <div class="notif-message">${n.message}</div>
                        <div class="notif-time">${timeAgo(n.created_at)}</div>
                        ${n.order_id ? `<span class="notif-view-link">View order →</span>` : ''}
                    </div>
                </div>
            `).join('');
        });
}

function viewNotif(id, orderId, el) {
    fetch(`/cashier/notifications/${id}/read`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
    }).then(() => {
        el.classList.remove('unread');
        updateBadge();
        if (orderId && orderId !== 'null') {
            window.location.href = `/cashier/orders/${orderId}`;
        }
    });
}

function markAllRead() {
    fetch('/cashier/notifications/mark-all-read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json' }
    }).then(() => {
        document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
        updateBadge();
    });
}

function updateBadge() {
    fetch('/cashier/notifications/unread-count')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('notifBadge');
            if (data.count > 0) {
                badge.style.display = 'flex';
                badge.textContent = data.count > 9 ? '9+' : data.count;
            } else {
                badge.style.display = 'none';
            }
        });
}

function getIcon(message) {
    if (message.includes('claimed'))   return '<i class="fa-solid fa-hand"></i>';
    if (message.includes('washing'))   return '<i class="fa-solid fa-soap"></i>';
    if (message.includes('ready'))     return '<i class="fa-solid fa-circle-check"></i>';
    if (message.includes('completed')) return '<i class="fa-solid fa-star"></i>';
    if (message.includes('cancelled')) return '<i class="fa-solid fa-circle-xmark"></i>';
    if (message.includes('placed'))    return '<i class="fa-solid fa-file-lines"></i>';
    if (message.includes('payment'))   return '<i class="fa-solid fa-credit-card"></i>';
    return '<i class="fa-solid fa-bell"></i>';
}

function timeAgo(dateStr) {
    const date = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    if (diff < 60) return 'Just now';
    if (diff < 3600) return Math.floor(diff / 60) + ' min ago';
    if (diff < 86400) return Math.floor(diff / 3600) + ' hrs ago';
    return Math.floor(diff / 86400) + ' days ago';
}

updateBadge();
setInterval(updateBadge, 30000);
</script>
</body>
</html>