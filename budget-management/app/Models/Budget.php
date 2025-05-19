<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $table = 'budgets';
    
    protected $fillable = [
        'utilisateur_id', 'nom', 'date_debut', 'date_fin', 'montant_total',
    ];

    protected $dates = [
        'date_debut', 'date_fin', 'date_creation',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function categoriesBudget()
    {
        return $this->hasMany(CategoryBudget::class, 'budget_id');
    }
}
