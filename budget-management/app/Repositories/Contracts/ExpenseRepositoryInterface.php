<?php

namespace App\Repositories\Contracts;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface ExpenseRepositoryInterface
{
    /**
     * Récupère toutes les dépenses.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Récupère une dépense par son identifiant.
     *
     * @param int $id
     * @return Expense|null
     */
    public function find(int $id): ?Expense;

    /**
     * Récupère les dépenses d'un utilisateur.
     *
     * @param User $user
     * @return Collection
     */
    public function findByUser(User $user): Collection;

    /**
     * Récupère les dépenses d'un utilisateur pour une période donnée.
     *
     * @param User $user
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    public function findByUserAndPeriod(User $user, Carbon $startDate, Carbon $endDate): Collection;

    /**
     * Récupère les dépenses d'un utilisateur pour une catégorie donnée.
     *
     * @param User $user
     * @param int $categoryId
     * @return Collection
     */
    public function findByUserAndCategory(User $user, int $categoryId): Collection;

    /**
     * Récupère les dépenses d'un utilisateur pour un budget donné.
     *
     * @param User $user
     * @param int $budgetId
     * @return Collection
     */
    public function findByUserAndBudget(User $user, int $budgetId): Collection;

    /**
     * Crée une nouvelle dépense.
     *
     * @param array $data
     * @return Expense
     */
    public function create(array $data): Expense;

    /**
     * Met à jour une dépense.
     *
     * @param Expense $expense
     * @param array $data
     * @return bool
     */
    public function update(Expense $expense, array $data): bool;

    /**
     * Supprime une dépense.
     *
     * @param Expense $expense
     * @return bool
     */
    public function delete(Expense $expense): bool;

    /**
     * Récupère les dépenses récentes d'un utilisateur.
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function findRecentByUser(User $user, int $limit = 5): Collection;

    /**
     * Calcule les dépenses mensuelles d'un utilisateur.
     *
     * @param User $user
     * @param int $months
     * @return array
     */
    public function getMonthlyExpenses(User $user, int $months = 6): array;
}