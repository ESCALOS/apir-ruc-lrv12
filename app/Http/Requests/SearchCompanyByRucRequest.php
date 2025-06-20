<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchCompanyByRucRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ruc' => ['required', 'string', 'digits:11'],
        ];
    }

    public function messages(): array
    {
        return [
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.string'   => 'El RUC debe ser texto.',
            'ruc.digits'   => 'El RUC debe tener exactamente 11 dígitos numéricos.',
        ];
    }
}
