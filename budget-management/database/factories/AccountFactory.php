<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'utilisateur_id' => User::factory(),
            'nom' => $this->faker->randomElement(['Courant', 'Épargne', 'Investissement', 'Salaire']),
            'solde' => $this->faker->randomFloat(2, 100, 10000),
            'devise' => 'MAD',
            'date_creation' => $this->faker->dateTimeThisYear(),
        ];
    }
}