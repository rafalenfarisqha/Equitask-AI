@extends('layouts.app')

@section('content')
<div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 20px;">
    <div class="card" style="width: 100%; max-width: 420px; padding: 32px; box-shadow: var(--shadow-lg);">

        <!-- Logo / Header -->
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--primary), #3b82f6); color: #fff; border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; box-shadow: 0 10px 20px -10px rgba(37, 99, 235, 0.5);">
                <span class="material-symbols-outlined" style="font-size: 32px;">school</span>
            </div>
            <h1 style="font-size: 22px; font-weight: 800; color: var(--text);">Masuk ke EquiTask AI</h1>
            <p class="muted" style="margin-top: 4px; font-size: 13px;">Platform Evaluasi Diferensiasi Inklusif</p>
        </div>

        <!-- Form Login -->
        <form action="{{ url('/login') }}" method="POST">
            @csrf

            <div style="margin-bottom: 16px;">
                <label class="muted" style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13px;">Email</label>
                <input type="email" name="email" class="input" value="anita.wijaya@sekolah.sch.id" required>
            </div>

            <div style="margin-bottom: 16px;">
                <label class="muted" style="display: block; font-weight: 700; margin-bottom: 6px; font-size: 13px;">Kata Sandi</label>
                <input type="password" name="password" class="input" value="password123" required>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px;">
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: var(--muted); font-weight: 600;">
                    <input type="checkbox" style="accent-color: var(--primary);"> Ingat Saya
                </label>
                <a href="#" style="color: var(--primary); text-decoration: none; font-weight: 700;">Lupa Sandi?</a>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                <span class="material-symbols-outlined" style="font-size: 18px;">login</span> Masuk
            </button>
        </form>

    </div>
</div>
@endsection
