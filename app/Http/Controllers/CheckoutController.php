<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\UserOrder; // Asumsi model user order sudah ada
use App\Models\PaymentMethod;

class CheckoutController extends Controller
{
    /**
     * Menampilkan halaman checkout.
     */
    public function index()
    {
        $cart = session()->get('cart', []);

        // Cek apakah keranjang kosong
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return Inertia::render('User/Checkout/Index', [
            'cart' => array_values($cart),
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Memproses data checkout dan membuat order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        // Hitung total harga
        $total = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // Buat order baru
        $order = UserOrder::create([
            'user_id' => auth()->id(), // Asumsi user sudah login
            'total_amount' => $total,
            'status' => 'pending',
            'payment_method_id' => $request->payment_method_id,
            'shipping_address' => $request->address,
            'customer_name' => $request->name,
            'customer_email' => $request->email,
        ]);
        
        // Simpan detail order
        foreach ($cart as $item) {
            $order->items()->create([
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // Hapus keranjang setelah checkout berhasil
        session()->forget('cart');

        return redirect()->route('user.orders.show', $order)->with('success', 'Order Anda berhasil dibuat. Silakan selesaikan pembayaran.');
    }
}