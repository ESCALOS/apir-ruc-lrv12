<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchCompanyByNameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string'   => 'El nombre debe ser texto.',
            'name.min'      => 'El nombre debe tener al menos 3 caracteres.',
        ];
    }
}
