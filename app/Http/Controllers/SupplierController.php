<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Inertia\Inertia;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource for public.
     * * @return \Inertia\Response
     */
    public function index()
    {
        // Mengambil semua supplier dengan jumlah produk terkait
        $suppliers = Supplier::query()
            ->withCount('products')
            ->latest()
            ->get();

        return Inertia::render('Public/Suppliers/Index', [
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Display the specified resource for public.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Inertia\Response
     */
    public function show(Supplier $supplier)
    {
        // Memuat produk yang terkait dengan supplier, termasuk kategorinya
        $supplier->load(['products' => function ($query) {
            $query->with(['category:id,name'])->select('id', 'name', 'price', 'stock', 'image_url', 'category_id', 'supplier_id');
        }]);
        
        return Inertia::render('Public/Suppliers/Show', [
            'supplier' => $supplier,
        ]);
    }
}
