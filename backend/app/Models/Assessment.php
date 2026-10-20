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
    ];

    protected $casts = [
        'jumlah_pilihan_ganda' => 'integer',
        'jumlah_esai' => 'integer',
        'c1' => 'boolean',
        'c2' => 'boolean',
        'c3' => 'boolean',
        'c4' => 'boolean',
        'c5' => 'boolean',
        'c6' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
