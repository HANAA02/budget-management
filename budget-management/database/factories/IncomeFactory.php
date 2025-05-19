<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Income::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sources = ['Salaire', 'Freelance', 'Dividendes', 'Loyer', 'Indemnités', 'Bonus', 'Autres'];
        $periodicites = ['mensuel', 'bimensuel', 'hebdomadaire', 'trimestriel', 'annuel', 'ponctuel'];
        
        return [
            'utilisateur_id' => User::factory(),
            'compte_id' => Account::factory(),
            'source' => $this->faker->randomElement($sources),
            'montant' => $this->faker->randomFloat(2, 3000, 15000),
            'date_perception' => $this->faker->dateTimeThisMonth(),
            'periodicite' => $this->faker->randomElement($periodicites),
        ];
    }
    
    /**
     * Configure the income for a specific user and account.
     *
     * @param User $user
     * @param Account $account
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forUserAndAccount(User $user, Account $account)
    {
        return $this->state(function (array $attributes) use ($user, $account) {
            return [
                'utilisateur_id' => $user->id,
                'compte_id' => $account->id,
            ];
        });
    }
}