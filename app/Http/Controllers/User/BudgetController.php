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
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $budgets = auth()->user()->budgets()->orderBy('date_debut', 'desc')->paginate(10);
        return view('user.budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        $revenus = auth()->user()->revenus;
        $montantDisponible = $this->calculationService->calculateAvailableIncome(auth()->user());
        
        return view('user.budgets.create', compact('categories', 'revenus', 'montantDisponible'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\User\BudgetRequest  $request
     * @return \Illuminate\Http\RedirectResponse
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
     * Display the specified resource.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\View\View
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
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\View\View
     */
    public function edit(Budget $budget)
    {
        $this->authorize('update', $budget);
        
        return view('user.budgets.edit', compact('budget'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\BudgetRequest  $request
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BudgetRequest $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $budget->update($request->validated());
        
        return redirect()->route('budgets.show', $budget)
            ->with('success', 'Budget mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Budget $budget)
    {
        $this->authorize('delete', $budget);
        
        $budget->delete();
        
        return redirect()->route('budgets.index')
            ->with('success', 'Budget supprimé avec succès.');
    }

    /**
     * Show the budget allocation page.
     *
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\View\View
     */
    public function showAllocation(Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $categoriesBudget = $budget->categoriesBudget()->with('categorie')->get();
        $categories = Category::all();
        
        return view('user.budgets.allocate', compact('budget', 'categoriesBudget', 'categories'));
    }

    /**
     * Save the budget allocation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Budget  $budget
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveAllocation(Request $request, Budget $budget)
    {
        $this->authorize('update', $budget);
        
        $this->allocationService->updateAllocation($budget, $request->all());
        
        return redirect()->route('budgets.show', $budget)
            ->with('success', 'Répartition du budget mise à jour avec succès.');
    }
}