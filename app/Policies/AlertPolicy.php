<?php

namespace App\Policies;

use App\Models\Alert;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlertPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Alert  $alert
     * @return bool
     */
    public function view(User $user, Alert $alert): bool
    {
        return $user->id === $alert->categorieBudget->budget->utilisateur_id || $user->role === 'admin';
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Alert  $alert
     * @return bool
     */
    public function update(User $user, Alert $alert): bool
    {
        return $user->id === $alert->categorieBudget->budget->utilisateur_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Alert  $alert
     * @return bool
     */
    public function delete(User $user, Alert $alert): bool
    {
        return $user->id === $alert->categorieBudget->budget->utilisateur_id;
    }
}