@extends('layouts.admin')

@section('title', 'Modifier un utilisateur')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifier l'utilisateur</h1>
        <div>
            <a href="{{ route('admin.users.show', $user->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm mr-2">
                <i class="fas fa-eye fa-sm text-white-50"></i> Voir les détails
            </a>
            <a href="{{ route('admin.users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informations de l'utilisateur</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Actions:</div>
                    <a class="dropdown-item" href="{{ route('admin.users.show', $user->id) }}">Voir les détails</a>
                    <a class="dropdown-item text-primary" href="{{ route('admin.password.reset', $user->id) }}">Réinitialiser le mot de passe</a>
                    <button class="dropdown-item text-danger" type="button" data-toggle="modal" data-target="#deleteModal">Supprimer</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Informations personnelles -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="font-weight-bold text-primary mb-0">Informations personnelles</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Avatar actuel -->
                            <div class="col-md-12 mb-4 text-center">
                                <div class="avatar-preview mb-3">
                                    @if($user->avatar)
                                        <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar de l'utilisateur" class="img-profile rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                    @else
                                        <div class="avatar-placeholder rounded-circle">
                                            {{ substr($user->nom, 0, 1) . substr($user->prenom, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="custom-file" style="max-width: 300px; margin: 0 auto;">
                                    <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror" id="avatar" name="avatar">
                                    <label class="custom-file-label" for="avatar">Changer l'avatar</label>
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Formats acceptés : JPG, PNG. Taille maximale : 2Mo</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Nom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" 
                                           value="{{ old('nom', $user->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Prénom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="prenom">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenom" name="prenom" 
                                           value="{{ old('prenom', $user->prenom) }}" required>
                                    @error('prenom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                                           value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Téléphone (optionnel) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">Téléphone (optionnel)</label>
                                    <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" 
                                           value="{{ old('telephone', $user->telephone) }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informations de compte -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="font-weight-bold text-primary mb-0">Informations de compte</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Date de création -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date de création</label>
                                    <input type="text" class="form-control" value="{{ $user->date_creation->format('d/m/Y H:i') }}" readonly>
                                </div>
                            </div>
                            
                            <!-- Dernière connexion -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Dernière connexion</label>
                                    <input type="text" class="form-control" value="{{ $user->dernier_login ? $user->dernier_login->format('d/m/Y H:i') : 'Jamais connecté' }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Rôle -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Rôle <span class="text-danger">*</span></label>
                                    <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>Utilisateur standard</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                    </select>
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Statut du compte -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Statut du compte</label>
                                    <div class="custom-control custom-switch mt-2">
                                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                              {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Compte actif</label>
                                    </div>
                                    <small class="form-text text-muted">Si désactivé, l'utilisateur ne pourra pas se connecter</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.password.reset', $user->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-key"></i> Réinitialiser le mot de passe
                            </a>
                            <small class="form-text text-muted mt-1">Un email sera envoyé à l'utilisateur pour réinitialiser son mot de passe</small>
                        </div>
                    </div>
                </div>
                
                <!-- Paramètres utilisateur -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="font-weight-bold text-primary mb-0">Paramètres utilisateur</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Devise par défaut -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="devise_defaut">Devise par défaut</label>
                                    <select class="form-control" id="devise_defaut" name="devise_defaut">
                                        <option value="MAD" {{ old('devise_defaut', $user->devise_defaut) == 'MAD' ? 'selected' : '' }}>Dirham marocain (MAD)</option>
                                        <option value="EUR" {{ old('devise_defaut', $user->devise_defaut) == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                        <option value="USD" {{ old('devise_defaut', $user->devise_defaut) == 'USD' ? 'selected' : '' }}>Dollar américain (USD)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Langue préférée -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="langue">Langue préférée</label>
                                    <select class="form-control" id="langue" name="langue">
                                        <option value="fr" {{ old('langue', $user->langue) == 'fr' ? 'selected' : '' }}>Français</option>
                                        <option value="en" {{ old('langue', $user->langue) == 'en' ? 'selected' : '' }}>Anglais</option>
                                        <option value="ar" {{ old('langue', $user->langue) == 'ar' ? 'selected' : '' }}>Arabe</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Options utilisateur -->
                            <div class="col-md-6">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="email_verified" name="email_verified" 
                                          {{ old('email_verified', $user->email_verified_at != null) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="email_verified">Email vérifié</label>
                                </div>
                                
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="notifications_enabled" name="notifications_enabled" 
                                          {{ old('notifications_enabled', $user->notifications_enabled) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="notifications_enabled">Notifications par email</label>
                                </div>
                            </div>
                            
                            <!-- Préférences d'affichage -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="theme">Thème préféré</label>
                                    <select class="form-control" id="theme" name="theme">
                                        <option value="light" {{ old('theme', $user->theme) == 'light' ? 'selected' : '' }}>Clair</option>
                                        <option value="dark" {{ old('theme', $user->theme) == 'dark' ? 'selected' : '' }}>Sombre</option>
                                        <option value="system" {{ old('theme', $user->theme) == 'system' ? 'selected' : '' }}>Système</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Statistiques -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="font-weight-bold text-primary mb-0">Statistiques d'utilisation</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-primary h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Budgets</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->budgets_count ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-success h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Revenus</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->incomes_count ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-info h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Dépenses</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->expenses_count ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-warning h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Objectifs</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $user->goals_count ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-danger mr-2" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $user->nom }} {{ $user->prenom }}</strong> ?</p>
                <p class="text-danger">Cette action est irréversible et supprimera toutes les données associées à cet utilisateur (budgets, dépenses, comptes, etc.).</p>
                
                @if(($user->budgets_count ?? 0) > 0 || ($user->expenses_count ?? 0) > 0 || ($user->incomes_count ?? 0) > 0 || ($user->goals_count ?? 0) > 0)
                    <div class="alert alert-danger">
                        <h6 class="font-weight-bold">Attention !</h6>
                        <p class="mb-0">Cet utilisateur possède :</p>
                        <ul class="mb-0">
                            @if(($user->budgets_count ?? 0) > 0)
                                <li>{{ $user->budgets_count }} budget(s)</li>
                            @endif
                            @if(($user->expenses_count ?? 0) > 0)
                                <li>{{ $user->expenses_count }} dépense(s)</li>
                            @endif
                            @if(($user->incomes_count ?? 0) > 0)
                                <li>{{ $user->incomes_count }} revenu(s)</li>
                            @endif
                            @if(($user->goals_count ?? 0) > 0)
                                <li>{{ $user->goals_count }} objectif(s)</li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-placeholder {
        width: 150px;
        height: 150px;
        background-color: #4e73df;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: bold;
        margin: 0 auto;
    }
</style>
@endpush

@push('scripts')
<script>
    // Afficher le nom du fichier sélectionné pour l'avatar
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.custom-file-input').addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var label = e.target.nextElementSibling;
            label.innerHTML = fileName;
        });
    });
</script>
@endpush