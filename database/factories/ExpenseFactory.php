<?php

namespace Database\Factories;

use App\Models\CategoryBudget;
use App\Models\Expense;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statuts = ['validée', 'en attente', 'annulée'];
        
        return [
            'categorie_budget_id' => CategoryBudget::factory(),
            'description' => $this->faker->sentence(4),
            'montant' => $this->faker->randomFloat(2, 10, 1000),
            'date_depense' => $this->faker->dateTimeThisMonth(),
            'statut' => $this->faker->randomElement($statuts),
        ];
    }
    
    /**
     * Configure the expense for a specific category budget.
     *
     * @param CategoryBudget $categoryBudget
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forCategoryBudget(CategoryBudget $categoryBudget)
    {
        return $this->state(function (array $attributes) use ($categoryBudget) {
            return [
                'categorie_budget_id' => $categoryBudget->id,
            ];
        });
    }
    
    /**
     * Set the expense as validated.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function validated()
    {
        return $this->state(function (array $attributes) {
            return [
                'statut' => 'validée',
            ];
        });
    }
}