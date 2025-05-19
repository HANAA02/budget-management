<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    /**
     * Récupère toutes les catégories.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Récupère une catégorie par son identifiant.
     *
     * @param int $id
     * @return Category|null
     */
    public function find(int $id): ?Category;

    /**
     * Crée une nouvelle catégorie.
     *
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category;

    /**
     * Met à jour une catégorie.
     *
     * @param Category $category
     * @param array $data
     * @return bool
     */
    public function update(Category $category, array $data): bool;

    /**
     * Supprime une catégorie.
     *
     * @param Category $category
     * @return bool
     */
    public function delete(Category $category): bool;

    /**
     * Récupère les catégories avec un pourcentage par défaut supérieur à zéro.
     *
     * @return Collection
     */
    public function findActive(): Collection;

    /**
     * Récupère les catégories les plus utilisées.
     *
     * @param int $limit
     * @return Collection
     */
    public function findMostUsed(int $limit = 5): Collection;
}