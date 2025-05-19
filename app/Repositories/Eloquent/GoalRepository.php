<?php

namespace App\Repositories\Eloquent;

use App\Models\Goal;
use App\Models\User;
use App\Repositories\Contracts\GoalRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class GoalRepository implements GoalRepositoryInterface
{
    /**
     * @var Goal
     */
    protected $model;

    /**
     * Constructeur
     *
     * @param Goal $model
     */
    public function __construct(Goal $model)
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
    public function find(int $id): ?Goal
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUser(User $user): Collection
    {
        return $this->model->where('utilisateur_id', $user->id)
                           ->with('categorie')
                           ->orderBy('date_fin')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findActiveByUser(User $user): Collection
    {
        return $this->model->where('utilisateur_id', $user->id)
                           ->where('date_fin', '>=', Carbon::now())
                           ->where('statut', 'en cours')
                           ->with('categorie')
                           ->orderBy('date_fin')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Goal
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Goal $goal, array $data): bool
    {
        return $goal->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Goal $goal): bool
    {
        return $goal->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function changeStatus(Goal $goal, string $status): bool
    {
        return $goal->update([
            'statut' => $status
        ]);
    }
}