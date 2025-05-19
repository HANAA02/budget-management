<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ExpenseAnalysisService
{
    /**
     * Récupère les dépenses récentes pour un utilisateur.
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getRecentExpenses(User $user, int $limit = 5)
    {
        return \App\Models\Expense::whereHas('categorieBudget', function ($query) use ($user) {
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
     * Récupère les alertes récentes pour un utilisateur.
     *
     * @param User $user
     * @param int $limit
     * @return Collection
     */
    public function getRecentAlerts(User $user, int $limit = 5)
    {
        return \App\Models\Alert::whereHas('categorieBudget', function ($query) use ($user) {
            $query->whereHas('budget', function ($query) use ($user) {
                $query->where('utilisateur_id', $user->id);
            });
        })
        ->with(['categorieBudget.categorie', 'categorieBudget.budget'])
        ->orderBy('created_at', 'desc')
        ->limit($limit)
        ->get();
    }
    
    /**
     * Récupère les dépenses mensuelles pour un utilisateur.
     *
     * @param User $user
     * @param int $months
     * @return array
     */
    public function getMonthlyExpenses(User $user, int $months = 6)
    {
        $result = [];
        
        // Récupérer les derniers mois
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->translatedFormat('F Y');
            
            // Récupérer les dépenses du mois
            $monthlyExpenses = \App\Models\Expense::whereHas('categorieBudget', function ($query) use ($user) {
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
    
    /**
     * Calcule la progression d'un objectif.
     *
     * @param Goal $goal
     * @return float
     */
    public function calculateGoalProgress(Goal $goal)
    {
        // Si l'objectif est complété, retourner 100%
        if ($goal->statut === 'complété') {
            return 100;
        }
        
        // Si l'objectif est abandonné, retourner la progression actuelle
        if ($goal->statut === 'abandonné') {
            return 0;
        }
        
        // Récupérer toutes les dépenses liées à la catégorie de l'objectif
        $depenses = $this->getExpensesForGoal($goal);
        
        // Calculer le total des dépenses
        $totalDepenses = $depenses->sum('montant');
        
        // Calculer le pourcentage de progression
        $progression = 0;
        
        if ($goal->montant_cible > 0) {
            $progression = ($totalDepenses / $goal->montant_cible) * 100;
        }
        
        // Limiter à 100%
        return min(100, $progression);
    }
    
    /**
     * Récupère les dépenses liées à un objectif.
     *
     * @param Goal $goal
     * @return Collection
     */
    public function getExpensesForGoal(Goal $goal)
    {
        return \App\Models\Expense::whereHas('categorieBudget', function ($query) use ($goal) {
            $query->where('categorie_id', $goal->categorie_id)
                  ->whereHas('budget', function ($query) use ($goal) {
                      $query->where('utilisateur_id', $goal->utilisateur_id)
                            ->where('date_debut', '>=', $goal->date_debut)
                            ->where('date_fin', '<=', $goal->date_fin);
                  });
        })
        ->with(['categorieBudget.categorie', 'categorieBudget.budget'])
        ->orderBy('date_depense', 'desc')
        ->get();
    }
    
    /**
     * Génère les données de progression d'un objectif au fil du temps.
     *
     * @param Goal $goal
     * @return array
     */
    public function getGoalProgressData(Goal $goal)
    {
        $result = [];
        
        // Calculer le nombre de mois entre la date de début et la date de fin
        $dateDebut = Carbon::parse($goal->date_debut);
        $dateFin = Carbon::parse($goal->date_fin);
        $diffMonths = $dateDebut->diffInMonths($dateFin) + 1;
        
        // Limiter à 12 mois maximum
        $diffMonths = min(12, $diffMonths);
        
        // Récupérer les dépenses mensuelles
        for ($i = 0; $i < $diffMonths; $i++) {
            $date = Carbon::parse($goal->date_debut)->addMonths($i);
            $monthKey = $date->translatedFormat('F Y');
            
            // Récupérer les dépenses du mois
            $monthlyExpenses = \App\Models\Expense::whereHas('categorieBudget', function ($query) use ($goal) {
                $query->where('categorie_id', $goal->categorie_id)
                      ->whereHas('budget', function ($query) use ($goal) {
                          $query->where('utilisateur_id', $goal->utilisateur_id);
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