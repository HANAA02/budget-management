<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ExpenseRequest;
use App\Models\Budget;
use App\Models\CategoryBudget;
use App\Models\Expense;
use App\Services\AlertService;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    protected $alertService;

    public function __construct(AlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Affiche la liste des dépenses.
     */
    public function index(Request $request)
    {
        $query = Expense::query()
            ->whereHas('categorieBudget', function ($q) {
                $q->whereHas('budget', function ($q) {
                    $q->where('utilisateur_id', auth()->id());
                });
            })
            ->with(['categorieBudget.categorie', 'categorieBudget.budget']);

        // Filtres
        if ($request->filled('date_debut')) {
            $query->where('date_depense', '>=', $request->date_debut);
        }
        
        if ($request->filled('date_fin')) {
            $query->where('date_depense', '<=', $request->date_fin);
        }
        
        if ($request->filled('categorie')) {
            $query->whereHas('categorieBudget', function ($q) use ($request) {
                $q->where('categorie_id', $request->categorie);
            });
        }
        
        if ($request->filled('budget')) {
            $query->whereHas('categorieBudget', function ($q) use ($request) {
                $q->where('budget_id', $request->budget);
            });
        }

        $depenses = $query->orderBy('date_depense', 'desc')->paginate(15);
        $budgets = auth()->user()->budgets;
        $categories = \App\Models\Category::all();

        return view('user.expenses.index', compact('depenses', 'budgets', 'categories'));
    }

    /**
     * Affiche le formulaire de création d'une dépense.
     */
    public function create()
    {
        $budgets = auth()->user()->budgets()
            ->where('date_fin', '>=', now())
            ->with('categoriesBudget.categorie')
            ->get();

        return view('user.expenses.create', compact('budgets'));
    }

    /**
     * Enregistre une nouvelle dépense.
     */
    public function store(ExpenseRequest $request)
    {
        $categorieBudget = CategoryBudget::findOrFail($request->categorie_budget_id);
        
        // Vérifier que l'utilisateur a accès à cette catégorie de budget
        $this->authorize('createExpense', $categorieBudget);

        $depense = Expense::create([
            'categorie_budget_id' => $request->categorie_budget_id,
            'description' => $request->description,
            'montant' => $request->montant,
            'date_depense' => $request->date_depense,
            'statut' => $request->statut ?? 'validée',
        ]);

        // Vérifier si des alertes doivent être déclenchées
        $this->alertService->checkBudgetAlerts($categorieBudget);

        return redirect()->route('expenses.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }

    /**
     * Affiche les détails d'une dépense.
     */
    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        
        return view('user.expenses.show', compact('expense'));
    }

    /**
     * Affiche le formulaire de modification d'une dépense.
     */
    public function edit(Expense $expense)
    {
        $this->authorize('update', $expense);
        
        $budgets = auth()->user()->budgets()->with('categoriesBudget.categorie')->get();
        
        return view('user.expenses.edit', compact('expense', 'budgets'));
    }

    /**
     * Met à jour une dépense.
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        $this->authorize('update', $expense);
        
        // Si la catégorie a changé, vérifier l'autorisation
        if ($request->categorie_budget_id != $expense->categorie_budget_id) {
            $categorieBudget = CategoryBudget::findOrFail($request->categorie_budget_id);
            $this->authorize('createExpense', $categorieBudget);
        }

        $expense->update($request->validated());
        
        return redirect()->route('expenses.index')
            ->with('success', 'Dépense mise à jour avec succès.');
    }

    /**
     * Supprime une dépense.
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        
        $expense->delete();
        
        return redirect()->route('expenses.index')
            ->with('success', 'Dépense supprimée avec succès.');
    }

    /**
     * Ajoute rapidement une dépense.
     */
    public function quickAdd(Request $request)
    {
        $request->validate([
            'categorie_budget_id' => 'required|exists:categorie_budget,id',
            'montant' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
        ]);

        $categorieBudget = CategoryBudget::findOrFail($request->categorie_budget_id);
        
        // Vérifier que l'utilisateur a accès à cette catégorie de budget
        $this->authorize('createExpense', $categorieBudget);

        $depense = Expense::create([
            'categorie_budget_id' => $request->categorie_budget_id,
            'description' => $request->description,
            'montant' => $request->montant,
            'date_depense' => now(),
            'statut' => 'validée',
        ]);

        // Vérifier si des alertes doivent être déclenchées
        $this->alertService->checkBudgetAlerts($categorieBudget);

        return redirect()->back()->with('success', 'Dépense rapide ajoutée avec succès.');
    }
}