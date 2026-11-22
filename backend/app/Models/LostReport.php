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
    'returned_at',
    'archived_at',
])]
class LostReport extends Model
{
    use HasFactory;

    public const FOUND_DISPLAY_DAYS = 30;

    protected function casts(): array
    {
        return [
            'lost_at' => 'date',
            'terms_accepted_at' => 'datetime',
            'returned_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class);
    }

    public function latestRejection(): HasOne
    {
        return $this->hasOne(ReportStatusHistory::class)
            ->where('action', 'reject')
            ->latestOfMany();
    }

    public function returnConfirmations(): HasMany
    {
        return $this->hasMany(ReturnConfirmation::class);
    }

    public function latestReturnConfirmation(): HasOne
    {
        return $this->hasOne(ReturnConfirmation::class)->latestOfMany('id');
    }

    public function latestReturnRejection(): HasOne
    {
        return $this->hasOne(ReturnConfirmation::class)
            ->where('status', 'rejected')
            ->latestOfMany('id');
    }

    public function archiveDueAt(): ?Carbon
    {
        return $this->returned_at?->copy()->addDays(self::FOUND_DISPLAY_DAYS);
    }

    public function isArchiveDue(?Carbon $now = null): bool
    {
        if ($this->status !== 'returned' || ! $this->returned_at) {
            return false;
        }

        return ($now ?? now())->greaterThanOrEqualTo($this->archiveDueAt());
    }

    public function scopePublicLost($query)
    {
        return $query->whereIn('status', ['approved', 'return_pending']);
    }

    public function scopePublicFound($query)
    {
        return $query->where('status', 'returned')
            ->whereNotNull('returned_at')
            ->where('returned_at', '>', now()->subDays(self::FOUND_DISPLAY_DAYS));
    }

    public function isPublic(): bool
    {
        return in_array($this->status, ['approved', 'return_pending'], true)
            || ($this->status === 'returned'
                && $this->returned_at?->greaterThan(now()->subDays(self::FOUND_DISPLAY_DAYS)));
    }
}
