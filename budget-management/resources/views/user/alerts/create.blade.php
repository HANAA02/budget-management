@extends('layouts.user')

@section('title', 'Créer une alerte')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Créer une nouvelle alerte</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.alerts.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="budget_id" class="col-md-3 col-form-label">Budget</label>
                            <div class="col-md-9">
                                <select class="form-control @error('budget_id') is-invalid @enderror" id="budget_id" name="budget_id" required>
                                    <option value="">Sélectionnez un budget</option>
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
                        
                        <div class="form-group row mb-3">
                            <label for="categorie_budget_id" class="col-md-3 col-form-label">Catégorie</label>
                            <div class="col-md-9">
                                <select class="form-control @error('categorie_budget_id') is-invalid @enderror" id="categorie_budget_id" name="categorie_budget_id" required {{ !old('budget_id') ? 'disabled' : '' }}>
                                    <option value="">Sélectionnez une catégorie</option>
                                    @if(old('budget_id') && isset($categories))
                                        @foreach($categories as $categorie)
                                            <option value="{{ $categorie->id }}" {{ old('categorie_budget_id') == $categorie->id ? 'selected' : '' }}>
                                                {{ $categorie->categorie->nom }} ({{ number_format($categorie->montant_alloue, 2) }} MAD)
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('categorie_budget_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="type" class="col-md-3 col-form-label">Type d'alerte</label>
                            <div class="col-md-9">
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="pourcentage" {{ old('type') == 'pourcentage' ? 'selected' : '' }}>Pourcentage du budget atteint</option>
                                    <option value="montant" {{ old('type') == 'montant' ? 'selected' : '' }}>Montant spécifique atteint</option>
                                    <option value="depassement" {{ old('type') == 'depassement' ? 'selected' : '' }}>Dépassement du budget</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3" id="seuil-pourcentage-group">
                            <label for="seuil_pourcentage" class="col-md-3 col-form-label">Seuil de déclenchement</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="number" min="1" max="100" step="1" class="form-control @error('seuil_pourcentage') is-invalid @enderror" id="seuil_pourcentage" name="seuil_pourcentage" value="{{ old('seuil_pourcentage', 80) }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Pourcentage du budget atteint qui déclenchera l'alerte.</small>
                                @error('seuil_pourcentage')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3" id="seuil-montant-group" style="display: none;">
                            <label for="seuil_montant" class="col-md-3 col-form-label">Montant de déclenchement</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="number" min="0" step="0.01" class="form-control @error('seuil_montant') is-invalid @enderror" id="seuil_montant" name="seuil_montant" value="{{ old('seuil_montant') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text">MAD</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Montant dépensé qui déclenchera l'alerte.</small>
                                @error('seuil_montant')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="active" name="active" value="1" {{ old('active', '1') == '1' ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="active">Activer l'alerte</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> Les alertes vous informeront par notification lorsque le seuil défini sera atteint.
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Créer l'alerte
                                </button>
                                <a href="{{ route('user.alerts.index') }}" class="btn btn-secondary">
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
        // Chargement des catégories en fonction du budget sélectionné
        $('#budget_id').change(function() {
            const budgetId = $(this).val();
            if (budgetId) {
                $.ajax({
                    url: '/user/budgets/' + budgetId + '/categories',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#categorie_budget_id').empty();
                        $('#categorie_budget_id').append('<option value="">Sélectionnez une catégorie</option>');
                        
                        $.each(data, function(key, value) {
                            $('#categorie_budget_id').append('<option value="' + value.id + '">' + 
                                value.categorie.nom + ' (' + value.montant_alloue.toFixed(2) + ' MAD)</option>');
                        });
                        
                        $('#categorie_budget_id').prop('disabled', false);
                    },
                    error: function() {
                        $('#categorie_budget_id').empty();
                        $('#categorie_budget_id').append('<option value="">Erreur lors du chargement des catégories</option>');
                        $('#categorie_budget_id').prop('disabled', true);
                    }
                });
            } else {
                $('#categorie_budget_id').empty();
                $('#categorie_budget_id').append('<option value="">Sélectionnez un budget d\'abord</option>');
                $('#categorie_budget_id').prop('disabled', true);
            }
        });
        
        // Affichage conditionnel des champs en fonction du type d'alerte
        $('#type').change(function() {
            const alertType = $(this).val();
            
            if (alertType === 'pourcentage' || alertType === 'depassement') {
                $('#seuil-pourcentage-group').show();
                $('#seuil-montant-group').hide();
                
                if (alertType === 'depassement') {
                    $('#seuil_pourcentage').val(100);
                }
            } else if (alertType === 'montant') {
                $('#seuil-pourcentage-group').hide();
                $('#seuil-montant-group').show();
            }
        });
        
        // Initialisation au chargement de la page
        $('#type').trigger('change');
    });
</script>
@endsection