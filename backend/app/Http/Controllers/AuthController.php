<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses login Guru dan Siswa.
     */
    public function login(Request $request)
    {
        // Validasi role dan password
        $request->validate([
            'role' => ['required', 'in:guru,siswa'],
            'password' => ['required'],
        ]);

        /*
         * LOGIN GURU
         * Guru login menggunakan email dan password.
         */
        if ($request->role === 'guru') {

            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
                'role' => 'guru',
            ];

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->route('guru.dashboard');
            }
        }

        /*
         * LOGIN SISWA
         * Siswa login menggunakan NIS dan password.
         */
        if ($request->role === 'siswa') {

            $request->validate([
                'nis' => ['required'],
            ]);

            $credentials = [
                'nis' => $request->nis,
                'password' => $request->password,
                'role' => 'siswa',
            ];

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return redirect()->route('siswa.dashboard');
            }
        }

        // Jika login gagal
        return back()
            ->withErrors([
                'login' => 'Email/NIS atau kata sandi yang kamu masukkan salah.',
            ])
            ->withInput();
    }

    /**
     * Memproses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}