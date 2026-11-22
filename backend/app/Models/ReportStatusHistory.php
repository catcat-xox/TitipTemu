<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'lost_report_id',
    'actor_id',
    'admin_id',
    'action',
    'from_status',
    'to_status',
    'reason',
    'note',
])]
class ReportStatusHistory extends Model
{
    use HasFactory;

    public function report(): BelongsTo
    {
        return $this->belongsTo(LostReport::class, 'lost_report_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
