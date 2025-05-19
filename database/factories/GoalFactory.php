<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GoalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Goal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeThisYear();
        $endDate = clone $startDate;
        $endDate->modify('+' . $this->faker->numberBetween(1, 12) . ' months');
        
        $statuts = ['en cours', 'complété', 'abandonné'];
        
        $objectives = [
            'Économiser pour vacances',
            'Réduire dépenses alimentaires',
            'Épargner pour achat immobilier',
            'Limiter frais de transport',
            'Fonds d\'urgence',
            'Épargne retraite',
            'Budget rénovation',
            'Achat d\'une voiture'
        ];
        
        return [
            'utilisateur_id' => User::factory(),
            'categorie_id' => Category::factory(),
            'titre' => $this->faker->randomElement($objectives),
            'description' => $this->faker->paragraph(),
            'montant_cible' => $this->faker->randomFloat(2, 1000, 50000),
            'date_debut' => $startDate,
            'date_fin' => $endDate,
            'statut' => $this->faker->randomElement($statuts),
        ];
    }
    
    /**
     * Set the goal as in progress.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inProgress()
    {
        return $this->state(function (array $attributes) {
            return [
                'statut' => 'en cours',
            ];
        });
    }
}