<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Account extends Model
{use HasFactory;

    protected $table = 'comptes';
    
    protected $fillable = [
        'utilisateur_id', 'nom', 'solde', 'devise',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function revenus()
    {
        return $this->hasMany(Income::class, 'compte_id');
    }
}