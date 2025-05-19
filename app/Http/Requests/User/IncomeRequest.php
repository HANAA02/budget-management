<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class IncomeRequest extends FormRequest
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
            'compte_id' => 'required|exists:comptes,id',
            'source' => 'required|string|max:100',
            'montant' => 'required|numeric|min:0',
            'date_perception' => 'required|date',
            'periodicite' => 'required|in:mensuel,bimensuel,hebdomadaire,trimestriel,annuel,ponctuel',
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
            'compte_id' => 'compte',
            'source' => 'source du revenu',
            'montant' => 'montant',
            'date_perception' => 'date de perception',
            'periodicite' => 'périodicité',
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
            'compte_id.required' => 'Veuillez sélectionner un compte.',
            'compte_id.exists' => 'Le compte sélectionné n\'existe pas.',
            'montant.min' => 'Le montant du revenu doit être supérieur ou égal à 0.',
            'periodicite.in' => 'La périodicité sélectionnée n\'est pas valide.',
        ];
    }
}