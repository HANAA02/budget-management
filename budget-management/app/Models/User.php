<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom', 'prenom', 'email', 'role', 'mot_de_passe',
    ];

    protected $hidden = [
        'mot_de_passe', 'remember_token',
    ];

    public function comptes()
    {
        return $this->hasMany(Account::class, 'utilisateur_id');
    }

    public function revenus()
    {
        return $this->hasMany(Income::class, 'utilisateur_id');
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class, 'utilisateur_id');
    }

    public function objectifs()
    {
        return $this->hasMany(Goal::class, 'utilisateur_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}