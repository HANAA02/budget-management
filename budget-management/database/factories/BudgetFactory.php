<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Budget::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeThisYear();
        $endDate = clone $startDate;
        $endDate->modify('+1 month');
        
        return [
            'utilisateur_id' => User::factory(),
            'nom' => 'Budget ' . $this->faker->monthName . ' ' . $startDate->format('Y'),
            'date_debut' => $startDate,
            'date_fin' => $endDate,
            'montant_total' => $this->faker->randomFloat(2, 5000, 20000),
            'date_creation' => now(),
        ];
    }
    
    /**
     * Create a budget for the current month.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function currentMonth()
    {
        return $this->state(function (array $attributes) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
            
            return [
                'nom' => 'Budget ' . $startDate->format('F Y'),
                'date_debut' => $startDate,
                'date_fin' => $endDate,
            ];
        });
    }
}