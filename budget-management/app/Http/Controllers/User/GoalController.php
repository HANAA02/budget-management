<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\GoalRequest;
use App\Models\Category;
use App\Models\Goal;
use App\Services\ExpenseAnalysisService;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    protected $expenseAnalysisService;

    public function __construct(ExpenseAnalysisService $expenseAnalysisService)
    {
        $this->expenseAnalysisService = $expenseAnalysisService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $objectifs = auth()->user()->objectifs()->with('categorie')->get();
        
        // Calculer la progression pour chaque objectif
        foreach ($objectifs as $objectif) {
            $objectif->progression = $this->expenseAnalysisService->calculateGoalProgress($objectif);
        }
        
        return view('user.goals.index', compact('objectifs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::all();
        return view('user.goals.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\User\GoalRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GoalRequest $request)
    {
        $objectif = auth()->user()->objectifs()->create($request->validated());
        
        return redirect()->route('goals.index')
            ->with('success', 'Objectif créé avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\View\View
     */
    public function show(Goal $goal)
    {
        $this->authorize('view', $goal);
        
        // Calculer la progression de l'objectif
        $progression = $this->expenseAnalysisService->calculateGoalProgress($goal);
        
        // Récupérer l'historique des dépenses liées à cet objectif
        $depenses = $this->expenseAnalysisService->getExpensesForGoal($goal);
        
        // Générer les données pour le graphique d'évolution
        $progressionData = $this->expenseAnalysisService->getGoalProgressData($goal);
        
        return view('user.goals.show', compact('goal', 'progression', 'depenses', 'progressionData'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\View\View
     */
    public function edit(Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $categories = Category::all();
        
        return view('user.goals.edit', compact('goal', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\GoalRequest  $request
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(GoalRequest $request, Goal $goal)
    {
        $this->authorize('update', $goal);
        
        $goal->update($request->validated());
        
        return redirect()->route('goals.index')
            ->with('success', 'Objectif mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Goal $goal)
    {
        $this->authorize('delete', $goal);
        
        $goal->delete();
        
        return redirect()->route('goals.index')
            ->with('success', 'Objectif supprimé avec succès.');
    }
    
    /**
     * Display the progress of a goal.
     *
     * @param  \App\Models\Goal  $goal
     * @return \Illuminate\View\View
     */
    public function progress(Goal $goal)
    {
        $this->authorize('view', $goal);
        
        // Calculer la progression de l'objectif
        $progression = $this->expenseAnalysisService->calculateGoalProgress($goal);
        
        // Générer les données pour le graphique d'évolution
        $progressionData = $this->expenseAnalysisService->getGoalProgressData($goal);
        
        return view('user.goals.progress', compact('goal', 'progression', 'progressionData'));
    }
}