<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $table = 'depenses';
    
    protected $fillable = [
        'categorie_budget_id', 'description', 'montant', 'date_depense', 'statut',
    ];

    protected $dates = [
        'date_depense',
    ];

    public function categorieBudget()
    {
        return $this->belongsTo(CategoryBudget::class, 'categorie_budget_id');
    }
}