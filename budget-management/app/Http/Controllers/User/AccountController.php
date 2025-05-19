<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\AccountRequest;
use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $accounts = auth()->user()->comptes()->paginate(10);
        return view('user.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('user.accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\User\AccountRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AccountRequest $request)
    {
        $account = auth()->user()->comptes()->create($request->validated());
        
        return redirect()->route('accounts.index')
            ->with('success', 'Compte créé avec succès.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\View\View
     */
    public function show(Account $account)
    {
        $this->authorize('view', $account);
        
        // Récupérer les revenus associés à ce compte
        $revenus = $account->revenus()->orderBy('date_perception', 'desc')->get();
        
        // Calculer le total des revenus
        $totalRevenus = $revenus->sum('montant');
        
        return view('user.accounts.show', compact('account', 'revenus', 'totalRevenus'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\View\View
     */
    public function edit(Account $account)
    {
        $this->authorize('update', $account);
        
        return view('user.accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\User\AccountRequest  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AccountRequest $request, Account $account)
    {
        $this->authorize('update', $account);
        
        $account->update($request->validated());
        
        return redirect()->route('accounts.index')
            ->with('success', 'Compte mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);
        
        // Vérifier si le compte a des revenus
        if ($account->revenus()->exists()) {
            return redirect()->route('accounts.index')
                ->with('error', 'Impossible de supprimer ce compte car il contient des revenus.');
        }
        
        $account->delete();
        
        return redirect()->route('accounts.index')
            ->with('success', 'Compte supprimé avec succès.');
    }
}