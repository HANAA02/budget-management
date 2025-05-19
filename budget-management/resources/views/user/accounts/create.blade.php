@extends('layouts.user')

@section('header-title', 'Créer un compte')

@section('content')
<div class="mb-6">
    <div class="flex items-center">
        <a href="{{ route('accounts.index') }}" class="text-primary-600 hover:text-primary-900 mr-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-xl font-bold text-gray-900">Ajouter un nouveau compte</h2>
    </div>
    <p class="mt-1 text-sm text-gray-500">Créez un compte pour suivre vos revenus et dépenses</p>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="p-6">
        <form method="POST" action="{{ route('accounts.store') }}">
            @csrf
            
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
                            <input type="text" name="nom" id="nom" value="{{ old('nom') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('nom') border-red-300 @enderror" required>
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
                                <option value="courant" {{ old('type') == 'courant' ? 'selected' : '' }}>Compte courant</option>
                                <option value="epargne" {{ old('type') == 'epargne' ? 'selected' : '' }}>Compte d'épargne</option>
                                <option value="carte_credit" {{ old('type') == 'carte_credit' ? 'selected' : '' }}>Carte de crédit</option>
                                <option value="especes" {{ old('type') == 'especes' ? 'selected' : '' }}>Espèces</option>
                                <option value="investissement" {{ old('type') == 'investissement' ? 'selected' : '' }}>Compte d'investissement</option>
                                <option value="autre" {{ old('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Solde initial -->
                    <div class="sm:col-span-3">
                        <label for="solde" class="block text-sm font-medium text-gray-700">Solde initial<span class="text-red-600">*</span></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">MAD</span>
                            </div>
                            <input type="number" step="0.01" name="solde" id="solde" value="{{ old('solde', '0.00') }}" class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-12 sm:text-sm border-gray-300 rounded-md @error('solde') border-red-300 @enderror" placeholder="0.00" required>
                        </div>
                        @error('solde')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Devise -->
                    <div class="sm:col-span-3">
                        <label for="devise" class="block text-sm font-medium text-gray-700">Devise<span class="text-red-600">*</span></label>
                        <div class="mt-1">
                            <select id="devise" name="devise" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('devise') border-red-300 @enderror" required>
                                <option value="MAD" {{ old('devise') == 'MAD' ? 'selected' : '' }}>Dirham marocain (MAD)</option>
                                <option value="EUR" {{ old('devise') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                <option value="USD" {{ old('devise') == 'USD' ? 'selected' : '' }}>Dollar américain (USD)</option>
                                <option value="GBP" {{ old('devise') == 'GBP' ? 'selected' : '' }}>Livre sterling (GBP)</option>
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
                            <input type="text" name="institution" id="institution" value="{{ old('institution') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('institution') border-red-300 @enderror">
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
                            <input type="text" name="numero_compte" id="numero_compte" value="{{ old('numero_compte') }}" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('numero_compte') border-red-300 @enderror">
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
                            <textarea id="notes" name="notes" rows="3" class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
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
                                <input id="exclure_du_total" name="exclure_du_total" type="checkbox" value="1" {{ old('exclure_du_total') ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="exclure_du_total" class="font-medium text-gray-700">Exclure du solde total</label>
                                <p class="text-gray-500">Ce compte sera exclu du calcul du solde total sur le tableau de bord.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="actif" name="actif" type="checkbox" value="1" {{ old('actif', '1') ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="actif" class="font-medium text-gray-700">Compte actif</label>
                                <p class="text-gray-500">Décochez cette case pour archiver le compte.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="couleur_personnalisee" name="couleur_personnalisee" type="checkbox" value="1" {{ old('couleur_personnalisee') ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="couleur_personnalisee" class="font-medium text-gray-700">Couleur personnalisée</label>
                                <div class="mt-1 flex items-center">
                                    <input type="color" name="couleur" id="couleur" value="{{ old('couleur', '#4F46E5') }}" class="h-8 w-8 rounded-full border border-gray-300">
                                    <span class="ml-2 text-gray-500">Choisissez une couleur pour identifier ce compte.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Boutons d'action -->
            <div class="pt-6 mt-6 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('accounts.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Annuler
                </a>
                <button type="submit" class="bg-primary-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Créer le compte
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Conseils utiles -->
<div class="mt-6 bg-blue-50 rounded-lg border border-blue-200 p-4">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fas fa-info-circle text-blue-600"></i>
        </div>
        <div class="ml-3">
            <h3 class="text-sm font-medium text-blue-800">Conseils utiles</h3>
            <div class="mt-2 text-sm text-blue-700">
                <ul class="list-disc space-y-1 pl-5">
                    <li>Créez un compte pour chaque source de fonds que vous souhaitez suivre (compte bancaire, espèces, etc.).</li>
                    <li>Le solde initial doit refléter le montant actuellement disponible dans ce compte.</li>
                    <li>Pour suivre une carte de crédit, vous pouvez entrer un solde négatif correspondant à votre dette actuelle.</li>
                    <li>Utilisez l'option "Exclure du solde total" pour les comptes d'épargne à long terme ou autres comptes que vous ne souhaitez pas inclure dans votre budget mensuel.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection