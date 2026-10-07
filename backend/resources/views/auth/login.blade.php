@extends('layouts.app')

@section('title', 'Masuk - EquiTask AI')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-2xl shadow-sm border border-slate-200 mt-12">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-slate-900">Masuk ke EquiTask AI</h1>
        <p class="text-sm text-slate-500 mt-2">Platform Evaluasi Diferensiasi Inklusif</p>
    </div>

    <form action="#" method="POST" class="space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Kata Sandi</label>
            <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
        </div>

        <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition">
            Masuk
        </button>
    </form>
</div>
@endsection
