<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'categorie_budget_id' => 'required|exists:categorie_budget,id',
            'description' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'statut' => 'nullable|in:validée,en attente,annulée',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'categorie_budget_id' => 'catégorie de budget',
            'description' => 'description',
            'montant' => 'montant',
            'date_depense' => 'date de dépense',
            'statut' => 'statut',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'categorie_budget_id.required' => 'Veuillez sélectionner une catégorie de budget.',
            'categorie_budget_id.exists' => 'La catégorie de budget sélectionnée n\'existe pas.',
            'montant.min' => 'Le montant de la dépense doit être supérieur ou égal à 0.',
        ];
    }
}