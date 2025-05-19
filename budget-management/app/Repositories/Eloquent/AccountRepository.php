<?php

namespace App\Repositories\Eloquent;

use App\Models\Account;
use App\Models\User;
use App\Repositories\Contracts\AccountRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AccountRepository implements AccountRepositoryInterface
{
    /**
     * @var Account
     */
    protected $model;

    /**
     * Constructeur
     *
     * @param Account $model
     */
    public function __construct(Account $model)
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
    public function find(int $id): ?Account
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUser(User $user): Collection
    {
        return $this->model->where('utilisateur_id', $user->id)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Account
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Account $account, array $data): bool
    {
        return $account->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Account $account): bool
    {
        return $account->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalBalance(User $user): float
    {
        return $this->model->where('utilisateur_id', $user->id)->sum('solde');
    }
}