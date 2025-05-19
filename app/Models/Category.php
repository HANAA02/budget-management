<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{use HasFactory;

    protected $table = 'categories';
    
    protected $fillable = [
        'nom', 'description', 'icone', 'pourcentage_defaut',
    ];

    public function budgets()
    {
        return $this->belongsToMany(Budget::class, 'categorie_budget')
                    ->withPivot('montant_alloue', 'pourcentage')
                    ->withTimestamps();
    }

    public function objectifs()
    {
        return $this->hasMany(Goal::class, 'categorie_id');
    }
}
