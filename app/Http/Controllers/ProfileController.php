<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function index()
    {
        return Inertia::render('User/Profile/Index', [
            'mustVerifyEmail' => config('auth.must_verify_email'),
            'status' => session('status'),
            'auth' => [
                'user' => [
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone_number' => auth()->user()->phone_number,
                    'email_verified_at' => auth()->user()->email_verified_at,
                    'created_at' => auth()->user()->created_at,
                ],
            ],
            'addresses' => auth()->user()->addresses,
        ]);
    }

    /**
     * Menampilkan form edit profil.
     */
    public function edit()
    {
        return Inertia::render('User/Profile/Edit', [
            'mustVerifyEmail' => config('auth.must_verify_email'),
            'status' => session('status'),
            'auth' => [
                'user' => [
                    'name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                    'phone_number' => auth()->user()->phone_number,
                    'email_verified_at' => auth()->user()->email_verified_at,
                ],
            ],
            'addresses' => auth()->user()->addresses,
        ]);
    }

    /**
     * Memperbarui informasi profil pengguna.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('status', 'Profil berhasil diperbarui');
    }

    /**
     * Menghapus akun pengguna.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();
        
        Auth::logout();
        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Akun Anda berhasil dihapus');
    }

    /**
     * Memperbarui kata sandi pengguna.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current-password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Kata sandi saat ini tidak cocok.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}