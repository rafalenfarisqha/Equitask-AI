<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIGeneration extends Model
{
    protected $table = 'ai_generations';

    public $timestamps = false;

    protected $fillable = [
        'teacher_id',
        'material_id',
        'assessment_id',
        'target_bloom',
        'status',
        'total_agents',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function agentResults()
    {
        return $this->hasMany(
            AIAgentResult::class,
            'generation_id'
        );
    }
}