<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class AlertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'categorie_budget_id' => 'required|exists:categorie_budget,id',
            'type' => 'required|in:pourcentage,montant',
            'seuil' => 'required|numeric|min:0',
        ];
    }
    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'categorie_budget_id' => 'catégorie budgétaire',
            'type' => 'type d\'alerte',
            'seuil' => 'seuil',
        ];
    }
}