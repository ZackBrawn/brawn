<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;

class UserOrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan yang dimiliki oleh pengguna yang sedang login.
     */
    public function index()
    {
        $orders = auth()->user()->orders()->with(['items.product'])->latest()->get();

        return Inertia::render('User/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Menampilkan detail pesanan.
     */
    public function show(Order $order)
    {
        // Pastikan pengguna memiliki akses ke order ini
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        // Load relasi yang diperlukan
        $order->load(['items.product']);

        return Inertia::render('User/Orders/Show', [
            'order' => $order,
        ]);
    }
}