<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    // Mark single notification as read
    public function markRead($id)
    {
        $notification = DB::selectOne(
            "SELECT * FROM notifications WHERE id = ? AND user_id = ?",
            [$id, auth()->id()]
        );
        abort_if(!$notification, 404);

        DB::update(
            "UPDATE notifications SET is_read = 1, updated_at = NOW() WHERE id = ? AND user_id = ?",
            [$id, auth()->id()]
        );

        return response()->json(['success' => true]);
    }

    // Mark ALL notifications as read
    public function markAllRead()
    {
        DB::update(
            "UPDATE notifications SET is_read = 1, updated_at = NOW() WHERE user_id = ? AND is_read = 0",
            [auth()->id()]
        );

        return response()->json(['success' => true]);
    }

    // Get unread count (for AJAX polling)
    public function unreadCount()
    {
        $count = DB::selectOne(
            "SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND is_read = 0",
            [auth()->id()]
        )->total;

        return response()->json(['count' => $count]);
    }

    // Get all notifications (for dropdown)
    public function index()
    {
        $notifications = DB::select(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 10",
            [auth()->id()]
        );

        return response()->json($notifications);
    }
}