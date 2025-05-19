<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $rules = [
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'role' => 'required|in:admin,user',
        ];
        
        // Si c'est une création d'utilisateur, le mot de passe est requis
        if ($this->isMethod('post')) {
            $rules['email'] = 'required|string|email|max:100|unique:utilisateurs,email';
            $rules['mot_de_passe'] = 'required|string|min:8';
        } else {
            // Pour une mise à jour, l'email doit être unique sauf pour l'utilisateur en cours
            $userId = $this->route('user')->id;
            $rules['email'] = [
                'required',
                'string',
                'email',
                'max:100',
                Rule::unique('utilisateurs', 'email')->ignore($userId),
            ];
            $rules['mot_de_passe'] = 'nullable|string|min:8';
        }
        
        return $rules;
    }
    
    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom',
            'prenom' => 'prénom',
            'email' => 'adresse e-mail',
            'role' => 'rôle',
            'mot_de_passe' => 'mot de passe',
        ];
    }
}