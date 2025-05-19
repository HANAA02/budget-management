<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer un utilisateur administrateur
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Système',
            'email' => 'admin@budget.com',
            'role' => 'admin',
            'mot_de_passe' => Hash::make('admin123'),
            'date_creation' => now(),
        ]);
        
        // Créer un utilisateur standard
        User::create([
            'nom' => 'Utilisateur',
            'prenom' => 'Test',
            'email' => 'user@budget.com',
            'role' => 'user',
            'mot_de_passe' => Hash::make('user123'),
            'date_creation' => now(),
        ]);
        
        // Créer 10 utilisateurs aléatoires
        User::factory()->count(10)->create();
    }
}