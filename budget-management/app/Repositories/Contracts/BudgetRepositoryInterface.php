<?php

namespace App\Repositories\Contracts;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface BudgetRepositoryInterface
{
    /**
     * Récupère tous les budgets.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Récupère un budget par son identifiant.
     *
     * @param int $id
     * @return Budget|null
     */
    public function find(int $id): ?Budget;

    /**
     * Récupère les budgets d'un utilisateur.
     *
     * @param User $user
     * @return Collection
     */
    public function findByUser(User $user): Collection;

    /**
     * Récupère les budgets actifs d'un utilisateur pour une date donnée.
     *
     * @param User $user
     * @param Carbon|null $date
     * @return Collection
     */
    public function findActiveByUser(User $user, ?Carbon $date = null): Collection;

    /**
     * Crée un nouveau budget.
     *
     * @param array $data
     * @return Budget
     */
    public function create(array $data): Budget;

    /**
     * Met à jour un budget.
     *
     * @param Budget $budget
     * @param array $data
     * @return bool
     */
    public function update(Budget $budget, array $data): bool;

    /**
     * Supprime un budget.
     *
     * @param Budget $budget
     * @return bool
     */
    public function delete(Budget $budget): bool;

    /**
     * Récupère le budget actif le plus récent d'un utilisateur.
     *
     * @param User $user
     * @return Budget|null
     */
    public function findLatestActiveByUser(User $user): ?Budget;
}