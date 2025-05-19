<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\CategoryBudget;
use Illuminate\Http\Request;

class CategoryBudgetController extends Controller
{
    /**
     * Display the specified category budget details.
     *
     * @param  \App\Models\CategoryBudget  $categoryBudget
     * @return \Illuminate\View\View
     */
    public function show(CategoryBudget $categoryBudget)
    {
        $this->authorize('view', $categoryBudget);
        
        // Récupérer les dépenses associées à cette catégorie de budget
        $depenses = $categoryBudget->depenses()->orderBy('date_depense', 'desc')->get();
        
        // Récupérer les alertes associées à cette catégorie de budget
        $alertes = $categoryBudget->alertes;
        
        // Calculer le total des dépenses
        $totalDepenses = $depenses->sum('montant');
        
        // Calculer le pourcentage utilisé
        $pourcentageUtilise = ($categoryBudget->montant_alloue > 0) 
            ? round(($totalDepenses / $categoryBudget->montant_alloue) * 100, 2) 
            : 0;
        
        // Déterminer la classe CSS pour la barre de progression
        $progressClass = 'bg-success';
        if ($pourcentageUtilise >= 75 && $pourcentageUtilise < 90) {
            $progressClass = 'bg-warning';
        } elseif ($pourcentageUtilise >= 90) {
            $progressClass = 'bg-danger';
        }
        
        return view('user.category_budgets.show', compact(
            'categoryBudget',
            'depenses',
            'alertes',
            'totalDepenses',
            'pourcentageUtilise',
            'progressClass'
        ));
    }
    
    /**
     * Show the form for creating a new expense for this category budget.
     *
     * @param  \App\Models\CategoryBudget  $categoryBudget
     * @return \Illuminate\View\View
     */
    public function createExpense(CategoryBudget $categoryBudget)
    {
        $this->authorize('createExpense', $categoryBudget);
        
        return view('user.expenses.create', [
            'categoryBudget' => $categoryBudget,
            'budget' => $categoryBudget->budget,
            'categorie' => $categoryBudget->categorie,
        ]);
    }
    
    /**
     * Show the form for creating a new alert for this category budget.
     *
     * @param  \App\Models\CategoryBudget  $categoryBudget
     * @return \Illuminate\View\View
     */
    public function createAlert(CategoryBudget $categoryBudget)
    {
        $this->authorize('createAlert', $categoryBudget);
        
        return view('user.alerts.create', [
            'categoryBudget' => $categoryBudget,
            'budget' => $categoryBudget->budget,
            'categorie' => $categoryBudget->categorie,
        ]);
    }
}