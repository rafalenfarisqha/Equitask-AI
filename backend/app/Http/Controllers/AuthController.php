<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\NotificationHelper;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Memproses Login (Guru & Siswa sama-sama pakai Email)
    public function login(Request $request)
{
    $validated = $request->validate([
        'role' => ['required', 'in:guru,siswa'],
        'email' => ['nullable', 'required_if:role,guru', 'email'],
        'nisn' => ['nullable', 'required_if:role,siswa', 'string'],
        'password' => ['required', 'string'],
    ]);

    if ($validated['role'] === 'guru') {
        $user = User::where('email', $validated['email'])
            ->where('role', 'guru')
            ->first();
    } else {
        $user = User::where('nisn', $validated['nisn'])
            ->where('role', 'siswa')
            ->first();
    }

    if ($user && Hash::check($validated['password'], $user->password)) {
        Auth::login($user);
        $request->session()->regenerate();

        NotificationHelper::send(
            $user->id,
            'Aktivitas Masuk Akun',
            'Halo <b>' . e($user->name) .
                '</b>, akun Anda baru saja masuk ke sistem EquiTask AI.',
            'security'
        );

        return $user->role === 'guru'
            ? redirect()->route('guru.dashboard')
            : redirect()->route('siswa.dashboard');
    }

    return back()
        ->withErrors([
            'login' => 'Identitas atau kata sandi yang kamu masukkan salah.',
        ])
        ->withInput($request->only('email', 'nisn', 'role'));
}

    // Menampilkan form register
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Memproses reg
    public function register(Request $request)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'role' => ['required', 'in:guru,siswa'],

        'email' => [
            'nullable',
            'required_if:role,guru',
            'email',
            'max:255',
            'unique:users,email',
        ],

        'nisn' => [
            'nullable',
            'required_if:role,siswa',
            'string',
            'max:255',
            'unique:users,nisn',
        ],

        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'institution' => ['required', 'string', 'max:150'],
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'] ?? null,
        'nisn' => $validated['nisn'] ?? null,
        'password' => $validated['password'],
        'role' => $validated['role'],
        'institution' => $validated['institution'],
    ]);

    Auth::login($user);
    $request->session()->regenerate();

    NotificationHelper::send(
        $user->id,
        'Akun Berhasil Dibuat',
        'Halo <b>' . e($user->name) .
            '</b>, selamat datang di EquiTask AI!',
        'security'
    );

    if ($user->role === 'guru') {
        return redirect()->route('guru.dashboard');
    }

    return redirect()->route('siswa.dashboard');
}

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
