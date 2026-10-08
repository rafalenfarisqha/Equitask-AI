<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentQuestion extends Model
{
    protected $table = 'assessment_questions';

    public $timestamps = false;

    protected $fillable = [
        'assessment_id',
        'question_id',
        'question_order',
        'points',
    ];

    public function assessment()
    {
        return $this->belongsTo(
            Assessment::class,
            'assessment_id'
        );
    }

    public function question()
    {
        return $this->belongsTo(
            Question::class,
            'question_id'
        );
    }
}