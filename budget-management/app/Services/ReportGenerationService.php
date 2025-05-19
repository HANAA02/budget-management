<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;

class ReportGenerationService
{
    protected $expenseAnalysisService;
    protected $budgetCalculationService;
    
    public function __construct(
        ExpenseAnalysisService $expenseAnalysisService,
        BudgetCalculationService $budgetCalculationService
    ) {
        $this->expenseAnalysisService = $expenseAnalysisService;
        $this->budgetCalculationService = $budgetCalculationService;
    }
    
    /**
     * Génère un rapport mensuel pour un utilisateur.
     *
     * @param User $user
     * @param Carbon $dateDebut
     * @param Carbon $dateFin
     * @return array
     */
    public function generateMonthlyReport(User $user, Carbon $dateDebut, Carbon $dateFin)
    {
        // Récupérer les budgets de l'utilisateur pour la période
        $budgets = $user->budgets()
            ->where(function ($query) use ($dateDebut, $dateFin) {
                $query->where(function ($q) use ($dateDebut, $dateFin) {
                    $q->where('date_debut', '<=', $dateFin)
                      ->where('date_fin', '>=', $dateDebut);
                });
            })
            ->with('categoriesBudget.categorie', 'categoriesBudget.depenses')
            ->get();
            
        if ($budgets->isEmpty()) {
            return null;
        }
        
        // Récupérer les dépenses pour la période
        $depenses = \App\Models\Expense::whereHas('categorieBudget', function ($query) use ($user, $dateDebut, $dateFin) {
            $query->whereHas('budget', function ($query) use ($user) {
                $query->where('utilisateur_id', $user->id);
            });
        })
        ->whereBetween('date_depense', [$dateDebut, $dateFin])
        ->with('categorieBudget.categorie', 'categorieBudget.budget')
        ->orderBy('date_depense', 'desc')
        ->get();
        
        // Calculer les totaux pour chaque catégorie
        $categoriesData = [];
        
        foreach ($budgets as $budget) {
            foreach ($budget->categoriesBudget as $categorieBudget) {
                $categoryId = $categorieBudget->categorie_id;
                
                // Filtrer les dépenses pour cette catégorie et cette période
                $categoryExpenses = $depenses->filter(function ($expense) use ($categorieBudget) {
                    return $expense->categorie_budget_id === $categorieBudget->id;
                });
                
                $totalDepenses = $categoryExpenses->sum('montant');
                
                // Ajouter ou mettre à jour les données de la catégorie
                if (isset($categoriesData[$categoryId])) {
                    $categoriesData[$categoryId]['montant_alloue'] += $categorieBudget->montant_alloue;
                    $categoriesData[$categoryId]['total_depenses'] += $totalDepenses;
                } else {
                    $categoriesData[$categoryId] = [
                        'id' => $categoryId,
                        'nom' => $categorieBudget->categorie->nom,
                        'montant_alloue' => $categorieBudget->montant_alloue,
                        'total_depenses' => $totalDepenses,
                    ];
                }
            }
        }
        
        // Calculer les pourcentages et montants restants
        foreach ($categoriesData as &$category) {
            $category['montant_restant'] = $category['montant_alloue'] - $category['total_depenses'];
            $category['pourcentage_utilise'] = ($category['montant_alloue'] > 0) 
                ? ($category['total_depenses'] / $category['montant_alloue']) * 100 
                : 0;
        }
        
        // Calculer les totaux globaux
        $totalAlloue = array_sum(array_column($categoriesData, 'montant_alloue'));
        $totalDepenses = array_sum(array_column($categoriesData, 'total_depenses'));
        $montantRestant = $totalAlloue - $totalDepenses;
        $pourcentageUtilise = ($totalAlloue > 0) ? ($totalDepenses / $totalAlloue) * 100 : 0;
        
        // Structurer le rapport
        $report = [
            'periode' => [
                'debut' => $dateDebut->format('Y-m-d'),
                'fin' => $dateFin->format('Y-m-d'),
            ],
            'totaux' => [
                'alloue' => $totalAlloue,
                'depenses' => $totalDepenses,
                'restant' => $montantRestant,
                'pourcentage_utilise' => $pourcentageUtilise,
            ],
            'categories' => array_values($categoriesData),
            'depenses' => $depenses,
            'budgets' => $budgets,
        ];
        
        return $report;
    }
    
    /**
     * Génère un rapport personnalisé pour un utilisateur.
     *
     * @param User $user
     * @param string $typeRapport
     * @param string $dateDebut
     * @param string $dateFin
     * @param array $categories
     * @param array $budgets
     * @return array
     */
    public function generateReport(User $user, string $typeRapport, string $dateDebut, string $dateFin, array $categories = [], array $budgets = [])
    {
        // Convertir les dates en objets Carbon
        $dateDebut = Carbon::parse($dateDebut);
        $dateFin = Carbon::parse($dateFin);
        
        // Si le type de rapport n'est pas personnalisé, ajuster les dates
        if ($typeRapport === 'mensuel') {
            $dateDebut = Carbon::now()->startOfMonth();
            $dateFin = Carbon::now()->endOfMonth();
        } elseif ($typeRapport === 'trimestriel') {
            $dateDebut = Carbon::now()->startOfQuarter();
            $dateFin = Carbon::now()->endOfQuarter();
        } elseif ($typeRapport === 'annuel') {
            $dateDebut = Carbon::now()->startOfYear();
            $dateFin = Carbon::now()->endOfYear();
        }
        
        // Récupérer les budgets de l'utilisateur pour la période
        $budgetsQuery = $user->budgets()
            ->where(function ($query) use ($dateDebut, $dateFin) {
                $query->where(function ($q) use ($dateDebut, $dateFin) {
                    $q->where('date_debut', '<=', $dateFin)
                      ->where('date_fin', '>=', $dateDebut);
                });
            });
            
        // Filtrer par budgets spécifiques si nécessaire
        if (!empty($budgets)) {
            $budgetsQuery->whereIn('id', $budgets);
        }
        
        $budgetsData = $budgetsQuery->with('categoriesBudget.categorie', 'categoriesBudget.depenses')->get();
        
        if ($budgetsData->isEmpty()) {
            return [
                'periode' => [
                    'debut' => $dateDebut->format('Y-m-d'),
                    'fin' => $dateFin->format('Y-m-d'),
                    'type' => $typeRapport,
                ],
                'totaux' => [
                    'alloue' => 0,
                    'depenses' => 0,
                    'restant' => 0,
                    'pourcentage_utilise' => 0,
                ],
                'categories' => [],
                'depenses' => [],
                'budgets' => [],
            ];
        }
        
        // Récupérer les dépenses pour la période
        $depensesQuery = \App\Models\Expense::whereHas('categorieBudget', function ($query) use ($user, $budgets) {
            $query->whereHas('budget', function ($query) use ($user, $budgets) {
                $query->where('utilisateur_id', $user->id);
                if (!empty($budgets)) {
                    $query->whereIn('id', $budgets);
                }
            });
        })
        ->whereBetween('date_depense', [$dateDebut, $dateFin]);
        
        // Filtrer par catégories spécifiques si nécessaire
        if (!empty($categories)) {
            $depensesQuery->whereHas('categorieBudget', function ($query) use ($categories) {
                $query->whereIn('categorie_id', $categories);
            });
        }
        
        $depensesData = $depensesQuery->with('categorieBudget.categorie', 'categorieBudget.budget')
            ->orderBy('date_depense', 'desc')
            ->get();
        
        // Calculer les totaux pour chaque catégorie
        $categoriesData = [];
        
        foreach ($budgetsData as $budget) {
            foreach ($budget->categoriesBudget as $categorieBudget) {
                if (!empty($categories) && !in_array($categorieBudget->categorie_id, $categories)) {
                    continue;
                }
                
                $categoryId = $categorieBudget->categorie_id;
                
                // Filtrer les dépenses pour cette catégorie et cette période
                $categoryExpenses = $depensesData->filter(function ($expense) use ($categorieBudget) {
                    return $expense->categorie_budget_id === $categorieBudget->id;
                });
                
                $totalDepenses = $categoryExpenses->sum('montant');
                
                // Ajouter ou mettre à jour les données de la catégorie
                if (isset($categoriesData[$categoryId])) {
                    $categoriesData[$categoryId]['montant_alloue'] += $categorieBudget->montant_alloue;
                    $categoriesData[$categoryId]['total_depenses'] += $totalDepenses;
                } else {
                    $categoriesData[$categoryId] = [
                        'id' => $categoryId,
                        'nom' => $categorieBudget->categorie->nom,
                        'montant_alloue' => $categorieBudget->montant_alloue,
                        'total_depenses' => $totalDepenses,
                    ];
                }
            }
        }
        
        // Calculer les pourcentages et montants restants
        foreach ($categoriesData as &$category) {
            $category['montant_restant'] = $category['montant_alloue'] - $category['total_depenses'];
            $category['pourcentage_utilise'] = ($category['montant_alloue'] > 0) 
                ? ($category['total_depenses'] / $category['montant_alloue']) * 100 
                : 0;
        }
        
        // Calculer les totaux globaux
        $totalAlloue = array_sum(array_column($categoriesData, 'montant_alloue'));
        $totalDepenses = array_sum(array_column($categoriesData, 'total_depenses'));
        $montantRestant = $totalAlloue - $totalDepenses;
        $pourcentageUtilise = ($totalAlloue > 0) ? ($totalDepenses / $totalAlloue) * 100 : 0;
        
        // Structurer le rapport
        $report = [
            'periode' => [
                'debut' => $dateDebut->format('Y-m-d'),
                'fin' => $dateFin->format('Y-m-d'),
                'type' => $typeRapport,
            ],
            'totaux' => [
                'alloue' => $totalAlloue,
                'depenses' => $totalDepenses,
                'restant' => $montantRestant,
                'pourcentage_utilise' => $pourcentageUtilise,
            ],
            'categories' => array_values($categoriesData),
            'depenses' => $depensesData,
            'budgets' => $budgetsData,
        ];
        
        return $report;
    }
    
    /**
     * Récupère un rapport sauvegardé.
     * Note: Cette méthode nécessiterait une implémentation de stockage des rapports.
     *
     * @param int $reportId
     * @return array|null
     */
    public function getReport(int $reportId)
    {
        // Cette méthode serait implémentée si vous stockez les rapports générés
        return null;
    }
}