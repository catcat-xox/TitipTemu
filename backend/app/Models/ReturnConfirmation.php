<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'lost_report_id',
    'user_id',
    'admin_id',
    'status',
    'attempt',
    'reported_returned_at',
    'proof_paths',
    'note',
    'rejection_reason',
    'admin_note',
    'is_admin_resolution',
    'reviewed_at',
])]
class ReturnConfirmation extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'proof_paths' => 'array',
            'reported_returned_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'is_admin_resolution' => 'bool',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(LostReport::class, 'lost_report_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
