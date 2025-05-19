<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CategoryRepository implements CategoryRepositoryInterface
{
    /**
     * @var Category
     */
    protected $model;

    /**
     * Constructeur
     *
     * @param Category $model
     */
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): Collection
    {
        return $this->model->orderBy('nom')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id): ?Category
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Category $category, array $data): bool
    {
        return $category->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Category $category): bool
    {
        return $category->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function findActive(): Collection
    {
        return $this->model->where('pourcentage_defaut', '>', 0)
                           ->orderBy('nom')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findMostUsed(int $limit = 5): Collection
    {
        return $this->model->select('categories.id', 'categories.nom', DB::raw('SUM(depenses.montant) as total'))
                           ->join('categorie_budget', 'categories.id', '=', 'categorie_budget.categorie_id')
                           ->join('depenses', 'categorie_budget.id', '=', 'depenses.categorie_budget_id')
                           ->groupBy('categories.id', 'categories.nom')
                           ->orderBy('total', 'desc')
                           ->limit($limit)
                           ->get();
    }
}