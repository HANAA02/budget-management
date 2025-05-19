<?php

namespace App\Policies;

use App\Models\CategoryBudget;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryBudgetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CategoryBudget  $categoryBudget
     * @return bool
     */
    public function view(User $user, CategoryBudget $categoryBudget): bool
    {
        return $user->id === $categoryBudget->budget->utilisateur_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CategoryBudget  $categoryBudget
     * @return bool
     */
    public function update(User $user, CategoryBudget $categoryBudget): bool
    {
        return $user->id === $categoryBudget->budget->utilisateur_id;
    }

    /**
     * Determine whether the user can create expenses for this category budget.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CategoryBudget  $categoryBudget
     * @return bool
     */
    public function createExpense(User $user, CategoryBudget $categoryBudget): bool
    {
        return $user->id === $categoryBudget->budget->utilisateur_id;
    }

    /**
     * Determine whether the user can create alerts for this category budget.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\CategoryBudget  $categoryBudget
     * @return bool
     */
    public function createAlert(User $user, CategoryBudget $categoryBudget): bool
    {
        return $user->id === $categoryBudget->budget->utilisateur_id;
    }
}