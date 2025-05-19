<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
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
            'date_debut' => $this->date_debut->format('Y-m-d'),
            'date_fin' => $this->date_fin->format('Y-m-d'),
            'montant_total' => $this->montant_total,
            'date_creation' => $this->date_creation->format('Y-m-d H:i:s'),
            'utilisateur' => new UserResource($this->whenLoaded('utilisateur')),
            'categories_budget' => $this->when($this->relationLoaded('categoriesBudget'), function () {
                return $this->categoriesBudget->map(function ($categorieBudget) {
                    return [
                        'id' => $categorieBudget->id,
                        'categorie' => [
                            'id' => $categorieBudget->categorie->id,
                            'nom' => $categorieBudget->categorie->nom,
                            'icone' => $categorieBudget->categorie->icone,
                        ],
                        'montant_alloue' => $categorieBudget->montant_alloue,
                        'pourcentage' => $categorieBudget->pourcentage,
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}