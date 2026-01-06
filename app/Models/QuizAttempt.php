<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_questions',
        'correct_answers',
        'time_taken',
        'is_completed',
        'started_at',
        'completed_at'
    ];

    protected $casts = [
        'score' => 'integer',
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'time_taken' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }

    public function getPercentageAttribute(): float
    {
        return $this->total_questions > 0 ? round(($this->correct_answers / $this->total_questions) * 100, 1) : 0;
    }

    public function getIsPassedAttribute(): bool
    {
        return $this->percentage >= $this->quiz->passing_score;
    }
}
