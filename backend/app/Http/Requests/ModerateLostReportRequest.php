<?php

namespace App\Http\Requests;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;

class ModerateLostReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') instanceof Admin;
    }

    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approve,reject'],
            'reason' => ['required_if:decision,reject', 'nullable', 'string', 'min:5', 'max:500'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'decision' => 'keputusan verifikasi',
            'reason' => 'alasan penolakan',
            'note' => 'catatan internal',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required_if' => 'Alasan penolakan wajib diisi.',
        ];
    }
}
