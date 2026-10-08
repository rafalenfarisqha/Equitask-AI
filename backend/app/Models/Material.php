<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $table = 'materials';

    protected $fillable = [
        'teacher_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'material_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'material_id');
    }
}