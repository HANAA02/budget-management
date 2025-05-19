<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Goal extends Model
{use HasFactory;
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