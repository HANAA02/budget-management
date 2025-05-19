<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomeResource extends JsonResource
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
            'source' => $this->source,
            'montant' => $this->montant,
            'date_perception' => $this->date_perception->format('Y-m-d'),
            'periodicite' => $this->periodicite,
            'compte' => $this->when($this->relationLoaded('compte'), function () {
                return [
                    'id' => $this->compte->id,
                    'nom' => $this->compte->nom,
                    'solde' => $this->compte->solde,
                    'devise' => $this->compte->devise,
                ];
            }),
            'utilisateur' => new UserResource($this->whenLoaded('utilisateur')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}