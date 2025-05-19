@extends('layouts.user')

@section('header-title', 'Modifier un compte')

@section('content')
<div class="mb-6">
    <div class="flex items-center">
        <a href="{{ route('accounts.show', $account->id) }}" class="text-primary-600 hover:text-primary-900 mr-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-xl font-bold text-gray-900">Modifier le compte « {{ $account->nom }} »</h2>
    </div>
    <p class="mt-1 text-sm text-gray-500">Mettez à jour les informations de votre compte</p>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6">
        <form method="POST" action="{{ route('accounts.update', $account->id) }}">
            @csrf
            @method('PUT')
            
            <!-- Informations du compte -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Informations du compte</h3>
                    <p class="mt-1 text-sm text-gray-500">Renseignez les détails de votre compte.</p>
                </div>
                
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                    <!-- Nom du compte -->
                    <div class="sm:col-span-3">
                        <label for="nom" class="block text-sm font-medium text-gray-700">Nom du compte<span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <input type="text" name="nom" id="nom" value="{{ old('nom', $account->nom) }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('nom') border-red-300 @enderror" required>
                        </div>
                        @error('nom')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Ex: Compte courant, Épargne, etc.</p>
                    </div>
                    
                    <!-- Type de compte -->
                    <div class="sm:col-span-3">
                        <label for="type" class="block text-sm font-medium text-gray-700">Type de compte<span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <select id="type" name="type" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('type') border-red-300 @enderror" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="courant" {{ old('type', $account->type) == 'courant' ? 'selected' : '' }}>Compte courant</option>
                                <option value="epargne" {{ old('type', $account->type) == 'epargne' ? 'selected' : '' }}>Compte d'épargne</option>
                                <option value="carte_credit" {{ old('type', $account->type) == 'carte_credit' ? 'selected' : '' }}>Carte de crédit</option>
                                <option value="especes" {{ old('type', $account->type) == 'especes' ? 'selected' : '' }}>Espèces</option>
                                <option value="investissement" {{ old('type', $account->type) == 'investissement' ? 'selected' : '' }}>Compte d'investissement</option>
                                <option value="autre" {{ old('type', $account->type) == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Solde actuel (en lecture seule) -->
                    <div class="sm:col-span-3">
                        <label class="block text-sm font-medium text-gray-700">Solde actuel</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">{{ $account->devise }}</span>
                            </div>
                            <input type="text" value="{{ number_format($account->solde, 2) }}" class="bg-gray-50 focus:ring-primary-500 focus:border-primary-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md" readonly>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Le solde réel en fonction des transactions enregistrées</p>
                    </div>
                    
                    <!-- Devise -->
                    <div class="sm:col-span-3">
                        <label for="devise" class="block text-sm font-medium text-gray-700">Devise<span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <select id="devise" name="devise" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('devise') border-red-300 @enderror" required>
                                <option value="MAD" {{ old('devise', $account->devise) == 'MAD' ? 'selected' : '' }}>Dirham marocain (MAD)</option>
                                <option value="EUR" {{ old('devise', $account->devise) == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                <option value="USD" {{ old('devise', $account->devise) == 'USD' ? 'selected' : '' }}>Dollar américain (USD)</option>
                                <option value="GBP" {{ old('devise', $account->devise) == 'GBP' ? 'selected' : '' }}>Livre sterling (GBP)</option>
                            </select>
                        </div>
                        @error('devise')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Institution financière -->
                    <div class="sm:col-span-6">
                        <label for="institution" class="block text-sm font-medium text-gray-700">Institution financière</label>
                        <div class="mt-1">
                            <input type="text" name="institution" id="institution" value="{{ old('institution', $account->institution) }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('institution') border-red-300 @enderror">
                        </div>
                        @error('institution')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Ex: Attijariwafa Bank, BMCE, etc.</p>
                    </div>
                    
                    <!-- Numéro de compte (masqué) -->
                    <div class="sm:col-span-6">
                        <label for="numero_compte" class="block text-sm font-medium text-gray-700">Numéro de compte (facultatif)</label>
                        <div class="mt-1">
                            <input type="text" name="numero_compte" id="numero_compte" value="{{ old('numero_compte', $account->numero_compte) }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('numero_compte') border-red-300 @enderror">
                        </div>
                        @error('numero_compte')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Pour votre information uniquement. Exemple: XXXX XXXX XXXX 1234</p>
                    </div>
                    
                    <!-- Notes -->
                    <div class="sm:col-span-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <div class="mt-1">
                            <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('notes') border-red-300 @enderror">{{ old('notes', $account->notes) }}</textarea>
                        </div>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">Toute information supplémentaire sur ce compte.</p>
                    </div>
                </div>
                
                <!-- Options supplémentaires -->
                <div class="pt-6 border-t border-gray-200">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Options supplémentaires</h3>
                        <p class="mt-1 text-sm text-gray-500">Configurez des options supplémentaires pour ce compte.</p>
                    </div>
                    
                    <div class="mt-4 space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="exclure_du_total" name="exclure_du_total" type="checkbox" value="1" {{ old('exclure_du_total', $account->exclure_du_total) ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="exclure_du_total" class="font-medium text-gray-700">Exclure du solde total</label>
                                <p class="text-gray-500">Ce compte sera exclu du calcul du solde total sur le tableau de bord.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="actif" name="actif" type="checkbox" value="1" {{ old('actif', $account->actif) ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="actif" class="font-medium text-gray-700">Compte actif</label>
                                <p class="text-gray-500">Décochez cette case pour archiver le compte.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="couleur_personnalisee" name="couleur_personnalisee" type="checkbox" value="1" {{ old('couleur_personnalisee', !is_null($account->couleur)) ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="couleur_personnalisee" class="font-medium text-gray-700">Couleur personnalisée</label>
                                <div class="mt-1 flex items-center">
                                    <input type="color" name="couleur" id="couleur" value="{{ old('couleur', $account->couleur ?? '#4F46E5') }}" class="h-8 w-8 rounded-full border border-gray-300">
                                    <span class="ml-2 text-gray-500">Choisissez une couleur pour identifier ce compte.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ajustement du solde -->
                <div class="pt-6 border-t border-gray-200">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Ajustement du solde</h3>
                        <p class="mt-1 text-sm text-gray-500">Utilisez cette option si vous avez besoin d'ajuster le solde du compte manuellement.</p>
                    </div>
                    
                    <div class="mt-4 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="ajustement_type" class="block text-sm font-medium text-gray-700">Type d'ajustement</label>
                            <div class="mt-1">
                                <select id="ajustement_type" name="ajustement_type" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                    <option value="">Aucun ajustement</option>
                                    <option value="nouveau_solde">Définir un nouveau solde</option>
                                    <option value="ajouter">Ajouter au solde actuel</option>
                                    <option value="soustraire">Soustraire du solde actuel</option>
                                </select>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Type d'ajustement à effectuer</p>
                        </div>
                        
                        <div class="sm:col-span-3">
                            <label for="ajustement_montant" class="block text-sm font-medium text-gray-700">Montant de l'ajustement</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">{{ $account->devise }}</span>
                                </div>
                                <input type="number" step="0.01" name="ajustement_montant" id="ajustement_montant" value="{{ old('ajustement_montant') }}" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md" placeholder="0.00">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Laissez vide ou à 0 pour ne pas ajuster</p>
                        </div>
                        
                        <div class="sm:col-span-6">
                            <label for="ajustement_raison" class="block text-sm font-medium text-gray-700">Raison de l'ajustement</label>
                            <div class="mt-1">
                                <input type="text" name="ajustement_raison" id="ajustement_raison" value="{{ old('ajustement_raison') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Ex: Correction d'erreur, Réconciliation bancaire, etc.">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Une description de la raison de cet ajustement pour votre suivi</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Boutons d'action -->
            <div class="pt-6 mt-6 border-t border-gray-200 flex items-center justify-between">
                <button type="button" class="text-red-600 hover:text-red-800 font-medium" onclick="document.getElementById('delete-form').submit();">
                    Supprimer ce compte
                </button>
                
                <div class="flex space-x-3">
                    <a href="{{ route('accounts.show', $account->id) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Annuler
                    </a>
                    <button type="submit" class="bg-primary-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Formulaire de suppression -->
        <form id="delete-form" method="POST" action="{{ route('accounts.destroy', $account->id) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<!-- Avertissement pour l'ajustement du solde -->
<div class="mt-6 bg-yellow-50 rounded-lg border border-yellow-200 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-yellow-800">Attention lors de l'ajustement du solde</h3>
            <div class="mt-2 text-sm text-yellow-700">
                <p>L'ajustement du solde est une opération qui modifie directement le solde de votre compte sans passer par une transaction. Utilisez cette fonction uniquement dans les cas suivants :</p>
                <ul class="list-disc space-y-1 pl-5 mt-2">
                    <li>Correction d'erreurs d'enregistrement</li>
                    <li>Réconciliation avec votre relevé bancaire</li>
                    <li>Prise en compte d'opérations que vous ne souhaitez pas suivre individuellement</li>
                </ul>
                <p class="mt-2">Un enregistrement de cet ajustement sera conservé dans l'historique du compte.</p>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation de suppression -->
<div id="delete-confirmation" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Supprimer le compte</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Êtes-vous sûr de vouloir supprimer ce compte ? Cette action est irréversible et toutes les transactions associées seront également supprimées.
                </p>
            </div>
            <div class="flex justify-center gap-4 mt-4">
                <button id="cancel-btn" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Annuler
                </button>
                <button id="confirm-btn" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script pour la confirmation de suppression
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBtn = document.querySelector('.text-red-600');
        const deleteForm = document.getElementById('delete-form');
        const deleteConfirmation = document.getElementById('delete-confirmation');
        const cancelBtn = document.getElementById('cancel-btn');
        const confirmBtn = document.getElementById('confirm-btn');
        
        deleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            deleteConfirmation.classList.remove('hidden');
        });
        
        cancelBtn.addEventListener('click', function() {
            deleteConfirmation.classList.add('hidden');
        });
        
        confirmBtn.addEventListener('click', function() {
            deleteForm.submit();
        });
    });
</script>
@endpush