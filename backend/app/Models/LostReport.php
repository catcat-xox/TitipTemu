<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'item_name',
    'category',
    'description',
    'lost_at',
    'location',
    'photo_path',
    'status',
    'terms_accepted_at',
    'returned_at',
    'archived_at',
])]
class LostReport extends Model
{
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

    public function scopePublicLost($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePublicFound($query)
    {
        return $query->where('status', 'returned')
            ->whereNotNull('returned_at')
            ->where('returned_at', '>=', now()->subDays(30));
    }

    public function isPublic(): bool
    {
        return $this->status === 'approved'
            || ($this->status === 'returned'
                && $this->returned_at?->greaterThanOrEqualTo(now()->subDays(30)));
    }
}
