<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function index()
    {
        $notifications = auth()->user()->unreadNotifications;
        return response()->json($notifications);
    }

    public function notifications(Request $request)
    {
        return response()->json([
            'notifications' => $request->user()->unreadNotifications,
            'count' => $request->user()->unreadNotifications->count(),
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $request->user()->notifications()->where('id', $id)->update(['read_at' => now()]);
        return response()->json(['message' => 'Notification marked as read']);
    }
}