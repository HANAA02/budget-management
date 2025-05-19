<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'revenus';
    
    protected $fillable = [
        'utilisateur_id', 'compte_id', 'source', 'montant', 'date_perception', 'periodicite',
    ];

    protected $dates = [
        'date_perception',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function compte()
    {
        return $this->belongsTo(Account::class, 'compte_id');
    }
}