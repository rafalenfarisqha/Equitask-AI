<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Menampilkan halaman profil (mode lihat)
    public function show()
    {
        $guru = auth()->user();
        return view('guru.profil', compact('guru'));
    }

    // Menampilkan halaman form edit profil
    public function edit()
    {
        $guru = auth()->user();
        return view('guru.edit-profil', compact('guru'));
    }

    // Memproses update data profil
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'institution' => 'nullable|string|max:150',
            'password' => 'nullable|min:6|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->name = trim(($request->first_name ?? '') . ' ' . ($request->last_name ?? '')) ?: $user->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->institution = $request->institution;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('guru.profil')->with('success', 'Profil berhasil diperbarui!');
    }
}
