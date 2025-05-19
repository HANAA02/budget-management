<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryBudget extends Model
{
    protected $table = 'categorie_budget';
    
    protected $fillable = [
        'budget_id', 'categorie_id', 'montant_alloue', 'pourcentage',
    ];

    public function budget()
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function categorie()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function depenses()
    {
        return $this->hasMany(Expense::class, 'categorie_budget_id');
    }

    public function alertes()
    {
        return $this->hasMany(Alert::class, 'categorie_budget_id');
    }
}