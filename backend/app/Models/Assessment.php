<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $table = 'assessments';

    protected $fillable = [
        'user_id',
        'nama',
        'mapel',
        'kelas',
        'tujuan_pembelajaran',
        'materi_ajar',
        'versi_asesmen',
        'jumlah_pilihan_ganda',
        'jumlah_esai',
        'c1',
        'c2',
        'c3',
        'c4',
        'c5',
        'c6',
        'status',
        'visibility',
        'icon',
        'color',
        'description', // Tempat menyimpan hasil output dari Dify AI
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
