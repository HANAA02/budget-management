<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AlertRequest;
use App\Models\Alert;
use App\Models\Budget;
use App\Models\CategoryBudget;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Récupérer les alertes de l'utilisateur
        $alertes = Alert::whereHas('categorieBudget', function ($query) {
            $query->whereHas('budget', function ($query) {
                $query->where('utilisateur_id', auth()->id());
            });
        })->with(['categorieBudget.categorie', 'categorieBudget.budget'])
          ->orderBy('created_at', 'desc')
          ->paginate(10);
        
        return view('user.alerts.index', compact('alertes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Récupérer les budgets actifs de l'utilisateur
        $budgets = Budget::where('utilisateur_id', auth()->id())
            ->where('date_fin', '>=', now())
            ->with('categoriesBudget.categorie')
            ->get();
        
        return view('user.alerts.create', compact('budgets'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\User\AlertRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AlertRequest $request)
    {
        // Vérifier que l'utilisateur a accès à la catégorie de budget
        $categoryBudget = CategoryBudget::findOrFail($request->categorie_budget_id);
        $this->authorize('createAlert', $categoryBudget);
        
        // Créer l'alerte
        $alerte = Alert::create([
            'categorie_budget_id' => $request->categorie_budget_id,
            'type' => $request->type,
            'seuil' => $request->seuil,
            'active' => true,
        ]);
        
        return redirect()->route('alerts.index')
            ->with('success', 'Alerte créée avec succès.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Alert  $alert
     * @return \Illuminate\View\View
     */
    public function edit(Alert $alert)
    {
        $this->authorize('update', $alert);
        
        // Récupérer les budgets actifs de l'utilisateur
        $budgets = Budget::where('utilisateur_id', auth()->id())
            ->where('date_fin', '>=', now())
            ->with('categoriesBudget.categorie')
            ->get();
        
        return view('user.alerts.edit', compact('alert', 'budgets'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\AlertRequest  $request
     * @param  \App\Models\Alert  $alert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AlertRequest $request, Alert $alert)
    {
        $this->authorize('update', $alert);
        
        // Vérifier que l'utilisateur a accès à la catégorie de budget
        if ($request->categorie_budget_id != $alert->categorie_budget_id) {
            $categoryBudget = CategoryBudget::findOrFail($request->categorie_budget_id);
            $this->authorize('createAlert', $categoryBudget);
        }
        
        $alert->update($request->validated());
        
        return redirect()->route('alerts.index')
            ->with('success', 'Alerte mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Alert  $alert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Alert $alert)
    {
        $this->authorize('delete', $alert);
        
        $alert->delete();
        
        return redirect()->route('alerts.index')
            ->with('success', 'Alerte supprimée avec succès.');
    }
    
    /**
     * Toggle the active status of the specified alert.
     *
     * @param  \App\Models\Alert  $alert
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(Alert $alert)
    {
        $this->authorize('update', $alert);
        
        $alert->update([
            'active' => !$alert->active,
        ]);
        
        $status = $alert->active ? 'activée' : 'désactivée';
        
        return redirect()->route('alerts.index')
            ->with('success', "Alerte $status avec succès.");
    }
}