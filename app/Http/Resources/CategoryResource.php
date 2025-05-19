<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'description' => $this->description,
            'icone' => $this->icone,
            'pourcentage_defaut' => $this->pourcentage_defaut,
            'budgets' => $this->when($this->relationLoaded('budgets'), function () {
                return $this->budgets->map(function ($budget) {
                    return [
                        'id' => $budget->id,
                        'nom' => $budget->nom,
                        'montant_alloue' => $budget->pivot->montant_alloue,
                        'pourcentage' => $budget->pivot->pourcentage,
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}