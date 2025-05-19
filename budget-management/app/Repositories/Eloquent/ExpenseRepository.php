<?php

namespace App\Repositories\Eloquent;

use App\Models\Expense;
use App\Models\User;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class ExpenseRepository implements ExpenseRepositoryInterface
{
    /**
     * @var Expense
     */
    protected $model;

    /**
     * Constructeur
     *
     * @param Expense $model
     */
    public function __construct(Expense $model)
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
    public function find(int $id): ?Expense
    {
        return $this->model->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByUser(User $user): Collection
    {
        return $this->model->whereHas('categorieBudget', function ($query) use ($user) {
                               $query->whereHas('budget', function ($query) use ($user) {
                                   $query->where('utilisateur_id', $user->id);
                               });
                           })
                           ->with(['categorieBudget.categorie', 'categorieBudget.budget'])
                           ->orderBy('date_depense', 'desc')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserAndPeriod(User $user, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->model->whereHas('categorieBudget', function ($query) use ($user) {
                               $query->whereHas('budget', function ($query) use ($user) {
                                   $query->where('utilisateur_id', $user->id);
                               });
                           })
                           ->whereBetween('date_depense', [$startDate, $endDate])
                           ->with(['categorieBudget.categorie', 'categorieBudget.budget'])
                           ->orderBy('date_depense', 'desc')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserAndCategory(User $user, int $categoryId): Collection
    {
        return $this->model->whereHas('categorieBudget', function ($query) use ($user, $categoryId) {
                               $query->where('categorie_id', $categoryId)
                                     ->whereHas('budget', function ($query) use ($user) {
                                         $query->where('utilisateur_id', $user->id);
                                     });
                           })
                           ->with(['categorieBudget.categorie', 'categorieBudget.budget'])
                           ->orderBy('date_depense', 'desc')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUserAndBudget(User $user, int $budgetId): Collection
    {
        return $this->model->whereHas('categorieBudget', function ($query) use ($budgetId) {
                               $query->where('budget_id', $budgetId);
                           })
                           ->with(['categorieBudget.categorie', 'categorieBudget.budget'])
                           ->orderBy('date_depense', 'desc')
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Expense
    {
        return $this->model->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Expense $expense, array $data): bool
    {
        return $expense->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Expense $expense): bool
    {
        return $expense->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function findRecentByUser(User $user, int $limit = 5): Collection
    {
        return $this->model->whereHas('categorieBudget', function ($query) use ($user) {
                               $query->whereHas('budget', function ($query) use ($user) {
                                   $query->where('utilisateur_id', $user->id);
                               });
                           })
                           ->with(['categorieBudget.categorie', 'categorieBudget.budget'])
                           ->orderBy('date_depense', 'desc')
                           ->limit($limit)
                           ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getMonthlyExpenses(User $user, int $months = 6): array
    {
        $result = [];
        
        // Récupérer les derniers mois
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->translatedFormat('F Y');
            
            // Récupérer les dépenses du mois
            $monthlyExpenses = $this->model->whereHas('categorieBudget', function ($query) use ($user) {
                                    $query->whereHas('budget', function ($query) use ($user) {
                                        $query->where('utilisateur_id', $user->id);
                                    });
                                })
                                ->whereYear('date_depense', $date->year)
                                ->whereMonth('date_depense', $date->month)
                                ->sum('montant');
            
            $result[$monthKey] = $monthlyExpenses;
        }
        
        return $result;
    }
}