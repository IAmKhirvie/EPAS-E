<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'information_sheet_id',
        'title',
        'topic_number',
        'content',
        'parts',
        'order'
    ];

    protected $casts = [
        'parts' => 'array',
    ];

    public function informationSheet()
    {
        return $this->belongsTo(InformationSheet::class);
    }

    public function getNextTopic()
    {
        return self::where('information_sheet_id', $this->information_sheet_id)
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    public function getPreviousTopic()
    {
        return self::where('information_sheet_id', $this->information_sheet_id)
            ->where('order', '<', $this->order)
            ->orderBy('order', 'desc')
            ->first();
    }
}