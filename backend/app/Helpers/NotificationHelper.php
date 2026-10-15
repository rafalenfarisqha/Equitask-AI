<?php

namespace App\Helpers;

use App\Models\Notification;

class NotificationHelper
{
    public static function send($userId, $title, $message, $type)
    {
        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'is_read' => false,
        ]);
    }
}
