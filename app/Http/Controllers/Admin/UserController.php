<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::orderBy('nom')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Affiche le formulaire de création d'un utilisateur.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistre un nouvel utilisateur.
     *
     * @param  \App\Http\Requests\Admin\UserRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        $userData = $request->validated();
        $userData['mot_de_passe'] = Hash::make($userData['mot_de_passe']);
        
        $user = User::create($userData);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Affiche les détails d'un utilisateur.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // Récupérer les statistiques de l'utilisateur
        $totalBudgets = $user->budgets()->count();
        $totalExpenses = 0;
        
        foreach ($user->budgets as $budget) {
            foreach ($budget->categoriesBudget as $categoryBudget) {
                $totalExpenses += $categoryBudget->depenses()->sum('montant');
            }
        }
        
        $totalAccounts = $user->comptes()->count();
        $totalIncomes = $user->revenus()->sum('montant');
        
        return view('admin.users.show', compact(
            'user',
            'totalBudgets',
            'totalExpenses',
            'totalAccounts',
            'totalIncomes'
        ));
    }

    /**
     * Affiche le formulaire de modification d'un utilisateur.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Met à jour un utilisateur.
     *
     * @param  \App\Http\Requests\Admin\UserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, User $user)
    {
        $userData = $request->validated();
        
        // Ne mettre à jour le mot de passe que s'il est fourni
        if (!empty($userData['mot_de_passe'])) {
            $userData['mot_de_passe'] = Hash::make($userData['mot_de_passe']);
        } else {
            unset($userData['mot_de_passe']);
        }
        
        $user->update($userData);
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprime un utilisateur.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Vérifier qu'on ne supprime pas l'administrateur principal
        if ($user->id === 1) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de supprimer l\'administrateur principal.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
    
    /**
     * Réinitialise le mot de passe d'un utilisateur.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(User $user)
    {
        // Générer un mot de passe aléatoire
        $newPassword = Str::random(10);
        
        // Mettre à jour le mot de passe de l'utilisateur
        $user->update([
            'mot_de_passe' => Hash::make($newPassword),
        ]);
        
        // TODO: Envoyer un e-mail avec le nouveau mot de passe à l'utilisateur
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', "Mot de passe réinitialisé avec succès. Nouveau mot de passe : $newPassword");
    }
}