<?php

namespace App\Models;

use Database\Factories\AdminFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class Admin extends Authenticatable
{
    /** @use HasFactory<AdminFactory> */
    use HasFactory;

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ReportStatusHistory::class);
    }

    public function reviewedReturnConfirmations(): HasMany
    {
        return $this->hasMany(ReturnConfirmation::class, 'reviewer_admin_id');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
