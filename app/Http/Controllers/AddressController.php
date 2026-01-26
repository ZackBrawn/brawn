<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            \Log::info('AddressController::store called');
            \Log::info($request->all());

            $validated = $request->validate([
                'label' => 'required|string|max:100',
                'recipient_name' => 'required|string|max:100',
                'full_address' => 'required|string',
                'city' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'postal_code' => 'required|string|max:10',
                'is_primary' => 'boolean',
            ]);

            $user = $request->user();

            // Handle primary address logic
            // If marked as primary OR user has no addresses yet, make this one primary
            if ($request->boolean('is_primary') || $user->addresses()->count() === 0) {
                // Set all other addresses to non-primary
                $user->addresses()->update(['is_primary' => false]);
                $validated['is_primary'] = true;
            } else {
                $validated['is_primary'] = false;
            }

            $address = $user->addresses()->create($validated);
            
            \Log::info('Address created: ' . $address->id);

            return redirect()->back()->with('success', 'Alamat berhasil ditambahkan.');
        } catch (\Exception $e) {
            \Log::error('Error creating address: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan alamat: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $validated = $request->validate([
            'label' => 'required|string|max:100',
            'recipient_name' => 'required|string|max:100',
            'full_address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'is_primary' => 'boolean',
        ]);

        if ($request->is_primary) {
             $request->user()->addresses()->where('id', '!=', $id)->update(['is_primary' => false]);
        }

        $address->update($validated);

        return redirect()->back()->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->delete();

        return redirect()->back()->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Set the specified address as default.
     */
    public function setDefault(Request $request, $id)
    {
        $request->user()->addresses()->update(['is_primary' => false]);
        $request->user()->addresses()->findOrFail($id)->update(['is_primary' => true]);

        return redirect()->back()->with('success', 'Alamat utama berhasil diubah.');
    }
}
