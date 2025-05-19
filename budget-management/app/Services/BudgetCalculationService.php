<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BudgetCalculationService
{
    /**
     * Calcule le revenu disponible pour un utilisateur.
     *
     * @param User $user
     * @return float
     */
    public function calculateAvailableIncome(User $user)
    {
        // Récupérer tous les revenus mensuels de l'utilisateur
        $revenus = $user->revenus()
            ->where('periodicite', 'mensuel')
            ->whereMonth('date_perception', Carbon::now()->month)
            ->whereYear('date_perception', Carbon::now()->year)
            ->get();

        // Calculer le total des revenus
        $totalRevenus = $revenus->sum('montant');

        // Récupérer tous les budgets actifs de l'utilisateur
        $budgetsActifs = $user->budgets()
            ->where('date_debut', '<=', Carbon::now())
            ->where('date_fin', '>=', Carbon::now())
            ->get();

        // Calculer le total déjà alloué
        $totalAlloue = $budgetsActifs->sum('montant_total');

        // Retourner le montant disponible
        return $totalRevenus - $totalAlloue;
    }

    /**
     * Calcule les totaux pour chaque catégorie de budget.
     *
     * @param Collection $categoriesBudget
     * @return array
     */
    public function calculateCategoryTotals(Collection $categoriesBudget)
    {
        $result = [];

        foreach ($categoriesBudget as $categorieBudget) {
            $totalDepenses = $categorieBudget->depenses->sum('montant');
            $montantRestant = $categorieBudget->montant_alloue - $totalDepenses;
            $pourcentageUtilise = ($categorieBudget->montant_alloue > 0) 
                ? ($totalDepenses / $categorieBudget->montant_alloue) * 100 
                : 0;

            $result[] = [
                'id' => $categorieBudget->id,
                'categorie_id' => $categorieBudget->categorie_id,
                'nom' => $categorieBudget->categorie->nom,
                'icone' => $categorieBudget->categorie->icone,
                'montant_alloue' => $categorieBudget->montant_alloue,
                'pourcentage' => $categorieBudget->pourcentage,
                'total_depenses' => $totalDepenses,
                'montant_restant' => $montantRestant,
                'pourcentage_utilise' => $pourcentageUtilise,
            ];
        }

        return $result;
    }

    /**
     * Calcule le total des dépenses pour un budget.
     *
     * @param int $budgetId
     * @return float
     */
    public function calculateTotalExpenses(int $budgetId)
    {
        $totalDepenses = 0;

        // Récupérer toutes les catégories du budget
        $categoriesBudget = \App\Models\CategoryBudget::where('budget_id', $budgetId)->get();

        // Calculer le total des dépenses
        foreach ($categoriesBudget as $categorieBudget) {
            $totalDepenses += $categorieBudget->depenses()->sum('montant');
        }

        return $totalDepenses;
    }

    /**
     * Calcule le montant total restant dans un budget.
     *
     * @param int $budgetId
     * @return float
     */
    public function calculateRemainingAmount(int $budgetId)
    {
        $budget = \App\Models\Budget::findOrFail($budgetId);
        $totalDepenses = $this->calculateTotalExpenses($budgetId);
        
        return $budget->montant_total - $totalDepenses;
    }

    /**
     * Calcule le pourcentage utilisé d'un budget.
     *
     * @param int $budgetId
     * @return float
     */
    public function calculateUsedPercentage(int $budgetId)
    {
        $budget = \App\Models\Budget::findOrFail($budgetId);
        $totalDepenses = $this->calculateTotalExpenses($budgetId);
        
        return ($budget->montant_total > 0) 
            ? ($totalDepenses / $budget->montant_total) * 100 
            : 0;
    }
}