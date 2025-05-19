<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $table = 'alertes';
    
    protected $fillable = [
        'categorie_budget_id', 'type', 'seuil', 'active',
    ];

    public function categorieBudget()
    {
        return $this->belongsTo(CategoryBudget::class, 'categorie_budget_id');
    }
}