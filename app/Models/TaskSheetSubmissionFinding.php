<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskSheetSubmissionFinding extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'item_id',
        'finding',
        'is_within_range',
    ];

    protected $casts = [
        'is_within_range' => 'boolean',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(TaskSheetSubmission::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(TaskSheetItem::class);
    }
}
