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
        $orders = auth()->user()->orders()->with(['orderItems.product'])->latest()->get();

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
        $order->load(['orderItems.product', 'paymentMethod', 'address']);

        return Inertia::render('User/Orders/Show', [
            'order' => $order,
        ]);
    }
    /**
     * Mengunggah bukti pembayaran.
     */
    public function uploadProof(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('payment_proof')) {
            // Delete old proof if exists
            if ($order->bukti_pembayaran) {
                // Assuming you might want to delete old file, but careful with path logic
                // Storage::disk('public')->delete($order->bukti_pembayaran);
            }

            $path = $request->file('payment_proof')->store('payment-proofs', 'public');
            
            $order->update([
                'bukti_pembayaran' => $path,
                // Optional: Update status to indicate verification needed
                 'status' => 'Menunggu Pembayaran', // Or keep as is, or change to 'Menunggu Konfirmasi' if that status exists. Sticking to current flow.
            ]);

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah. Kami akan segera memverifikasinya.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah bukti pembayaran.');
    }
}