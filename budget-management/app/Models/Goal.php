<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $table = 'objectifs';
    
    protected $fillable = [
        'utilisateur_id', 'categorie_id', 'titre', 'description', 
        'montant_cible', 'date_debut', 'date_fin', 'statut',
    ];

    protected $dates = [
        'date_debut', 'date_fin',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }
}