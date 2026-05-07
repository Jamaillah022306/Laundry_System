<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;

class CustomerController extends Controller
{
    public function dashboard()
    {
        $userId = auth()->id();

        $totalOrders = DB::selectOne(
            "SELECT COUNT(*) as total FROM orders WHERE customer_id = ?",
            [$userId]
        )->total;

        $totalSpent = DB::selectOne(
            "SELECT COALESCE(SUM(amount), 0) as total FROM orders WHERE customer_id = ?",
            [$userId]
        )->total;

        $pointsEarned = DB::selectOne(
            "SELECT COALESCE(points, 0) as points FROM users WHERE id = ?",
            [$userId]
        )->points;

        $activeOrders = DB::select(
            "SELECT * FROM orders
             WHERE customer_id = ?
             AND status NOT IN ('completed', 'cancelled', 'archived')
             ORDER BY created_at DESC",
            [$userId]
        );

        return view('customer.dashboard', compact('totalOrders', 'totalSpent', 'pointsEarned', 'activeOrders'));
    }

    public function track(Request $request)
    {
        $trackedOrders = collect();
        if ($request->filled('order_id')) {
            $results = DB::select(
                "SELECT * FROM orders WHERE order_id = ?",
                [$request->order_id]
            );
            $trackedOrders = collect($results);
        }
        return view('customer.track', compact('trackedOrders'));
    }

    // ===== AJAX: Track order search for modal =====
    public function trackSearch(Request $request)
    {
        $orderId = trim($request->get('order_id', ''));

        if (!$orderId) {
            return response()->json([]);
        }

        $orders = DB::select(
            "SELECT order_id, service, weight, pickup_date, status
             FROM orders
             WHERE order_id LIKE ? AND customer_id = ?
             LIMIT 5",
            ['%' . $orderId . '%', auth()->id()]
        );

        $orders = array_map(function ($order) {
            $order->pickup_date = \Carbon\Carbon::parse($order->pickup_date)->format('M d, Y');
            return $order;
        }, $orders);

        return response()->json($orders);
    }

    public function history()
    {
        $userId  = auth()->id();
        $perPage = 5;
        $page    = request()->get('page', 1);
        $offset  = ($page - 1) * $perPage;

        $orders = DB::select(
            "SELECT o.*, p.payment_id, p.status as payment_status, p.method, p.amount as payment_amount, p.paid_at
             FROM orders o
             LEFT JOIN payments p ON o.order_id = p.order_id
             WHERE o.customer_id = ?
             ORDER BY o.created_at DESC
             LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );

        $total = DB::selectOne(
            "SELECT COUNT(*) as total FROM orders WHERE customer_id = ?",
            [$userId]
        )->total;

        $orders = new \Illuminate\Pagination\LengthAwarePaginator($orders, $total, $perPage, $page, [
            'path' => request()->url(),
        ]);

        return view('customer.history', compact('orders'));
    }

    public function receipt($paymentId)
    {
        $payment = DB::selectOne(
            "SELECT p.*, o.customer_id, o.service, o.weight, o.pickup_date,
                    o.status as order_status, u.name as customer_name
             FROM payments p
             LEFT JOIN orders o ON p.order_id = o.order_id
             LEFT JOIN users u ON o.customer_id = u.id
             WHERE p.payment_id = ?",
            [$paymentId]
        );

        abort_if(!$payment, 404);
        abort_if($payment->customer_id !== auth()->id(), 403, 'Unauthorized');

        return view('customer.receipt', compact('payment'));
    }

    public function claimOrder($id)
    {
        $order = DB::selectOne(
            "SELECT * FROM orders WHERE order_id = ? AND customer_id = ?",
            [$id, auth()->id()]
        );

        abort_if(!$order, 404);
        abort_if($order->status !== 'ready', 403, 'Order is not ready for claiming.');

        DB::update(
            "UPDATE orders SET status = 'claimed', claimed_at = NOW(), updated_at = NOW() WHERE order_id = ?",
            [$id]
        );

        $cashier = DB::selectOne("SELECT id FROM users WHERE role = 'cashier' LIMIT 1");
        if ($cashier) {
            Notification::sendTo(
                $cashier->id,
                "Customer has claimed order {$order->order_id}. Please mark as completed.",
                $order->order_id
            );
        }

        return redirect()->back()->with('success', "Order {$order->order_id} claimed successfully!");
    }
}