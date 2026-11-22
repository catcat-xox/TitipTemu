<?php

namespace App\Http\Requests;

use App\Models\LostReport;
use Illuminate\Foundation\Http\FormRequest;

class StoreReturnConfirmationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $report instanceof LostReport
            && $report->user_id === $this->user()?->id
            && $report->status === 'approved';
    }

    public function rules(): array
    {
        return [
            'owner_name' => ['required', 'string', 'min:2', 'max:100'],
            'proof' => ['required', 'array', 'min:1', 'max:3'],
            'proof.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'note' => ['nullable', 'string', 'max:500'],
            'return_statement' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'owner_name' => 'nama pemilik',
            'proof' => 'foto bersama penemu dan pemilik',
            'proof.*' => 'foto bersama penemu dan pemilik',
            'note' => 'catatan pengembalian',
            'return_statement' => 'pernyataan pengembalian',
        ];
    }
}
