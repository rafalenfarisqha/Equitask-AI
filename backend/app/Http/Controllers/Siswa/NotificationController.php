<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $siswa = auth()->user();

        // Mengambil notifikasi khusus untuk siswa yang sedang login
        $notifications = Notification::where('user_id', $siswa->id)
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('siswa.notifikasi', compact('notifications'));
    }
}
