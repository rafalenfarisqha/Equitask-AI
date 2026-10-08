<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    protected $table = 'student_profiles';

    protected $fillable = [
        'user_id',
        'disability_type',
        'support_needs',
        'learning_notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}