<?php

namespace App\Repositories\Eloquent;

use App\Models\Budget;
use App\Models\User;
use App\Repositories\Contracts\BudgetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class BudgetRepository implements BudgetRepositoryInterface
{
    /**
     * @var Budget
     */
    protected $model;

    /**
     * Constructeur
     *
     * @param Budget $model
     */
    public function __construct(Budget $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * {@inheritdoc}
     */
    public function find(int $id): ?Budget
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUser(User $user): Collection
    {
        return $this->model->where('utilisateur_id', $user->id)
                           ->orderBy('date_debut', 'desc')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findActiveByUser(User $user, ?Carbon $date = null): Collection
    {
        $date = $date ?? Carbon::now();

        return $this->model->where('utilisateur_id', $user->id)
                           ->where('date_debut', '<=', $date)
                           ->where('date_fin', '>=', $date)
                           ->orderBy('date_creation', 'desc')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Budget
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Budget $budget, array $data): bool
    {
        return $budget->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Budget $budget): bool
    {
        return $budget->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function findLatestActiveByUser(User $user): ?Budget
    {
        return $this->model->where('utilisateur_id', $user->id)
                           ->where('date_debut', '<=', Carbon::now())
                           ->where('date_fin', '>=', Carbon::now())
                           ->orderBy('date_creation', 'desc')
                           ->first();
    }
}