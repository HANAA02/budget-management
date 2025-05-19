@extends('layouts.admin')

@section('title', 'Créer un utilisateur')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Créer un nouvel utilisateur</h1>
        <a href="{{ route('admin.users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
        </a>
    </div>

    <!-- Carte principale -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations de l'utilisateur</h6>
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

            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Informations personnelles -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="font-weight-bold text-primary mb-0">Informations personnelles</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Nom -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" 
                                           value="{{ old('nom') }}" required>
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
                                           value="{{ old('prenom') }}" required>
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
                                           value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">L'email doit être unique et servira d'identifiant de connexion</small>
                                </div>
                            </div>
                            
                            <!-- Téléphone (optionnel) -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">Téléphone (optionnel)</label>
                                    <input type="text" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" 
                                           value="{{ old('telephone') }}">
                                    @error('telephone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Avatar (optionnel) -->
                        <div class="form-group">
                            <label for="avatar">Avatar (optionnel)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror" id="avatar" name="avatar">
                                <label class="custom-file-label" for="avatar">Choisir une image</label>
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Formats acceptés : JPG, PNG. Taille maximale : 2Mo</small>
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
                            <!-- Mot de passe -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mot_de_passe">Mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control @error('mot_de_passe') is-invalid @enderror" id="mot_de_passe" name="mot_de_passe" required>
                                    @error('mot_de_passe')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Au moins 8 caractères, incluant lettres, chiffres et caractères spéciaux</small>
                                </div>
                            </div>
                            
                            <!-- Confirmation mot de passe -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mot_de_passe_confirmation">Confirmation du mot de passe <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <!-- Rôle -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">Rôle <span class="text-danger">*</span></label>
                                    <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Utilisateur standard</option>
                                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
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
                                              {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">Compte actif</label>
                                    </div>
                                    <small class="form-text text-muted">Si désactivé, l'utilisateur ne pourra pas se connecter</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Paramètres supplémentaires -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="font-weight-bold text-primary mb-0">Paramètres supplémentaires</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Devise par défaut -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="devise_defaut">Devise par défaut</label>
                                    <select class="form-control" id="devise_defaut" name="devise_defaut">
                                        <option value="MAD" {{ old('devise_defaut') == 'MAD' ? 'selected' : '' }}>Dirham marocain (MAD)</option>
                                        <option value="EUR" {{ old('devise_defaut') == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                        <option value="USD" {{ old('devise_defaut') == 'USD' ? 'selected' : '' }}>Dollar américain (USD)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Langue par défaut -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="langue">Langue préférée</label>
                                    <select class="form-control" id="langue" name="langue">
                                        <option value="fr" {{ old('langue') == 'fr' ? 'selected' : '' }}>Français</option>
                                        <option value="en" {{ old('langue') == 'en' ? 'selected' : '' }}>Anglais</option>
                                        <option value="ar" {{ old('langue') == 'ar' ? 'selected' : '' }}>Arabe</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="email_verified" name="email_verified" 
                                  {{ old('email_verified', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="email_verified">Email vérifié</label>
                            <small class="form-text text-muted">Si coché, l'utilisateur n'aura pas besoin de vérifier son adresse email</small>
                        </div>
                        
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="send_welcome_email" name="send_welcome_email" 
                                  {{ old('send_welcome_email', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="send_welcome_email">Envoyer un email de bienvenue</label>
                            <small class="form-text text-muted">Si coché, un email de bienvenue sera envoyé à l'utilisateur</small>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="text-center mt-4">
                    <button type="reset" class="btn btn-secondary mr-2">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

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