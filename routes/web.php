<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerManagementController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () { return redirect('/login'); });
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', function () { return view('auth.forgot-password'); })->name('password.request');

// CASHIER
Route::prefix('cashier')->name('cashier.')->middleware(['auth', 'role:cashier'])->group(function () {
    Route::get('/dashboard', [CashierController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [CashierController::class, 'orderIndex'])->name('orders.index');
    Route::get('/orders/create', [CashierController::class, 'orderCreate'])->name('orders.create');
    Route::get('/orders/archived', [CashierController::class, 'orderArchived'])->name('orders.archived');
    Route::post('/orders', [CashierController::class, 'orderStore'])->name('orders.store');
    Route::get('/orders/{id}/claim-slip', [CashierController::class, 'claimSlip'])->name('orders.claim-slip');
    Route::get('/orders/{id}', [CashierController::class, 'orderShow'])->name('orders.show');
    Route::post('/orders/{id}/complete', [CashierController::class, 'orderComplete'])->name('orders.complete');
    Route::post('/orders/{id}/archive', [CashierController::class, 'orderArchive'])->name('orders.archive');
    Route::get('/update-status', [CashierController::class, 'updateStatus'])->name('update-status');
    Route::post('/update-status', [CashierController::class, 'updateStatusStore'])->name('update-status.store');
    Route::get('/machines', [CashierController::class, 'machines'])->name('machines');
    Route::patch('/machines/{id}/maintenance', [CashierController::class, 'machineMaintenance'])->name('machines.maintenance');
    Route::post('/machines/{id}/report-issue', [CashierController::class, 'machineReportIssue'])->name('machines.report-issue');
    Route::get('/customers', [CustomerManagementController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerManagementController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerManagementController::class, 'store'])->name('customers.store');
    Route::get('/customers/{id}', [CustomerManagementController::class, 'show'])->name('customers.show');
    Route::get('/payments', [CashierController::class, 'paymentIndex'])->name('payments.index');
    Route::get('/payments/{id}', [CashierController::class, 'paymentShow'])->name('payments.show');
    Route::post('/payments/{id}/mark-paid', [CashierController::class, 'markPaid'])->name('payments.mark-paid');
    Route::get('/payments/{id}/receipt', [CashierController::class, 'paymentReceipt'])->name('payments.receipt');
    Route::get('/reports/pdf', [CashierController::class, 'exportReportPdf'])->name('reports.pdf');
    Route::get('/reports', [CashierController::class, 'reports'])->name('reports');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
});

// CUSTOMER
Route::prefix('customer')->name('customer.')->middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/track', [CustomerController::class, 'track'])->name('track');
    Route::get('/track/search', [CustomerController::class, 'trackSearch'])->name('track.search');
    Route::get('/history', [CustomerController::class, 'history'])->name('history');
    Route::get('/receipt/{paymentId}', [CustomerController::class, 'receipt'])->name('receipt');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
    Route::post('/orders/{id}/claim', [CustomerController::class, 'claimOrder'])->name('orders.claim');
});

Route::get('/home', function () {
    return match(Auth::user()->role) {
        'cashier'  => redirect()->route('cashier.dashboard'),
        'customer' => redirect()->route('customer.dashboard'),
        default    => redirect()->route('login'),
    };
})->name('home')->middleware('auth');