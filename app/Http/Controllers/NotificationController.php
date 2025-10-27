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

    // Tandai satu notifikasi sebagai dibaca
    public function markAsRead($id)
    {
        $notification = Notification::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($notification) {
            $notification->update(['is_read' => true]);
        }

        return response()->json(['success' => true]);
    }
}
