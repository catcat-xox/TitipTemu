<?php

namespace App\Http\Requests;

use App\Models\LostReport;
use Illuminate\Foundation\Http\FormRequest;

class ResolveReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');
        $latest = $report instanceof LostReport
            ? $report->returnConfirmations()->latest('id')->first()
            : null;

        return $report instanceof LostReport
            && $this->user()?->isAdmin() === true
            && $report->status === 'approved'
            && $latest?->status === 'rejected'
            && $latest->attempt >= 3;
    }

    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'note' => 'catatan penyelesaian Admin',
        ];
    }
}
