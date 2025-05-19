<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alert extends Model
{use HasFactory;

    protected $table = 'alertes';
    
    protected $fillable = [
        'categorie_budget_id', 'type', 'seuil', 'active',
    ];

    public function categorieBudget()
    {
        return $this->belongsTo(CategoryBudget::class, 'categorie_budget_id');
    }
}