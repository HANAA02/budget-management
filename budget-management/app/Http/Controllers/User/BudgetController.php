<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\BudgetRequest;
use App\Models\Budget;
use App\Models\Category;
use App\Services\BudgetAllocationService;
use App\Services\BudgetCalculationService;
use App\Services\ChartService;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    protected $allocationService;
    protected $calculationService;
    protected $chartService;

    public function __construct(
        BudgetAllocationService $allocationService,
        BudgetCalculationService $calculationService,
        ChartService $chartService
    ) {
        $this->allocationService = $allocationService;
        $this->calculationService = $calculationService;
        $this->chartService = $chartService;
    }

    /**
     * Affiche la liste des budgets de l'utilisateur.
     */
    public function index()
    {
        $budgets = auth()->user()->budgets()->orderBy('date_debut', 'desc')->paginate(10);
        return view('user.budgets.index', compact('budgets'));
    }

    /**
     * Affiche le formulaire de création d'un budget.
     */
    public function create()
    {
        $categories = Category::all();
        $revenus = auth()->user()->revenus;
        $montantDisponible = $this->calculationService->calculateAvailableIncome(auth()->user());
        
        return view('user.budgets.create', compact('categories', 'revenus', 'montantDisponible'));
    }

    /**
     * Enregistre un nouveau budget.
     */
    public function store(BudgetRequest $request)
    {
        // Créer le budget
        $budget = Budget::create([
            'utilisateur_id' => auth()->id(),
            'nom' => $request->nom,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'montant_total' => $request->montant_total,
        ]);

        // Créer la répartition initiale des catégories
        $this->allocationService->createInitialAllocation($budget);

        return redirect()->route('budgets.show', $budget)
            ->with('success', 'Budget créé avec succès. Vous pouvez maintenant personnaliser la répartition.');
    }

    /**
     * Affiche les détails d'un budget.
     */
    public function show(Budget $budget)
    {
        $this->authorize('view', $budget);

        // Récupérer les données du budget
        $categoriesBudget = $budget->categoriesBudget()->with('categorie', 'depenses')->get();
        
        // Calculer les totaux pour chaque catégorie
        $categoriesData = $this->calculationService->calculateCategoryTotals($categoriesBudget);
        
        // Générer les données pour les graphiques
        $pieChartData = $this->chartService->generateBudgetPieChart($categoriesData);
        $barChartData = $this->chartService->generateExpenseBarChart($categoriesData);
        
        return view('user.budgets.show', compact(
            'budget', 
            'categoriesData', 
            'pieChartData', 
            'barChartData'
        ));
    }

    /**
     * Affiche le formulaire de modification d'un budget.
     */
    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        
        return view('user.budgets.edit', compact('budget'));
    }

    /**
     * Met à jour un budget.
     */
    public function update(BudgetRequest $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $budget->update($request->validated());
        
        return redirect()->route('budgets.show', $budget)
            ->with('success', 'Budget mis à jour avec succès.');
    }

    /**
     * Supprime un budget.
     */
    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        
        $budget->delete();
        
        return redirect()->route('budgets.index')
            ->with('success', 'Budget supprimé avec succès.');
    }

    /**
     * Affiche la page de répartition du budget.
     */
    public function showAllocation(Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $categoriesBudget = $budget->categoriesBudget()->with('categorie')->get();
        $categories = Category::all();
        
        return view('user.budgets.allocate', compact('budget', 'categoriesBudget', 'categories'));
    }

    /**
     * Enregistre la répartition du budget.
     */
    public function saveAllocation(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $this->allocationService->updateAllocation($budget, $request->all());
        
        return redirect()->route('budgets.show', $budget)
            ->with('success', 'Répartition du budget mise à jour avec succès.');
    }
}