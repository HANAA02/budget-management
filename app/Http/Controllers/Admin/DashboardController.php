<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord administrateur.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Statistiques des utilisateurs
        $totalUsers = User::where('role', 'user')->count();
        $newUsers = User::where('role', 'user')
            ->where('date_creation', '>=', Carbon::now()->subDays(30))
            ->count();
        
        // Statistiques des budgets
        $totalBudgets = Budget::count();
        $activeBudgets = Budget::where('date_debut', '<=', Carbon::now())
            ->where('date_fin', '>=', Carbon::now())
            ->count();
        
        // Statistiques des dépenses
        $totalExpenses = Expense::sum('montant');
        $monthlyExpenses = Expense::where('date_depense', '>=', Carbon::now()->startOfMonth())
            ->where('date_depense', '<=', Carbon::now()->endOfMonth())
            ->sum('montant');
        
        // Statistiques des catégories les plus utilisées
        $topCategories = Category::select('categories.nom', DB::raw('SUM(depenses.montant) as total'))
            ->join('categorie_budget', 'categories.id', '=', 'categorie_budget.categorie_id')
            ->join('depenses', 'categorie_budget.id', '=', 'depenses.categorie_budget_id')
            ->groupBy('categories.id', 'categories.nom')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        // Utilisateurs récemment inscrits
        $recentUsers = User::where('role', 'user')
            ->orderBy('date_creation', 'desc')
            ->limit(5)
            ->get();
        
        // Activité récente (dépenses)
        $recentActivity = Expense::with(['categorieBudget.categorie', 'categorieBudget.budget.utilisateur'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.dashboard', compact(
            'totalUsers', 
            'newUsers', 
            'totalBudgets', 
            'activeBudgets', 
            'totalExpenses', 
            'monthlyExpenses', 
            'topCategories', 
            'recentUsers', 
            'recentActivity'
        ));
    }
}