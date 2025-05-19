<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:100',
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:50',
            'pourcentage_defaut' => 'required|numeric|min:0|max:100',
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
            'nom' => 'nom de la catégorie',
            'description' => 'description',
            'icone' => 'icône',
            'pourcentage_defaut' => 'pourcentage par défaut',
        ];
    }
}