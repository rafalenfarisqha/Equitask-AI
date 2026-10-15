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
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Cek autentikasi hanya berdasarkan email dan password
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Kirim notifikasi login ke database
            NotificationHelper::send(
                $user->id,
                'Aktivitas Masuk Akun',
                'Halo <b>' . $user->name . '</b>, akun Anda baru saja masuk ke sistem EquiTask AI.',
                'security'
            );

            // Redirect otomatis berdasarkan role user di database
            if ($user->role === 'guru') {
                return redirect()->route('guru.dashboard');
            } else {
                return redirect()->route('siswa.dashboard');
            }
        }

        return back()
            ->withErrors(['login' => 'Email atau kata sandi yang kamu masukkan salah.'])
            ->withInput();
    }
    // Menampilkan form register
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Memproses reg
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:guru,siswa'],
            'institution' => ['required', 'string', 'max:255'], // Ubah jadi required jika wajib diisi, atau nullable jika opsional
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'institution' => $request->institution, // Langsung mengambil dari input form tanpa template bawaan
        ]);

        // Otomatis login setelah mendaftar
        Auth::login($user);

        // Kirim notifikasi selamat datang ke database
        NotificationHelper::send(
            $user->id,
            'Akun Berhasil Dibuat',
            'Halo <b>' . $user->name . '</b>, selamat datang di EquiTask AI! Akun Anda berhasil didaftarkan.',
            'security'
        );

        if ($user->role === 'guru') {
            return redirect()->route('guru.dashboard');
        } else {
            return redirect()->route('siswa.dashboard');
        }
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
