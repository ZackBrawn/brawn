<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PaymentMethodsController extends Controller
{
public function index()
{
    // Mengambil semua data dari tabel payment_methods
    $paymentMethods = PaymentMethod::latest()->get(); 

    return Inertia::render('Admin/PaymentMethods/Index', [
        'paymentMethods' => $paymentMethods,
    ]);
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/PaymentMethods/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'is_active' => 'boolean',
        ]);
        
        PaymentMethod::create($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentMethod $paymentMethod)
    {
        return Inertia::render('Admin/PaymentMethods/Edit', [
            'paymentMethod' => $paymentMethod,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => [
                'required',
                'string',
                'max:50',
                // Pastikan nomor rekening unik, kecuali untuk metode yang sedang diedit
                Rule::unique('payment_methods')->ignore($paymentMethod->id),
            ],
            'is_active' => 'boolean',
        ]);
        
        $paymentMethod->update($validated);

        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy(PaymentMethod $paymentMethod)
{
    try {
        $paymentMethod->delete();
        // Redirect back with a success flash message
        return redirect()->route('admin.payment-methods.index')
            ->with('success', 'Metode pembayaran berhasil dihapus!');
    } catch (\Exception $e) {
        // Redirect back with an error flash message
        return redirect()->back()
            ->withErrors(['error' => 'Gagal menghapus metode pembayaran. Mungkin sedang digunakan.']);
    }
}
}