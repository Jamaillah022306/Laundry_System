<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class CashierController extends Controller
{
    public function dashboard()
    {
        $today = now()->toDateString();

        $todaySales = DB::selectOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments WHERE DATE(created_at) = ? AND status = 'paid'",
            [$today]
        )->total;

        $pendingPayment = DB::selectOne(
            "SELECT COUNT(*) as total FROM payments WHERE status = 'unpaid'"
        )->total;

        $newToday = DB::selectOne(
            "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = ?",
            [$today]
        )->total;

        $avgOrderValue = DB::selectOne(
            "SELECT COALESCE(AVG(amount), 0) as total FROM orders WHERE DATE(created_at) = ?",
            [$today]
        )->total;

        $recentOrders = DB::select(
            "SELECT o.*, COALESCE(u.name, o.customer_name, 'Walk-in') as customer_name
             FROM orders o
             LEFT JOIN users u ON o.customer_id = u.id
             ORDER BY o.created_at DESC
             LIMIT 5"
        );

        $pendingPickup = DB::select(
            "SELECT o.*, COALESCE(u.name, o.customer_name, 'Walk-in') as customer_name
             FROM orders o
             LEFT JOIN users u ON o.customer_id = u.id
             WHERE o.status = 'ready'
             ORDER BY o.created_at DESC
             LIMIT 5"
        );

        // ===== AUTO-FLAG OVERDUE MACHINES AS UNDER MAINTENANCE =====
        DB::update(
            "UPDATE machines 
             SET status = 'under_maintenance', maintenance_note = 'Auto: Scheduled maintenance due. Please inspect before use.'
             WHERE maintenance_due_at IS NOT NULL 
               AND maintenance_due_at <= NOW() 
               AND status = 'available'"
        );

        return view('cashier.dashboard', compact(
            'todaySales', 'pendingPayment', 'newToday',
            'avgOrderValue', 'recentOrders', 'pendingPickup'
        ));
    }

    public function orderIndex()
    {
        $perPage    = 5;
        $page       = request()->get('page', 1);
        $offset     = ($page - 1) * $perPage;

        $orders = DB::select(
            "SELECT o.*, COALESCE(u.name, o.customer_name, 'Walk-in') as customer_name
            FROM orders o
            LEFT JOIN users u ON o.customer_id = u.id
            WHERE o.status != 'archived'
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        $total = DB::selectOne(
            "SELECT COUNT(*) as total FROM orders WHERE status != 'archived'"
        )->total;

        $orders = new \Illuminate\Pagination\LengthAwarePaginator($orders, $total, $perPage, $page, [
            'path' => request()->url(),
        ]);

        $machines = DB::select("SELECT * FROM machines ORDER BY machine_number");

        return view('cashier.orders.index', compact('orders', 'machines'));
    }

    public function orderCreate()
    {
        $customers = DB::select("SELECT * FROM users WHERE role = 'customer' ORDER BY name");
        $services  = DB::select("SELECT * FROM services ORDER BY id");
        return view('cashier.orders.create', compact('customers', 'services'));
    }

    public function orderStore(Request $request)
    {
        $validated = $request->validate([
            'customer_name'      => 'required|string|max:255',
            'service_id'         => 'required|exists:services,id',
            'laundry_type'       => 'required|string',
            'laundry_type_other' => 'nullable|string|max:255',
            'weight'             => 'required|numeric|min:0.1',
            'pickup_date'        => 'required|date',
        ]);

        $service = DB::selectOne("SELECT * FROM services WHERE id = ?", [$validated['service_id']]);
        $amount  = $service->price_per_kg * $validated['weight'];

        $customerId = null;
        if ($request->customer_id && $request->customer_id !== 'walk_in') {
            $customerId = $request->customer_id;
        } else {
            $matched = DB::selectOne(
                "SELECT id FROM users WHERE role = 'customer' AND name = ? LIMIT 1",
                [$request->customer_name]
            );
            if ($matched) $customerId = $matched->id;
        }

        $lastOrder = DB::selectOne("SELECT order_id FROM orders ORDER BY id DESC LIMIT 1");
        $nextNum   = $lastOrder ? (intval(substr($lastOrder->order_id, 4)) + 1) : 1;
        $orderId   = 'ORD-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

        $laundryType = $validated['laundry_type'];
        $laundryTypeOther = ($laundryType === 'others') ? ($validated['laundry_type_other'] ?? null) : null;

        DB::insert(
            "INSERT INTO orders (order_id, customer_id, customer_name, service_id, service, laundry_type, laundry_type_other, weight, pickup_date, amount, status, received_at, cashier_id, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), ?, NOW(), NOW())",
            [$orderId, $customerId, $validated['customer_name'], $service->id, $service->service_name, $laundryType, $laundryTypeOther, $validated['weight'], $validated['pickup_date'], $amount, auth()->id()]
        );

        $lastPayment = DB::selectOne("SELECT payment_id FROM payments ORDER BY id DESC LIMIT 1");
        $nextPayNum  = $lastPayment ? (intval(substr($lastPayment->payment_id, 4)) + 1) : 1;
        $paymentId   = 'PAY-' . str_pad($nextPayNum, 3, '0', STR_PAD_LEFT);

        DB::insert(
            "INSERT INTO payments (payment_id, order_id, amount, method, status, created_at, updated_at)
             VALUES (?, ?, ?, 'Cash', 'unpaid', NOW(), NOW())",
            [$paymentId, $orderId, $amount]
        );

        if ($customerId) {
            Notification::sendTo($customerId, "Your order {$orderId} has been placed! Service: {$service->service_name}. We'll notify you of updates.", $orderId);
        }

        return redirect()->route('cashier.orders.claim-slip', $orderId);
    }

    public function claimSlip($id)
    {
        $order = DB::selectOne(
            "SELECT o.*, COALESCE(o.customer_name, u.name, 'Walk-in') as display_name
             FROM orders o
             LEFT JOIN users u ON o.customer_id = u.id
             WHERE o.order_id = ?",
            [$id]
        );
        abort_if(!$order, 404);
        return view('cashier.orders.claim-slip', compact('order'));
    }

    public function orderShow($id)
    {
        $order = DB::selectOne(
            "SELECT o.*, COALESCE(o.customer_name, u.name, 'Walk-in') as customer_name,
                    u.email as customer_email,
                    p.payment_id, p.amount as payment_amount, p.method, p.status as payment_status,
                    p.reference_number, p.paid_at
             FROM orders o
             LEFT JOIN users u ON o.customer_id = u.id
             LEFT JOIN payments p ON o.order_id = p.order_id
             WHERE o.order_id = ?",
            [$id]
        );
        abort_if(!$order, 404);
        return view('cashier.orders.show', compact('order'));
    }

    public function orderArchived()
    {
        $perPage = 5;
        $page    = request()->get('page', 1);
        $offset  = ($page - 1) * $perPage;

        $orders = DB::select(
            "SELECT o.*, COALESCE(u.name, o.customer_name, 'Walk-in') as customer_name
            FROM orders o
            LEFT JOIN users u ON o.customer_id = u.id
            WHERE o.status = 'archived'
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        $total = DB::selectOne(
            "SELECT COUNT(*) as total FROM orders WHERE status = 'archived'"
        )->total;

        $orders = new \Illuminate\Pagination\LengthAwarePaginator($orders, $total, $perPage, $page, [
            'path' => request()->url(),
        ]);

        return view('cashier.orders.archived', compact('orders'));
    }

    public function orderArchive($id)
    {
        $order = DB::selectOne("SELECT * FROM orders WHERE order_id = ?", [$id]);
        abort_if(!$order, 404);
        abort_if(
            !in_array($order->status, ['completed', 'cancelled']),
            403,
            'Only completed or cancelled orders can be archived.'
        );

        DB::update(
            "UPDATE orders SET status = 'archived', updated_at = NOW() WHERE order_id = ?",
            [$id]
        );

        return redirect()->route('cashier.orders.index')
                         ->with('success', "Order {$id} has been archived!");
    }

    public function updateStatus()
    {
        $machines = DB::select("SELECT * FROM machines ORDER BY machine_number");
        return view('cashier.update-status', compact('machines'));
    }

    public function updateStatusStore(Request $request)
    {
        $request->validate([
            'order_id'       => 'required|string|exists:orders,order_id',
            'status'         => 'required|in:pending,washing,drying,ready,claimed,completed,cancelled',
            'machine_number' => 'nullable|string',
        ]);

        $order = DB::selectOne("SELECT * FROM orders WHERE order_id = ?", [$request->order_id]);
        abort_if(!$order, 404);

        // ===== CAPACITY CHECK: before assigning machine =====
        if ($request->machine_number && in_array($request->status, ['washing', 'drying'])) {
            $machine = DB::selectOne(
                "SELECT * FROM machines WHERE machine_number = ?",
                [$request->machine_number]
            );

            if ($machine && $order->weight > $machine->capacity_kg) {
                return back()->withErrors([
                    'machine_number' => "⚠ The order weight ({$order->weight} kg) exceeds the capacity of {$machine->machine_number} ({$machine->capacity_kg} kg). Please split the order or choose a larger machine."
                ])->withInput();
            }
        }

        $washingAt = $request->status === 'washing'   ? now() : $order->washing_at;
        $readyAt   = $request->status === 'ready'     ? now() : $order->ready_at;
        $claimedAt = $request->status === 'completed' ? now() : $order->claimed_at;

        DB::update(
            "UPDATE orders SET status = ?, washing_at = ?, ready_at = ?, claimed_at = ?, updated_at = NOW() WHERE order_id = ?",
            [$request->status, $washingAt, $readyAt, $claimedAt, $request->order_id]
        );

        $terminalStatuses = ['ready', 'completed', 'cancelled'];

        if (in_array($request->status, $terminalStatuses)) {
            DB::update(
                "UPDATE machines SET status = 'available', current_order_id = NULL WHERE current_order_id = ?",
                [$request->order_id]
            );
        } else {
            if ($request->machine_number) {
                $newMachine = DB::selectOne(
                    "SELECT * FROM machines WHERE machine_number = ?",
                    [$request->machine_number]
                );

                if ($newMachine) {
                    if ($order->machine_id && $order->machine_id != $newMachine->id) {
                        DB::update(
                            "UPDATE machines SET status = 'available', current_order_id = NULL WHERE id = ?",
                            [$order->machine_id]
                        );
                    }

                    DB::update(
                        "UPDATE orders SET machine_id = ? WHERE order_id = ?",
                        [$newMachine->id, $request->order_id]
                    );

                    DB::update(
                        "UPDATE machines SET status = 'in_use', current_order_id = ? WHERE id = ?",
                        [$request->order_id, $newMachine->id]
                    );
                }
            }
        }

        if ($order->customer_id) {
            $messages = [
                'washing'   => "Your order {$order->order_id} is now being washed!",
                'drying'    => "Your order {$order->order_id} is now being dried!",
                'ready'     => "Your order {$order->order_id} is ready for pick-up!",
                'completed' => "Your order {$order->order_id} has been completed. Thank you!",
                'cancelled' => "Your order {$order->order_id} has been cancelled.",
            ];
            if (isset($messages[$request->status])) {
                Notification::sendTo($order->customer_id, $messages[$request->status], $order->order_id);
            }
        }

        return redirect()->route('cashier.orders.index')
                         ->with('success', "Order {$order->order_id} status updated to " . ucfirst($request->status) . "!");
    }

    public function machines()
    {
        $machines = DB::select("SELECT * FROM machines ORDER BY machine_number");

        $maintenanceDueSoon = DB::select(
            "SELECT * FROM machines 
             WHERE maintenance_due_at IS NOT NULL 
               AND maintenance_due_at <= DATE_ADD(NOW(), INTERVAL 7 DAY)
               AND maintenance_due_at > NOW()
               AND status = 'available'"
        );

        return view('cashier.machines', compact('machines', 'maintenanceDueSoon'));
    }

    public function machineMaintenance(Request $request, $id)
    {
        $machine = DB::selectOne("SELECT * FROM machines WHERE id = ?", [$id]);
        abort_if(!$machine, 404);

        if ($machine->status === 'in_use') {
            return back()->with('error', 'Cannot set a machine that is currently in use to maintenance!');
        }

        $newStatus = $machine->status === 'under_maintenance' ? 'available' : 'under_maintenance';

        $lastMaintained = $newStatus === 'available' ? now() : $machine->last_maintained_at;
        $maintenanceDue = $newStatus === 'available' ? now()->addDays(30) : $machine->maintenance_due_at;

        DB::update(
            "UPDATE machines SET status = ?, maintenance_note = ?, last_maintained_at = ?, maintenance_due_at = ?, updated_at = NOW() WHERE id = ?",
            [$newStatus, $request->note ?? null, $lastMaintained, $maintenanceDue, $id]
        );

        $msg = $newStatus === 'under_maintenance'
            ? "Machine {$machine->machine_number} is now Under Maintenance!"
            : "Machine {$machine->machine_number} is now Available! Next maintenance due in 30 days.";

        return back()->with('success', $msg);
    }

    public function machineReportIssue(Request $request, $id)
    {
        $request->validate([
            'issue' => 'required|string|max:500',
        ]);

        $machine = DB::selectOne("SELECT * FROM machines WHERE id = ?", [$id]);
        abort_if(!$machine, 404);

        if ($machine->status === 'in_use') {
            return back()->with('error', 'Cannot report issue on a machine currently in use!');
        }

        DB::update(
            "UPDATE machines SET status = 'under_maintenance', maintenance_note = ?, updated_at = NOW() WHERE id = ?",
            ["⚠ Issue Reported: {$request->issue}", $id]
        );

        return back()->with('success', "Issue reported for Machine {$machine->machine_number}. It has been flagged for maintenance.");
    }

    public function paymentIndex()
    {
        $perPage = 5;
        $page    = request()->get('page', 1);
        $offset  = ($page - 1) * $perPage;

        $payments = DB::select(
            "SELECT p.*, o.order_id as order_ref, COALESCE(u.name, o.customer_name, 'Walk-in') as customer_name 
            FROM payments p
            LEFT JOIN orders o ON p.order_id = o.order_id
            LEFT JOIN users u ON o.customer_id = u.id
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        $total = DB::selectOne("SELECT COUNT(*) as total FROM payments")->total;

        $payments = new \Illuminate\Pagination\LengthAwarePaginator($payments, $total, $perPage, $page, [
            'path' => request()->url(),
        ]);

        return view('cashier.payments.index', compact('payments'));
    }

    // ✅ FIXED: Added COALESCE(u.name, o.customer_name, 'Walk-in') so customer name shows correctly
    public function paymentShow($id)
    {
        $payment = DB::selectOne(
            "SELECT p.*, o.order_id as order_ref, o.service, o.weight, o.amount as order_amount,
                    o.status as order_status, o.pickup_date,
                    COALESCE(u.name, o.customer_name, 'Walk-in') as customer_name,
                    u.email as customer_email
             FROM payments p
             LEFT JOIN orders o ON p.order_id = o.order_id
             LEFT JOIN users u ON o.customer_id = u.id
             WHERE p.payment_id = ?",
            [$id]
        );
        abort_if(!$payment, 404);
        return view('cashier.payments.show', compact('payment'));
    }

    public function paymentReceipt($id)
    {
        $payment = DB::selectOne(
            "SELECT p.*, o.service, o.weight, o.amount as order_amount,
                    o.status as order_status, o.pickup_date, u.name as customer_name
             FROM payments p
             LEFT JOIN orders o ON p.order_id = o.order_id
             LEFT JOIN users u ON o.customer_id = u.id
             WHERE p.payment_id = ?",
            [$id]
        );
        abort_if(!$payment, 404);
        return view('cashier.payments.receipt', compact('payment'));
    }

    public function markPaid(Request $request, $id)
    {
        $payment = DB::selectOne(
            "SELECT p.*, o.customer_id, o.order_id as order_ref
             FROM payments p
             LEFT JOIN orders o ON p.order_id = o.order_id
             WHERE p.payment_id = ?",
            [$id]
        );
        abort_if(!$payment, 404);

        DB::update(
            "UPDATE payments SET status = 'paid', method = ?, reference_number = ?, paid_at = NOW(), updated_at = NOW() WHERE payment_id = ?",
            [$request->method ?? 'Cash', $request->reference_number ?? null, $id]
        );

        if ($payment->customer_id) {
            $customer = DB::selectOne("SELECT * FROM users WHERE id = ?", [$payment->customer_id]);
            if ($customer) {
                $pointsEarned = (int) floor($payment->amount / 100);
                $newPoints    = ($customer->points ?? 0) + $pointsEarned;
                DB::update("UPDATE users SET points = ?, updated_at = NOW() WHERE id = ?", [$newPoints, $customer->id]);

                Notification::sendTo(
                    $customer->id,
                    "Payment for order {$payment->order_id} has been received. You earned {$pointsEarned} point(s)! Thank you! 🎉",
                    $payment->order_id
                );
            }
        }

        return redirect()->route('cashier.payments.index')->with('success', "Payment {$id} marked as paid!");
    }

    // ===== REPORTS =====
    private function getReportData(): array
    {
        $month = now()->month;
        $year  = now()->year;

        $salesSummary = DB::select(
            "SELECT DATE(p.created_at) as date,
                    COUNT(p.id) as total_orders,
                    SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(p.amount) as revenue
             FROM payments p
             JOIN orders o ON p.order_id = o.order_id
             WHERE p.status = 'paid'
             GROUP BY DATE(p.created_at)
             ORDER BY DATE(p.created_at) DESC"
        );

        $topServices = DB::select(
            "SELECT service, COUNT(*) as total, SUM(amount) as revenue
             FROM orders
             GROUP BY service
             ORDER BY total DESC"
        );

        $paymentMethods = DB::select(
            "SELECT method, COUNT(*) as count, SUM(amount) as total
             FROM payments
             WHERE status = 'paid'
             GROUP BY method
             ORDER BY count DESC"
        );

        $topCustomers = DB::select(
            "SELECT o.customer_id, COALESCE(u.name, o.customer_name, 'Walk-in') as customer_name,
                    COUNT(*) as total_orders, SUM(o.amount) as total_spent
            FROM orders o
            LEFT JOIN users u ON o.customer_id = u.id
            GROUP BY o.customer_id, u.name, o.customer_name
            ORDER BY total_spent DESC
            LIMIT 10"
        );

        $monthlyRevenue = DB::selectOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM payments
             WHERE MONTH(created_at) = ? AND YEAR(created_at) = ? AND status = 'paid'",
            [$month, $year]
        )->total;

        $totalOrders = DB::selectOne("SELECT COUNT(*) as total FROM orders")->total;
        $completed   = DB::selectOne("SELECT COUNT(*) as total FROM orders WHERE status = 'completed'")->total;

        return compact('monthlyRevenue', 'totalOrders', 'completed', 'salesSummary', 'topServices', 'paymentMethods', 'topCustomers');
    }

    public function reports()
    {
        return view('cashier.reports', $this->getReportData());
    }

    public function exportReportPdf()
    {
        $data     = $this->getReportData();
        $pdf      = Pdf::loadView('cashier.report-pdf', $data)->setPaper('a4', 'portrait');
        $filename = 'BubbleBee-Report-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    public function orderComplete($id)
    {
        $order = DB::selectOne("SELECT * FROM orders WHERE order_id = ?", [$id]);
        abort_if(!$order, 404);
        abort_if(!in_array($order->status, ['claimed', 'ready']), 403, 'Order cannot be completed yet.');

        DB::update(
            "UPDATE orders SET status = 'completed', updated_at = NOW() WHERE order_id = ?",
            [$id]
        );

        if ($order->customer_id) {
            Notification::sendTo(
                $order->customer_id,
                "Your order {$order->order_id} has been completed. Thank you!",
                $order->order_id
            );
        }

        return redirect()->route('cashier.orders.index')
                         ->with('success', "Order {$order->order_id} marked as completed!");
    }
}