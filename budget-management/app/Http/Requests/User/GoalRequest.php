<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class GoalRequest extends FormRequest
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
            'categorie_id' => 'required|exists:categories,id',
            'titre' => 'required|string|max:100',
            'description' => 'nullable|string',
            'montant_cible' => 'required|numeric|min:0',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'statut' => 'nullable|in:en cours,complété,abandonné',
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
            'categorie_id' => 'catégorie',
            'titre' => 'titre de l\'objectif',
            'description' => 'description',
            'montant_cible' => 'montant cible',
            'date_debut' => 'date de début',
            'date_fin' => 'date de fin',
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
            'categorie_id.required' => 'Veuillez sélectionner une catégorie.',
            'categorie_id.exists' => 'La catégorie sélectionnée n\'existe pas.',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'montant_cible.min' => 'Le montant cible doit être supérieur ou égal à 0.',
        ];
    }
}