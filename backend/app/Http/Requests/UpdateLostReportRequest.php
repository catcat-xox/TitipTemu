<?php

namespace App\Http\Requests;

use App\Models\LostReport;
use App\Support\WhatsAppLink;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLostReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $report = $this->route('report');

        return $report instanceof LostReport
            && $report->user_id === $this->user()?->id
            && in_array($report->status, ['pending', 'rejected'], true);
    }

    public function rules(): array
    {
        return [
            'item_name' => ['required', 'string', 'min:2', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'lost_at' => ['required', 'date', 'before_or_equal:today'],
            'location' => ['required', 'string', 'min:3', 'max:150'],
            'contact_phone' => [
                'required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/',
                function (string $attribute, mixed $value, callable $fail): void {
                    if (WhatsAppLink::normalizePhone(is_string($value) ? $value : null) === null) {
                        $fail('Nomor HP penemu tidak valid.');
                    }
                },
            ],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'item_name' => 'nama barang',
            'lost_at' => 'tanggal ditemukan',
            'contact_phone' => 'no. HP penemu',
            'photo' => 'foto barang',
        ];
    }
}
