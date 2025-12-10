<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource for public.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Mengambil semua kategori dengan jumlah produk terkait
        $categories = Category::query()
            ->withCount('products')
            ->latest()
            ->get();

        // Debug log
        \Log::info('Rendering Categories/Index', [
            'auth_user' => auth()->user() ? auth()->id() : 'guest',
            'categories_count' => $categories->count()
        ]);

        return Inertia::render('User/Categories/Index', [
            'categories' => $categories,
            // Ensure auth data is included
            'auth' => [
                'user' => auth()->user() ? [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ] : null
            ]
        ]);
    }

    /**
     * Display the specified resource for public.
     *
     * @param  \App\Models\Category  $category
     * @return \Inertia\Response
     */
    public function show(Category $category)
    {
        // Memuat produk yang terkait dengan kategori, termasuk supplier dan kategori
        $category->load(['products' => function ($query) {
            $query->with([
                'supplier:id,name',
                'category:id,name'  // Load category relationship
            ])->select('id', 'name', 'slug', 'price', 'stock', 'image_url', 'category_id', 'supplier_id');
        }]);

        // Debug log
        \Log::info('Rendering Category Show', [
            'category_id' => $category->id,
            'auth_user' => auth()->user() ? auth()->id() : 'guest',
            'products_count' => $category->products ? $category->products->count() : 0
        ]);

        return Inertia::render('User/Categories/Show', [
            'category' => $category,
            // Ensure auth data is included
            'auth' => [
                'user' => auth()->user() ? [
                    'id' => auth()->id(),
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ] : null
            ]
        ]);
    }
}
