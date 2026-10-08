<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $table = 'assessments';

    protected $fillable = [
        'teacher_id',
        'material_id',
        'title',
        'description',
        'target_bloom',
        'assessment_type',
        'status',
        'duration_minutes',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function questions()
    {
        return $this->belongsToMany(
            Question::class,
            'assessment_questions',
            'assessment_id',
            'question_id'
        )->withPivot('question_order', 'points');
    }

    public function studentAssessments()
    {
        return $this->hasMany(
            StudentAssessment::class,
            'assessment_id'
        );
    }
}