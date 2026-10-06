<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScreeningSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_name',
        'parent_phone',
        'child_name',
        'child_age_months',
        'age_group',
        'form_type',
        'answers',
        'score',
        'total_questions',
        'red_flags_count',
        'risk_level',
        'need_doctor',
        'clinical_impression',
        'detected_red_flags',
        'doctor_recommendation',
        'advice_summary',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'answers' => 'array',
        'detected_red_flags' => 'array',
        'need_doctor' => 'boolean',
        'child_age_months' => 'integer',
        'score' => 'integer',
        'total_questions' => 'integer',
        'red_flags_count' => 'integer',
    ];

    public function getRiskBadgeAttribute(): array
    {
        return match($this->risk_level) {
            'high' => ['label' => 'Nguy cơ cao - Cần khám sớm', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
            'medium' => ['label' => 'Cần theo dõi & Tương tác nhiều hơn', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
            default => ['label' => 'Phát triển bình thường', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
        };
    }
}
