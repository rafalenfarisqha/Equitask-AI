<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $table = 'student_answers';

    public $timestamps = false;

    protected $fillable = [
        'student_assessment_id',
        'question_id',
        'answer',
        'is_correct',
        'points_obtained',
        'answered_at',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_obtained' => 'decimal:2',
        'answered_at' => 'datetime',
    ];

    public function studentAssessment()
    {
        return $this->belongsTo(
            StudentAssessment::class,
            'student_assessment_id'
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