@extends('layouts.admin')

@section('title', 'Modifier une catégorie')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Modifier la catégorie</h1>
        <div>
            <a href="{{ route('admin.categories.show', $category->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm mr-2">
                <i class="fas fa-eye fa-sm text-white-50"></i> Voir les détails
            </a>
            <a href="{{ route('admin.categories.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informations de la catégorie</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Actions:</div>
                    <a class="dropdown-item" href="{{ route('admin.categories.show', $category->id) }}">Voir les détails</a>
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

            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Nom de la catégorie -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nom">Nom de la catégorie <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" 
                                   value="{{ old('nom', $category->nom) }}" required>
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
                                       id="pourcentage_defaut" name="pourcentage_defaut" 
                                       value="{{ old('pourcentage_defaut', $category->pourcentage_defaut) }}" 
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
                              name="description" rows="3">{{ old('description', $category->description) }}</textarea>
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
                            <span class="input-group-text"><i id="selectedIcon" class="fas fa-{{ $category->icone ?? 'tag' }}"></i></span>
                        </div>
                        <input type="text" class="form-control @error('icone') is-invalid @enderror" id="icone" 
                               name="icone" value="{{ old('icone', $category->icone) }}">
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
                                    <button type="button" class="btn {{ $icon == $category->icone ? 'btn-primary' : 'btn-outline-secondary' }} icon-btn p-2" onclick="selectIcon('{{ $icon }}')">
                                        <i class="fas fa-{{ $icon }}"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Couleur -->
                <div class="form-group">
                    <label for="couleur">Couleur</label>
                    <input type="color" class="form-control @error('couleur') is-invalid @enderror" id="couleur" 
                           name="couleur" value="{{ old('couleur', $category->couleur ?? '#4e73df') }}" style="height: 38px;">
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
                            <input type="checkbox" class="custom-control-input" id="active" name="active" 
                                   {{ $category->active ? 'checked' : '' }}>
                            <label class="custom-control-label" for="active">Catégorie active</label>
                            <small class="form-text text-muted">Décochez pour désactiver cette catégorie</small>
                        </div>
                        
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="show_in_reports" name="show_in_reports" 
                                   {{ $category->show_in_reports ? 'checked' : '' }}>
                            <label class="custom-control-label" for="show_in_reports">Afficher dans les rapports</label>
                            <small class="form-text text-muted">Décochez pour masquer cette catégorie dans les rapports</small>
                        </div>
                        
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="allow_subcategories" name="allow_subcategories" 
                                   {{ $category->allow_subcategories ? 'checked' : '' }}>
                            <label class="custom-control-label" for="allow_subcategories">Autoriser les sous-catégories</label>
                            <small class="form-text text-muted">Cochez pour permettre la création de sous-catégories</small>
                        </div>
                    </div>
                </div>
                
                <!-- Statistiques d'utilisation -->
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Statistiques d'utilisation</h6>
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
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category->budgets_count ?? 0 }}</div>
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
                                                    Dépenses</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category->expenses_count ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-money-bill fa-2x text-gray-300"></i>
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
                                                    Objectifs</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category->goals_count ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-bullseye fa-2x text-gray-300"></i>
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
                                                    Alertes</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $category->alerts_count ?? 0 }}</div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-bell fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i> Attention: La modification de cette catégorie affectera {{ $category->total_references ?? 0 }} éléments dans le système.
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
                <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ $category->nom }}</strong> ?</p>
                <p class="text-danger">Cette action est irréversible et pourrait affecter les budgets et dépenses associés à cette catégorie.</p>
                
                @if(($category->budgets_count ?? 0) > 0 || ($category->expenses_count ?? 0) > 0)
                    <div class="alert alert-danger">
                        <h6 class="font-weight-bold">Attention !</h6>
                        <p class="mb-0">Cette catégorie est actuellement utilisée par :</p>
                        <ul class="mb-0">
                            @if(($category->budgets_count ?? 0) > 0)
                                <li>{{ $category->budgets_count }} budget(s)</li>
                            @endif
                            @if(($category->expenses_count ?? 0) > 0)
                                <li>{{ $category->expenses_count }} dépense(s)</li>
                            @endif
                            @if(($category->goals_count ?? 0) > 0)
                                <li>{{ $category->goals_count }} objectif(s)</li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
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
            document.getElementById('selectedIcon').className = 'fas fa-' + initialIcon;
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