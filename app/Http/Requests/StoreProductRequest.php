<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Choisissez une catégorie.',
            'category_id.exists' => 'Cette catégorie n\'existe pas.',
            'name.required' => 'Donnez un nom au produit.',
            'price.integer' => 'Le prix doit être un montant entier en francs CFA.',
            'price.min' => 'Le prix ne peut pas être négatif.',
            'stock_quantity.min' => 'La quantité en stock ne peut pas être négative.',
        ];
    }
}
