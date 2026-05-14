<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar semua produk.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'supplier']);

        $query->when($request->search, function ($q, $search) {
            $q->where('name', 'like', '%' . $search . '%');
        });

        $query->when($request->category_id, function ($q, $categoryId) {
            $q->where('category_id', $categoryId);
        });

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::all();

        return Inertia::render('User/Product/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only('search', 'category_id'),
        ]);
    }

    // Di ProductController atau controller yang menangani halaman product detail
public function show(Product $product)  // atau berdasarkan slug
{
    $user = auth()->user();
    $wishlist = [];
    
    if ($user) {
        $wishlist = $user->wishlist()
            ->with('product')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            });
    }

    return Inertia::render('Products/Show', [ // atau path yang sesuai
        'product' => $product->load('category', 'supplier'), // sesuaikan relasi yang dibutuhkan
        'auth' => [
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ] : null
        ],
        'wishlist' => $wishlist
    ]);
}
}