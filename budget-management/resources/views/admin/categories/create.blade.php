@extends('layouts.admin')

@section('title', 'Créer une catégorie')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Créer une nouvelle catégorie</h1>
        <a href="{{ route('admin.categories.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
        </a>
    </div>

    <!-- Carte principale -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations de la catégorie</h6>
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

            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <!-- Nom de la catégorie -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nom">Nom de la catégorie <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" 
                                   value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Le nom doit être unique et descriptif</small>
                        </div>
                    </div>
                    
                    <!-- Pourcentage par défaut -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pourcentage_defaut">Pourcentage par défaut <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('pourcentage_defaut') is-invalid @enderror" 
                                       id="pourcentage_defaut" name="pourcentage_defaut" value="{{ old('pourcentage_defaut', 0) }}" 
                                       min="0" max="100" step="0.01" required>
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                                @error('pourcentage_defaut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Pourcentage suggéré pour la répartition automatique du budget</small>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" 
                              name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Une brève description de la catégorie</small>
                </div>
                
                <!-- Icône -->
                <div class="form-group">
                    <label for="icone">Icône</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i id="selectedIcon" class="fas fa-tag"></i></span>
                        </div>
                        <input type="text" class="form-control @error('icone') is-invalid @enderror" id="icone" 
                               name="icone" value="{{ old('icone', 'tag') }}">
                        @error('icone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <small class="form-text text-muted">Nom de l'icône FontAwesome (sans le préfixe "fa-")</small>
                    
                    <!-- Sélecteur d'icônes -->
                    <div class="mt-3">
                        <p><strong>Icônes populaires :</strong></p>
                        <div class="icon-selection d-flex flex-wrap">
                            @foreach(['home', 'shopping-cart', 'utensils', 'car', 'plane', 'graduation-cap', 'medkit', 'money-bill', 'credit-card', 'gift', 'tshirt', 'dumbbell', 'book', 'film', 'music', 'bus', 'bicycle', 'train', 'phone', 'laptop', 'tv', 'gamepad', 'child', 'paw', 'heart', 'tags'] as $icon)
                                <div class="icon-option mr-2 mb-2" data-icon="{{ $icon }}">
                                    <button type="button" class="btn btn-outline-secondary icon-btn p-2" onclick="selectIcon('{{ $icon }}')">
                                        <i class="fas fa-{{ $icon }}"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Couleur (optionnelle) -->
                <div class="form-group">
                    <label for="couleur">Couleur (optionnelle)</label>
                    <input type="color" class="form-control @error('couleur') is-invalid @enderror" id="couleur" 
                           name="couleur" value="{{ old('couleur', '#4e73df') }}" style="height: 38px;">
                    @error('couleur')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Couleur associée à cette catégorie</small>
                </div>
                
                <!-- Options avancées -->
                <div class="card mt-4 mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Options avancées</h6>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="active" name="active" checked>
                            <label class="custom-control-label" for="active">Catégorie active</label>
                            <small class="form-text text-muted">Décochez pour désactiver cette catégorie</small>
                        </div>
                        
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="show_in_reports" name="show_in_reports" checked>
                            <label class="custom-control-label" for="show_in_reports">Afficher dans les rapports</label>
                            <small class="form-text text-muted">Décochez pour masquer cette catégorie dans les rapports</small>
                        </div>
                        
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="allow_subcategories" name="allow_subcategories">
                            <label class="custom-control-label" for="allow_subcategories">Autoriser les sous-catégories</label>
                            <small class="form-text text-muted">Cochez pour permettre la création de sous-catégories</small>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="text-center mt-4">
                    <button type="reset" class="btn btn-secondary mr-2">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer la catégorie
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Fonction pour mettre à jour l'icône sélectionnée
    function selectIcon(icon) {
        document.getElementById('icone').value = icon;
        document.getElementById('selectedIcon').className = 'fas fa-' + icon;
        
        // Mettre à jour le style des boutons d'icônes
        document.querySelectorAll('.icon-btn').forEach(function(btn) {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-secondary');
        });
        
        // Mettre en évidence l'icône sélectionnée
        document.querySelector('[data-icon="' + icon + '"] .icon-btn').classList.remove('btn-outline-secondary');
        document.querySelector('[data-icon="' + icon + '"] .icon-btn').classList.add('btn-primary');
    }
    
    // Initialiser l'affichage de l'icône sélectionnée
    document.addEventListener('DOMContentLoaded', function() {
        const initialIcon = document.getElementById('icone').value;
        if (initialIcon) {
            selectIcon(initialIcon);
        }
        
        // Mettre à jour l'icône lorsque le champ est modifié manuellement
        document.getElementById('icone').addEventListener('input', function() {
            const iconValue = this.value.trim();
            if (iconValue) {
                document.getElementById('selectedIcon').className = 'fas fa-' + iconValue;
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .icon-btn {
        min-width: 40px;
        min-height: 40px;
    }
    
    .icon-btn i {
        font-size: 1.2rem;
    }
</style>
@endpush