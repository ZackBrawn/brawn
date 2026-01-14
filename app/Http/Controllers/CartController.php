<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang belanja pengguna.
     */
    public function index()
    {
        // Ambil data keranjang dari session
        $cart = session()->get('cart', []);

        // Ambil data produk terbaru dari database untuk mendapatkan stok terkini
        $productIds = array_keys($cart);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        // Perbarui data cart dengan stok terbaru
        foreach ($cart as $id => &$item) {
            if (isset($products[$id])) {
                $item['stock'] = $products[$id]->stock;
                // Opsional: perbarui harga dan nama jika berubah
                $item['price'] = $products[$id]->price;
                $item['name'] = $products[$id]->name;
                $item['image'] = $products[$id]->image_url;
            } else {
                // Jika produk tidak ditemukan di DB (mungkin dihapus), set stok 0 atau hapus dari cart
                $item['stock'] = 0;
            }
        }

        return Inertia::render('User/Cart/Index', [
            'cart' => array_values($cart),
        ]);
    }

    /**
     * Menambahkan produk ke keranjang belanja.
     */
    public function add(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        
        $id = $product->id;
        $quantity = $request->input('quantity', 1);

        // Jika produk sudah ada di keranjang, tambahkan kuantitasnya
        if(isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            // Jika produk belum ada, tambahkan ke keranjang
            $cart[$id] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "image" => $product->image_url, // Gunakan accessor image_url
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    /**
     * Memperbarui kuantitas produk di keranjang.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        $cart = session()->get('cart', []);
        $id = $product->id;
        
        if(isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Kuantitas produk berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Menghapus produk dari keranjang.
     */
    public function remove(Product $product)
    {
        $cart = session()->get('cart', []);
        $id = $product->id;
        
        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang.');
        }
        
        return redirect()->back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }
}