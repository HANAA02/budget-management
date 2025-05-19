<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'description' => $this->description,
            'montant' => $this->montant,
            'date_depense' => $this->date_depense->format('Y-m-d'),
            'statut' => $this->statut,
            'categorie_budget' => $this->when($this->relationLoaded('categorieBudget'), function () {
                return [
                    'id' => $this->categorieBudget->id,
                    'categorie' => [
                        'id' => $this->categorieBudget->categorie->id,
                        'nom' => $this->categorieBudget->categorie->nom,
                        'icone' => $this->categorieBudget->categorie->icone,
                    ],
                    'budget' => [
                        'id' => $this->categorieBudget->budget->id,
                        'nom' => $this->categorieBudget->budget->nom,
                    ],
                    'montant_alloue' => $this->categorieBudget->montant_alloue,
                    'pourcentage' => $this->categorieBudget->pourcentage,
                ];
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}