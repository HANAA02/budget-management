<?php

namespace Database\Factories;

use App\Models\CategoryBudget;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryBudgetFactory extends Factory
{
    protected $model = CategoryBudget::class;

    public function definition(): array
    {
        return [
            'budget_id' => Budget::factory(),
            'categorie_id' => Category::factory(),
            'montant_alloue' => $this->faker->randomFloat(2, 50, 1000),
            'pourcentage' => $this->faker->numberBetween(1, 100),
        ];
    }
}
