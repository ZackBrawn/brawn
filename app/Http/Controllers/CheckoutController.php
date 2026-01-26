<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

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

        $defaultAddress = auth()->check() ? auth()->user()->addresses()->where('is_primary', TRUE)->first() : null;

        return Inertia::render('User/Checkout/Index', [
            'cart' => array_values($cart),
            'paymentMethods' => $paymentMethods,
            'defaultAddress' => $defaultAddress,
        ]);
    }

    /**
     * Memproses data checkout dan membuat order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method_id' => 'required|exists:payment_methods,id',
            'note' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja Anda kosong.');
        }

        $defaultAddress = $user->addresses()->where('is_primary', true)->first();

        if (!$defaultAddress) {
            return redirect()->route('profile.edit')->with('error', 'Silakan atur alamat pengiriman utama terlebih dahulu.');
        }

        // Hitung total harga
        $total = collect($cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        try {
            DB::beginTransaction();

            // Buat order baru
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $defaultAddress->id,
                'payment_method_id' => $request->payment_method_id,
                'grand_total' => $total,
                'status' => 'Menunggu Pembayaran',
                // 'note' => $request->note, // Jika ada kolom note di tabel orders
            ]);
            
            // Simpan detail order
            foreach ($cart as $item) {
                $product = \App\Models\Product::lockForUpdate()->find($item['id']);
                
                if (!$product) {
                    throw new \Exception("Produk dengan ID {$item['id']} tidak ditemukan.");
                }
                
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Sisa stok: {$product->stock}");
                }

                $product->decrement('stock', $item['quantity']);

                $order->orderItems()->create([
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
            
            // Buat history awal
            $order->orderStatusHistories()->create([
                'status' => 'Menunggu Pembayaran',
                'notes' => 'Pesanan baru dibuat via web checkout.'
            ]);

            // Kirim notifikasi ke user
            $user->notify(new \App\Notifications\OrderCreated($order));

            DB::commit();

            // Hapus keranjang setelah checkout berhasil
            session()->forget('cart');

            return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi.');
        }
    }
}