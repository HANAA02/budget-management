<?php

namespace App\Repositories\Contracts;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface GoalRepositoryInterface
{
    /**
     * Récupère tous les objectifs.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Récupère un objectif par son identifiant.
     *
     * @param int $id
     * @return Goal|null
     */
    public function find(int $id): ?Goal;

    /**
     * Récupère les objectifs d'un utilisateur.
     *
     * @param User $user
     * @return Collection
     */
    public function findByUser(User $user): Collection;

    /**
     * Récupère les objectifs actifs d'un utilisateur.
     *
     * @param User $user
     * @return Collection
     */
    public function findActiveByUser(User $user): Collection;

    /**
     * Crée un nouvel objectif.
     *
     * @param array $data
     * @return Goal
     */
    public function create(array $data): Goal;

    /**
     * Met à jour un objectif.
     *
     * @param Goal $goal
     * @param array $data
     * @return bool
     */
    public function update(Goal $goal, array $data): bool;

    /**
     * Supprime un objectif.
     *
     * @param Goal $goal
     * @return bool
     */
    public function delete(Goal $goal): bool;

    /**
     * Change le statut d'un objectif.
     *
     * @param Goal $goal
     * @param string $status
     * @return bool
     */
    public function changeStatus(Goal $goal, string $status): bool;
}