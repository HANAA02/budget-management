<?php

namespace App\Repositories\Eloquent;

use App\Models\Income;
use App\Models\User;
use App\Repositories\Contracts\IncomeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class IncomeRepository implements IncomeRepositoryInterface
{
    /**
     * @var Income
     */
    protected $model;

    /**
     * Constructeur
     *
     * @param Income $model
     */
    public function __construct(Income $model)
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
    public function find(int $id): ?Income
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUser(User $user): Collection
    {
        return $this->model->where('utilisateur_id', $user->id)
                           ->with('compte')
                           ->orderBy('date_perception', 'desc')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserAndPeriod(User $user, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->model->where('utilisateur_id', $user->id)
                           ->whereBetween('date_perception', [$startDate, $endDate])
                           ->with('compte')
                           ->orderBy('date_perception', 'desc')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserAndAccount(User $user, int $accountId): Collection
    {
        return $this->model->where('utilisateur_id', $user->id)
                           ->where('compte_id', $accountId)
                           ->orderBy('date_perception', 'desc')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Income
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Income $income, array $data): bool
    {
        return $income->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Income $income): bool
    {
        return $income->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTotalByUserAndPeriod(User $user, Carbon $startDate, Carbon $endDate): float
    {
        return $this->model->where('utilisateur_id', $user->id)
                           ->whereBetween('date_perception', [$startDate, $endDate])
                           ->sum('montant');
    }
}