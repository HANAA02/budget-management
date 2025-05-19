<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            AccountSeeder::class,
        ]);
        
        // Créer des données complexes pour un utilisateur test
        $this->command->info('Création de données de test complètes pour l\'utilisateur demo...');
        
        $user = \App\Models\User::where('email', 'user@budget.com')->first();
        
        if ($user) {
            // Créer des revenus pour l'utilisateur
            $account = $user->comptes()->first();
            
            if ($account) {
                \App\Models\Income::factory()
                    ->count(3)
                    ->forUserAndAccount($user, $account)
                    ->create();
            }
            
            // Créer un budget pour l'utilisateur
            $budget = \App\Models\Budget::factory()
                ->currentMonth()
                ->create([
                    'utilisateur_id' => $user->id,
                    'montant_total' => 12000,
                ]);
            
            // Créer des catégories budgétaires
            $categories = \App\Models\Category::all();
            
            foreach ($categories as $category) {
                $percentage = $category->pourcentage_defaut;
                $amount = ($percentage / 100) * $budget->montant_total;
                
                $categoryBudget = \App\Models\CategoryBudget::create([
                    'budget_id' => $budget->id,
                    'categorie_id' => $category->id,
                    'montant_alloue' => $amount,
                    'pourcentage' => $percentage,
                ]);
                
                // Créer quelques dépenses pour chaque catégorie
                \App\Models\Expense::factory()
                    ->count(rand(2, 5))
                    ->forCategoryBudget($categoryBudget)
                    ->validated()
                    ->create();
            }
            
            // Créer des objectifs pour l'utilisateur
            \App\Models\Goal::factory()
                ->count(3)
                ->inProgress()
                ->create([
                    'utilisateur_id' => $user->id,
                    'categorie_id' => $categories->random()->id,
                ]);
        }
    }
}