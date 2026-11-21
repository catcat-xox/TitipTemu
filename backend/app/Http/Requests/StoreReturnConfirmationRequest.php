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
            && $report->status === 'approved'
            && $this->user()?->hasVerifiedEmail() === true;
    }

    public function rules(): array
    {
        return [
            'proof' => ['required', 'array', 'min:1', 'max:3'],
            'proof.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'note' => ['nullable', 'string', 'max:500'],
            'return_statement' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'proof' => 'foto bukti',
            'proof.*' => 'foto bukti',
            'note' => 'catatan pengembalian',
            'return_statement' => 'pernyataan pengembalian',
        ];
    }
}
