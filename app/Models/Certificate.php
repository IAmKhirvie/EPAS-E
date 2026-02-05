<?php

namespace App\Models;

use App\Traits\HasCommonScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory, HasCommonScopes;

    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'title',
        'description',
        'issue_date',
        'expiry_date',
        'status',
        'metadata',
        'pdf_path',
        'requested_by',
        'requested_at',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'metadata' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public static function generateCertificateNumber()
    {
        $prefix = 'CERT';
        $year = date('Y');
        $random = strtoupper(substr(md5(uniqid()), 0, 8));
        return "{$prefix}-{$year}-{$random}";
    }
}
