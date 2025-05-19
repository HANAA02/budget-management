<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\BudgetCalculationService;
use App\Services\ChartService;
use App\Services\ExpenseAnalysisService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $budgetCalculationService;
    protected $expenseAnalysisService;
    protected $chartService;

    public function __construct(
        BudgetCalculationService $budgetCalculationService,
        ExpenseAnalysisService $expenseAnalysisService,
        ChartService $chartService
    ) {
        $this->budgetCalculationService = $budgetCalculationService;
        $this->expenseAnalysisService = $expenseAnalysisService;
        $this->chartService = $chartService;
    }

    /**
     * Affiche le tableau de bord de l'utilisateur.
     */
    public function index()
    {
        $user = auth()->user();
        
        // Récupérer le budget actif
        $budgetActif = $user->budgets()
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now())
            ->orderBy('date_creation', 'desc')
            ->first();
            
        // Récupérer les données du budget si disponible
        $budgetData = null;
        $pieChartData = null;
        $depensesRecentes = null;
        
        if ($budgetActif) {
            // Récupérer les catégories du budget avec leurs dépenses
            $categoriesBudget = $budgetActif->categoriesBudget()->with('categorie', 'depenses')->get();
            
            // Calculer les totaux pour chaque catégorie
            $budgetData = $this->budgetCalculationService->calculateCategoryTotals($categoriesBudget);
            
            // Générer les données pour le graphique camembert
            $pieChartData = $this->chartService->generateBudgetPieChart($budgetData);
            
            // Récupérer les dépenses récentes
            $depensesRecentes = $this->expenseAnalysisService->getRecentExpenses($user, 5);
        }
        
        // Récupérer les objectifs en cours
        $objectifs = $user->objectifs()
            ->where('date_fin', '>=', now())
            ->where('statut', 'en cours')
            ->with('categorie')
            ->limit(3)
            ->get();
            
        // Calculer la progression des objectifs
        foreach ($objectifs as $objectif) {
            $objectif->progression = $this->expenseAnalysisService->calculateGoalProgress($objectif);
        }
        
        // Récupérer les alertes récentes
        $alertes = $this->expenseAnalysisService->getRecentAlerts($user, 5);
        
        // Récupérer les soldes des comptes
        $comptes = $user->comptes;
        $soldeTotal = $comptes->sum('solde');
        
        // Graphique d'évolution des dépenses sur les 6 derniers mois
        $depensesMensuelles = $this->expenseAnalysisService->getMonthlyExpenses($user, 6);
        $lineChartData = $this->chartService->generateMonthlyExpenseChart($depensesMensuelles);
        
        return view('user.dashboard', compact(
            'budgetActif',
            'budgetData',
            'pieChartData',
            'depensesRecentes',
            'objectifs',
            'alertes',
            'comptes',
            'soldeTotal',
            'lineChartData'
        ));
    }
}