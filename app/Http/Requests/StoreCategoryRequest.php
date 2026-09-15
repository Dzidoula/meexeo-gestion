<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Le filtrage par rôle est porté par le middleware de la route.
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255', 'unique:categories,name']];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Donnez un nom à la catégorie.',
            'name.unique' => 'Cette catégorie existe déjà.',
        ];
    }
}
