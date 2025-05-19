<?php

namespace App\Repositories\Contracts;

use App\Models\Income;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface IncomeRepositoryInterface
{
    /**
     * Récupère tous les revenus.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Récupère un revenu par son identifiant.
     *
     * @param int $id
     * @return Income|null
     */
    public function find(int $id): ?Income;

    /**
     * Récupère les revenus d'un utilisateur.
     *
     * @param User $user
     * @return Collection
     */
    public function findByUser(User $user): Collection;

    /**
     * Récupère les revenus d'un utilisateur pour une période donnée.
     *
     * @param User $user
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    public function findByUserAndPeriod(User $user, Carbon $startDate, Carbon $endDate): Collection;

    /**
     * Récupère les revenus d'un utilisateur pour un compte donné.
     *
     * @param User $user
     * @param int $accountId
     * @return Collection
     */
    public function findByUserAndAccount(User $user, int $accountId): Collection;

    /**
     * Crée un nouveau revenu.
     *
     * @param array $data
     * @return Income
     */
    public function create(array $data): Income;

    /**
     * Met à jour un revenu.
     *
     * @param Income $income
     * @param array $data
     * @return bool
     */
    public function update(Income $income, array $data): bool;

    /**
     * Supprime un revenu.
     *
     * @param Income $income
     * @return bool
     */
    public function delete(Income $income): bool;

    /**
     * Calcule le revenu total d'un utilisateur pour une période donnée.
     *
     * @param User $user
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return float
     */
    public function calculateTotalByUserAndPeriod(User $user, Carbon $startDate, Carbon $endDate): float;
}