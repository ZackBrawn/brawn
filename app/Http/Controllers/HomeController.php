<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman beranda.
     */
    public function index()
    {
        // Ambil 10 produk terbaru untuk ditampilkan di halaman utama
        $products = Product::with(['category', 'supplier'])->latest()->take(10)->get();
        $user = auth()->user();

        return Inertia::render('User/Dashboard/Index', [
            'products' => $products,
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ] : null
            ],
            'wishlist' => $user ? $user->wishlist()->pluck('product_id') : []
        ]);
    }

    /**
     * Menampilkan detail produk.
     */
    public function showProduct(Product $product)
    {
        $user = auth()->user();
        
        return Inertia::render('User/Product/Show', [
            'product' => $product->load(['category', 'supplier']),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ] : null
            ],
            'wishlist' => $user ? $user->wishlist()->pluck('product_id') : []
        ]);
    }
}