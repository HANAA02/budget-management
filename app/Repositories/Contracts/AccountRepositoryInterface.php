<?php

namespace App\Repositories\Contracts;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface AccountRepositoryInterface
{
    /**
     * Récupère tous les comptes.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Récupère un compte par son identifiant.
     *
     * @param int $id
     * @return Account|null
     */
    public function find(int $id): ?Account;

    /**
     * Récupère les comptes d'un utilisateur.
     *
     * @param User $user
     * @return Collection
     */
    public function findByUser(User $user): Collection;

    /**
     * Crée un nouveau compte.
     *
     * @param array $data
     * @return Account
     */
    public function create(array $data): Account;

    /**
     * Met à jour un compte.
     *
     * @param Account $account
     * @param array $data
     * @return bool
     */
    public function update(Account $account, array $data): bool;

    /**
     * Supprime un compte.
     *
     * @param Account $account
     * @return bool
     */
    public function delete(Account $account): bool;

    /**
     * Calcule le solde total des comptes d'un utilisateur.
     *
     * @param User $user
     * @return float
     */
    public function getTotalBalance(User $user): float;
}