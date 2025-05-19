<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'role' => $this->role,
            'date_creation' => $this->date_creation->format('Y-m-d H:i:s'),
            'dernier_login' => $this->dernier_login ? $this->dernier_login->format('Y-m-d H:i:s') : null,
            'comptes' => $this->when($this->relationLoaded('comptes'), function () {
                return $this->comptes->map(function ($compte) {
                    return [
                        'id' => $compte->id,
                        'nom' => $compte->nom,
                        'solde' => $compte->solde,
                        'devise' => $compte->devise,
                    ];
                });
            }),
            'budgets' => $this->when($this->relationLoaded('budgets'), function () {
                return $this->budgets->map(function ($budget) {
                    return [
                        'id' => $budget->id,
                        'nom' => $budget->nom,
                        'date_debut' => $budget->date_debut->format('Y-m-d'),
                        'date_fin' => $budget->date_fin->format('Y-m-d'),
                        'montant_total' => $budget->montant_total,
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}