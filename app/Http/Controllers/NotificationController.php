<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Menampilkan daftar notifikasi pengguna.
     */
    public function index()
    {
        $user = auth()->user();

        // Ambil semua notifikasi untuk pengguna yang sedang login
        $notifications = $user->notifications()->paginate(10);

        return Inertia::render('User/Notification/Index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * Menandai notifikasi sebagai sudah dibaca.
     */
    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        $notification->markAsRead();

        return redirect()->back();
    }

    /**
     * Menandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }
}