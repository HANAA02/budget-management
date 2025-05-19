<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = auth()->user();
        return view('user.profile.edit', compact('user'));
    }

    /**
     * Update the user's profile.
     *
     * @param  \App\Http\Requests\User\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        
        $userData = $request->validated();
        
        // Ne mettre à jour le mot de passe que s'il est fourni
        if (!empty($userData['password']) && !empty($userData['current_password'])) {
            // Vérifier le mot de passe actuel
            if (!Hash::check($userData['current_password'], $user->mot_de_passe)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
            
            $user->mot_de_passe = Hash::make($userData['password']);
        }
        
        $user->nom = $userData['nom'];
        $user->prenom = $userData['prenom'];
        $user->email = $userData['email'];
        $user->save();
        
        return redirect()->route('profile.edit')
            ->with('success', 'Profil mis à jour avec succès.');
    }
}