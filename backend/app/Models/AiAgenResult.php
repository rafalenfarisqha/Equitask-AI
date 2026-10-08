<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AIAgentResult extends Model
{
    protected $table = 'ai_agent_results';

    public $timestamps = false;

    protected $fillable = [
        'generation_id',
        'agent_name',
        'agent_order',
        'input_data',
        'output_data',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'input_data' => 'array',
        'output_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function generation()
    {
        return $this->belongsTo(
            AIGeneration::class,
            'generation_id'
        );
    }
}