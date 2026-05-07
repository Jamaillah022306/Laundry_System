<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\User;

class CustomerManagementController extends Controller
{
    public function index()
    {
        $customers = DB::select(
            "SELECT u.id, u.name, u.username, u.email, u.created_at, COUNT(o.id) as orders_count
            FROM users u
            LEFT JOIN orders o ON u.id = o.customer_id
            WHERE u.role = 'customer'
            GROUP BY u.id, u.name, u.username, u.email, u.created_at
            ORDER BY u.created_at DESC"
        );

        return view('cashier.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('cashier.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|max:50',
            'password' => 'required|string|min:6',
        ]);

        DB::insert(
            "INSERT INTO users (name, email, username, password, role, points, created_at, updated_at)
             VALUES (?, ?, ?, ?, 'customer', 0, NOW(), NOW())",
            [
                $validated['name'],
                $validated['email'],
                $validated['username'],
                Hash::make($validated['password']),
            ]
        );

        // Send verification email
        $customer = User::where('email', $validated['email'])->first();
        event(new Registered($customer));

        return redirect()->route('cashier.customers.index')
                         ->with('success', "Customer '{$validated['name']}' registered! A verification email has been sent to {$validated['email']}.");
    }

    public function show($id)
    {
        $customer = DB::selectOne(
            "SELECT id, name, email, phone_number, created_at FROM users WHERE role = 'customer' AND id = ?",
            [$id]
        );
        abort_if(!$customer, 404);

        $orders = DB::select(
            "SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC",
            [$id]
        );

        return view('cashier.customers.show', compact('customer', 'orders'));
    }
}