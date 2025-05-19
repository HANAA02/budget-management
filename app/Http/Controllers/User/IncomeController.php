<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\IncomeRequest;
use App\Models\Account;
use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $revenus = auth()->user()->revenus()->with('compte')->orderBy('date_perception', 'desc')->paginate(10);
        return view('user.incomes.index', compact('revenus'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $comptes = auth()->user()->comptes;
        return view('user.incomes.create', compact('comptes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\User\IncomeRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(IncomeRequest $request)
    {
        // Vérifier que l'utilisateur a accès au compte
        $compte = Account::findOrFail($request->compte_id);
        $this->authorize('update', $compte);
        
        $revenu = Income::create([
            'utilisateur_id' => auth()->id(),
            'compte_id' => $request->compte_id,
            'source' => $request->source,
            'montant' => $request->montant,
            'date_perception' => $request->date_perception,
            'periodicite' => $request->periodicite,
        ]);
        
        // Mettre à jour le solde du compte
        $compte->update([
            'solde' => $compte->solde + $request->montant,
        ]);
        
        return redirect()->route('incomes.index')
            ->with('success', 'Revenu ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\View\View
     */
    public function show(Income $income)
    {
        $this->authorize('view', $income);
        
        return view('user.incomes.show', compact('income'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\View\View
     */
    public function edit(Income $income)
    {
        $this->authorize('update', $income);
        
        $comptes = auth()->user()->comptes;
        
        return view('user.incomes.edit', compact('income', 'comptes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\IncomeRequest  $request
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(IncomeRequest $request, Income $income)
    {
        $this->authorize('update', $income);
        
        // Récupérer l'ancien montant et le compte
        $oldAmount = $income->montant;
        $oldAccount = $income->compte;
        
        // Mettre à jour le revenu
        $income->update($request->validated());
        
        // Si le compte a changé, mettre à jour les soldes des deux comptes
        if ($request->compte_id != $income->compte_id) {
            // Soustraire le montant de l'ancien compte
            $oldAccount->update([
                'solde' => $oldAccount->solde - $oldAmount,
            ]);
            
            // Ajouter le montant au nouveau compte
            $newAccount = Account::findOrFail($request->compte_id);
            $newAccount->update([
                'solde' => $newAccount->solde + $request->montant,
            ]);
        } else {
            // Mettre à jour le solde du compte actuel
            $oldAccount->update([
                'solde' => $oldAccount->solde - $oldAmount + $request->montant,
            ]);
        }
        
        return redirect()->route('incomes.index')
            ->with('success', 'Revenu mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Income $income)
    {
        $this->authorize('delete', $income);
        
        // Soustraire le montant du compte
        $compte = $income->compte;
        $compte->update([
            'solde' => $compte->solde - $income->montant,
        ]);
        
        $income->delete();
        
        return redirect()->route('incomes.index')
            ->with('success', 'Revenu supprimé avec succès.');
    }
}