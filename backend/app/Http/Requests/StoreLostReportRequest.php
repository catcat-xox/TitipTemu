<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLostReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'item_name' => ['required', 'string', 'min:2', 'max:100'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:1000'],
            'lost_at' => ['required', 'date', 'before_or_equal:today'],
            'location' => ['required', 'string', 'min:3', 'max:150'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'terms_accepted' => ['accepted'],
        ];
    }

    public function attributes(): array
    {
        return [
            'item_name' => 'nama barang',
            'lost_at' => 'tanggal kehilangan',
            'photo' => 'foto barang',
            'terms_accepted' => 'persetujuan ketentuan',
        ];
    }
}
