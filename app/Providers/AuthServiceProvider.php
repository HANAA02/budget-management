<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Account::class => \App\Policies\AccountPolicy::class,
        \App\Models\Budget::class => \App\Policies\BudgetPolicy::class,
        \App\Models\CategoryBudget::class => \App\Policies\CategoryBudgetPolicy::class,
        \App\Models\Expense::class => \App\Policies\ExpensePolicy::class,
        \App\Models\Goal::class => \App\Policies\GoalPolicy::class,
        \App\Models\Income::class => \App\Policies\IncomePolicy::class,
        \App\Models\Alert::class => \App\Policies\AlertPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Définir la Gate admin qui vérifie si l'utilisateur est administrateur
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });
    }
}