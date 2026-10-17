<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'national_id',
        'job_title',
        'org_unit_id',
        'user_id',
        'is_active',
        'needs_review',
        'review_notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'needs_review' => 'boolean',
    ];

    public function organizationalUnit(): BelongsTo
    {
        return $this->belongsTo(OrganizationalUnit::class, 'org_unit_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rosterItems(): HasMany
    {
        return $this->hasMany(DailyRosterItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNeedsReview($query)
    {
        return $query->where('needs_review', true);
    }

    /**
     * Mask national ID for display in general lists:
     * e.g. "1087654321" -> "108****321"
     */
    public function getMaskedNationalIdAttribute(): string
    {
        $id = (string) $this->national_id;
        $len = strlen($id);
        if ($len <= 4) {
            return str_repeat('*', $len);
        }

        $visibleStart = 3;
        $visibleEnd = 3;

        if ($len < 8) {
            $visibleStart = 1;
            $visibleEnd = 2;
        }

        $middleLength = $len - $visibleStart - $visibleEnd;
        return substr($id, 0, $visibleStart) . str_repeat('*', $middleLength) . substr($id, -$visibleEnd);
    }
}
