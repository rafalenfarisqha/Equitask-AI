<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $table = 'assessments';

    protected $fillable = [
        'user_id',
        'nama',
        'mapel',
        'status',
        'visibility',
        'icon',
        'color',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
