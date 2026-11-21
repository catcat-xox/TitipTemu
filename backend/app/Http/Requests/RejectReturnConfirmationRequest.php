<?php

namespace App\Http\Requests;

use App\Models\ReturnConfirmation;
use Illuminate\Foundation\Http\FormRequest;

class RejectReturnConfirmationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $confirmation = $this->route('confirmation');

        return $confirmation instanceof ReturnConfirmation
            && $confirmation->status === 'pending'
            && $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'reason' => 'alasan penolakan',
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'Alasan penolakan wajib diisi.',
        ];
    }
}
