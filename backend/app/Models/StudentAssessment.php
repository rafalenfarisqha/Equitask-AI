<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAssessment extends Model
{
    protected $table = 'student_assessments';

    protected $fillable = [
        'assessment_id',
        'student_id',
        'status',
        'started_at',
        'submitted_at',
        'score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'decimal:2',
    ];

    public function assessment()
    {
        return $this->belongsTo(
            Assessment::class,
            'assessment_id'
        );
    }

    public function student()
    {
        return $this->belongsTo(
            User::class,
            'student_id'
        );
    }

    public function answers()
    {
        return $this->hasMany(
            StudentAnswer::class,
            'student_assessment_id'
        );
    }
}