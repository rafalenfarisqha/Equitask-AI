<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Mengambil data notifikasi dari database khusus untuk user yang sedang login
        $notifications = Notification::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->get();

        return view('guru.notifikasi', compact('notifications'));
    }
}
