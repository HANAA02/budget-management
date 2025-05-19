@extends('layouts.user')

@section('title', 'Générer un rapport')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Générer un rapport</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Paramètres du rapport</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.reports.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="titre" class="col-md-3 col-form-label">Titre du rapport</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre') }}" required>
                                @error('titre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="type" class="col-md-3 col-form-label">Type de rapport</label>
                            <div class="col-md-9">
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="budget" {{ old('type') == 'budget' ? 'selected' : '' }}>Rapport de budget</option>
                                    <option value="depenses" {{ old('type') == 'depenses' ? 'selected' : '' }}>Rapport de dépenses</option>
                                    <option value="revenus" {{ old('type') == 'revenus' ? 'selected' : '' }}>Rapport de revenus</option>
                                    <option value="objectifs" {{ old('type') == 'objectifs' ? 'selected' : '' }}>Rapport d'objectifs</option>
                                    <option value="complet" {{ old('type') == 'complet' ? 'selected' : '' }}>Rapport complet</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="periode" class="col-md-3 col-form-label">Période</label>
                            <div class="col-md-9">
                                <select class="form-control @error('periode') is-invalid @enderror" id="periode" name="periode" required>
                                    <option value="mois_courant" {{ old('periode') == 'mois_courant' ? 'selected' : '' }}>Mois courant</option>
                                    <option value="mois_precedent" {{ old('periode') == 'mois_precedent' ? 'selected' : '' }}>Mois précédent</option>
                                    <option value="trimestre_courant" {{ old('periode') == 'trimestre_courant' ? 'selected' : '' }}>Trimestre courant</option>
                                    <option value="annee_courante" {{ old('periode') == 'annee_courante' ? 'selected' : '' }}>Année courante</option>
                                    <option value="personnalise" {{ old('periode') == 'personnalise' ? 'selected' : '' }}>Période personnalisée</option>
                                </select>
                                @error('periode')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div id="dates-personnalisees" style="{{ old('periode') == 'personnalise' ? '' : 'display: none;' }}">
                            <div class="form-group row mb-3">
                                <label for="date_debut" class="col-md-3 col-form-label">Date de début</label>
                                <div class="col-md-9">
                                    <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut') }}">
                                    @error('date_debut')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="form-group row mb-3">
                                <label for="date_fin" class="col-md-3 col-form-label">Date de fin</label>
                                <div class="col-md-9">
                                    <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin') }}">
                                    @error('date_fin')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div id="options-budget" style="{{ old('type') == 'budget' || old('type') == 'complet' || !old('type') ? '' : 'display: none;' }}">
                            <div class="form-group row mb-3">
                                <label for="budget_id" class="col-md-3 col-form-label">Budget spécifique</label>
                                <div class="col-md-9">
                                    <select class="form-control @error('budget_id') is-invalid @enderror" id="budget_id" name="budget_id">
                                        <option value="">Tous les budgets</option>
                                        @foreach($budgets as $budget)
                                            <option value="{{ $budget->id }}" {{ old('budget_id') == $budget->id ? 'selected' : '' }}>
                                                {{ $budget->nom }} ({{ $budget->date_debut->format('d/m/Y') }} - {{ $budget->date_fin->format('d/m/Y') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('budget_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div id="options-categorie" style="{{ old('type') == 'depenses' || old('type') == 'objectifs' || old('type') == 'complet' ? '' : 'display: none;' }}">
                            <div class="form-group row mb-3">
                                <label for="categorie_id" class="col-md-3 col-form-label">Catégorie spécifique</label>
                                <div class="col-md-9">
                                    <select class="form-control @error('categorie_id') is-invalid @enderror" id="categorie_id" name="categorie_id">
                                        <option value="">Toutes les catégories</option>
                                        @foreach($categories as $categorie)
                                            <option value="{{ $categorie->id }}" {{ old('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                                {{ $categorie->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categorie_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div id="options-comptes" style="{{ old('type') == 'revenus' || old('type') == 'complet' ? '' : 'display: none;' }}">
                            <div class="form-group row mb-3">
                                <label for="compte_id" class="col-md-3 col-form-label">Compte spécifique</label>
                                <div class="col-md-9">
                                    <select class="form-control @error('compte_id') is-invalid @enderror" id="compte_id" name="compte_id">
                                        <option value="">Tous les comptes</option>
                                        @foreach($comptes as $compte)
                                            <option value="{{ $compte->id }}" {{ old('compte_id') == $compte->id ? 'selected' : '' }}>
                                                {{ $compte->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('compte_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="format" class="col-md-3 col-form-label">Format de sortie</label>
                            <div class="col-md-9">
                                <select class="form-control @error('format') is-invalid @enderror" id="format" name="format" required>
                                    <option value="html" {{ old('format', 'html') == 'html' ? 'selected' : '' }}>HTML (visualisation en ligne)</option>
                                    <option value="pdf" {{ old('format') == 'pdf' ? 'selected' : '' }}>PDF (téléchargement)</option>
                                    <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>CSV (données brutes)</option>
                                </select>
                                @error('format')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <h5>Options d'affichage</h5>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="inclure_graphiques" name="inclure_graphiques" value="1" {{ old('inclure_graphiques', '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="inclure_graphiques">Inclure les graphiques</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="inclure_statistiques" name="inclure_statistiques" value="1" {{ old('inclure_statistiques', '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="inclure_statistiques">Inclure les statistiques détaillées</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="inclure_tendances" name="inclure_tendances" value="1" {{ old('inclure_tendances', '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="inclure_tendances">Inclure l'analyse des tendances</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="inclure_recommandations" name="inclure_recommandations" value="1" {{ old('inclure_recommandations', '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="inclure_recommandations">Inclure les recommandations</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-file-alt"></i> Générer le rapport
                                </button>
                                <a href="{{ route('user.reports.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Affichage conditionnel des champs en fonction du type de rapport
        $('#type').change(function() {
            const reportType = $(this).val();
            
            // Gestion des options de budget
            if (reportType === 'budget' || reportType === 'complet') {
                $('#options-budget').show();
            } else {
                $('#options-budget').hide();
            }
            
            // Gestion des options de catégorie
            if (reportType === 'depenses' || reportType === 'objectifs' || reportType === 'complet') {
                $('#options-categorie').show();
            } else {
                $('#options-categorie').hide();
            }
            
            // Gestion des options de comptes
            if (reportType === 'revenus' || reportType === 'complet') {
                $('#options-comptes').show();
            } else {
                $('#options-comptes').hide();
            }
        });
        
        // Affichage conditionnel des dates personnalisées
        $('#periode').change(function() {
            const periode = $(this).val();
            
            if (periode === 'personnalise') {
                $('#dates-personnalisees').show();
            } else {
                $('#dates-personnalisees').hide();
            }
        });
        
        // Initialisation au chargement de la page
        $('#type').trigger('change');
        $('#periode').trigger('change');
    });
</script>
@endsection
