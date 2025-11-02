<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Menampilkan semua notifikasi user
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(10); // ✅ PAGINATION

        return view('notifications.index', compact('notifications'));
    }

     // ✅ Tandai notifikasi sebagai dibaca via AJAX
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($notification && !$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'unreadCount' => $unreadCount
        ]);
    }
}
