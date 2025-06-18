<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ruc' => 'nullable|string',
            'name' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'ruc.string' => 'El RUC debe ser texto',
            'name.string' => 'El nombre debe ser texto'
        ];
    }

    public function validateRuc(): bool
    {
        if ($this->has('ruc')) {
            $ruc = str_replace(' ', '', $this->ruc);
            return strlen($ruc) === 11 && ctype_digit($ruc);
        }
        return true;
    }
}
