<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
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
            'ruc' => 'required|string|unique:companies,ruc|max:11',
            'name' => 'required|string|unique:companies,name|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'ruc.required' => 'El RUC es obligatorio',
            'ruc.unique' => 'El RUC ya existe',
            'ruc.max' => 'El RUC no puede tener más de 11 caracteres',
            'name.required' => 'La razón social es obligatoria',
            'name.unique' => 'La razón social ya existe',
            'name.max' => 'La razón social no puede tener más de 255 caracteres'
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'ruc' => str_replace(' ', '', $this->ruc)
        ]);
    }
}
