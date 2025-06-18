<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $companyId = $this->route('company')->id;
        
        return [
            'ruc' => 'required|string|max:11|unique:companies,ruc,' . $companyId,
            'name' => 'required|string|max:255|unique:companies,name,' . $companyId
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
