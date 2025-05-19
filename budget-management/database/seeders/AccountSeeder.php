<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pour chaque utilisateur standard (non admin), créer 2 comptes
        $users = User::where('role', 'user')->get();
        
        foreach ($users as $user) {
            // Compte courant
            Account::create([
                'utilisateur_id' => $user->id,
                'nom' => 'Compte courant',
                'solde' => rand(1000, 10000),
                'devise' => 'MAD',
                'date_creation' => now(),
            ]);
            
            // Compte épargne
            Account::create([
                'utilisateur_id' => $user->id,
                'nom' => 'Épargne',
                'solde' => rand(5000, 50000),
                'devise' => 'MAD',
                'date_creation' => now(),
            ]);
        }
    }
}