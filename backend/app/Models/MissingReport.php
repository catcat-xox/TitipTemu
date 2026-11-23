<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

#[Fillable([
    'user_id',
    'item_name',
    'category',
    'description',
    'lost_at',
    'location',
    'contact_phone',
    'photo_path',
    'status',
    'terms_accepted_at',
    'found_at',
    'archived_at',
])]
class MissingReport extends Model
{
    use HasFactory;

    public const FOUND_DISPLAY_DAYS = 30;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_FOUND_PENDING = 'found_pending';

    public const STATUS_FOUND = 'found';

    public const STATUS_ARCHIVED = 'archived';

    public const STATUS_CANCELLED = 'cancelled';

    protected function casts(): array
    {
        return [
            'lost_at' => 'date',
            'terms_accepted_at' => 'datetime',
            'found_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(MissingReportStatusHistory::class);
    }

    public function latestRejection(): HasOne
    {
        return $this->hasOne(MissingReportStatusHistory::class)
            ->where('action', 'reject')
            ->latestOfMany();
    }

    public function archiveDueAt(): ?Carbon
    {
        return $this->found_at?->copy()->addDays(self::FOUND_DISPLAY_DAYS);
    }

    public function isArchiveDue(?Carbon $now = null): bool
    {
        if ($this->status !== self::STATUS_FOUND || ! $this->found_at) {
            return false;
        }

        return ($now ?? now())->greaterThanOrEqualTo($this->archiveDueAt());
    }

    public function scopePublicUnfound($query)
    {
        return $query->whereIn('status', [self::STATUS_APPROVED, self::STATUS_FOUND_PENDING]);
    }

    public function scopePublicFound($query)
    {
        return $query->where('status', self::STATUS_FOUND)
            ->whereNotNull('found_at')
            ->where('found_at', '>', now()->subDays(self::FOUND_DISPLAY_DAYS));
    }

    public function isPublic(): bool
    {
        return in_array($this->status, [self::STATUS_APPROVED, self::STATUS_FOUND_PENDING], true)
            || ($this->status === self::STATUS_FOUND
                && $this->found_at?->greaterThan(now()->subDays(self::FOUND_DISPLAY_DAYS)));
    }
}
