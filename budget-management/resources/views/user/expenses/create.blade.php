@extends('layouts.user')

@section('title', 'Ajouter une dépense')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Ajouter une nouvelle dépense</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.expenses.store') }}" method="POST">
                        @csrf
                        
                        @if(isset($budget_id))
                        <input type="hidden" name="budget_id" value="{{ $budget_id }}">
                        @else
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
                        @endif
                        
                        <div class="form-group row mb-3">
                            <label for="categorie_budget_id" class="col-md-3 col-form-label">Catégorie</label>
                            <div class="col-md-9">
                                <select class="form-control @error('categorie_budget_id') is-invalid @enderror" id="categorie_budget_id" name="categorie_budget_id" required {{ isset($budget_id) ? '' : 'disabled' }}>
                                    <option value="">Sélectionnez une catégorie</option>
                                    @if(isset($categories))
                                        @foreach($categories as $categorie)
                                            <option value="{{ $categorie->id }}" {{ old('categorie_budget_id') == $categorie->id ? 'selected' : '' }}>
                                                {{ $categorie->categorie->nom }} 
                                                ({{ number_format($categorie->montant_alloue - ($categorie->depensesTotal ?? 0), 2) }} MAD disponible)
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
                            <label for="description" class="col-md-3 col-form-label">Description</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" value="{{ old('description') }}" required>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="montant" class="col-md-3 col-form-label">Montant</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('montant') is-invalid @enderror" id="montant" name="montant" value="{{ old('montant') }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">MAD</span>
                                    </div>
                                </div>
                                @error('montant')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="date_depense" class="col-md-3 col-form-label">Date de la dépense</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control @error('date_depense') is-invalid @enderror" id="date_depense" name="date_depense" value="{{ old('date_depense', now()->format('Y-m-d')) }}" required>
                                @error('date_depense')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="statut" class="col-md-3 col-form-label">Statut</label>
                            <div class="col-md-9">
                                <select class="form-control @error('statut') is-invalid @enderror" id="statut" name="statut">
                                    <option value="validée" {{ old('statut') == 'validée' ? 'selected' : '' }}>Validée</option>
                                    <option value="en attente" {{ old('statut') == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="annulée" {{ old('statut') == 'annulée' ? 'selected' : '' }}>Annulée</option>
                                </select>
                                @error('statut')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <div id="alert-message" class="alert alert-warning" style="display: none;">
                                    <i class="fa fa-exclamation-triangle"></i> 
                                    <span id="alert-content"></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Enregistrer
                                </button>
                                <a href="{{ isset($budget_id) ? route('user.budgets.show', $budget_id) : route('user.expenses.index') }}" class="btn btn-secondary">
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
                            const disponible = parseFloat(value.montant_alloue) - parseFloat(value.depenses_total || 0);
                            $('#categorie_budget_id').append('<option value="' + value.id + '">' + 
                                value.categorie.nom + ' (' + disponible.toFixed(2) + ' MAD disponible)</option>');
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
        
        // Vérification du montant disponible dans la catégorie
        $('#categorie_budget_id, #montant').change(function() {
            const categorieId = $('#categorie_budget_id').val();
            const montant = parseFloat($('#montant').val()) || 0;
            
            if (categorieId && montant > 0) {
                const selectedOption = $('#categorie_budget_id option:selected');
                const texteOption = selectedOption.text();
                const match = texteOption.match(/\(([\d,.]+) MAD disponible\)/);
                
                if (match) {
                    const disponible = parseFloat(match[1].replace(',', ''));
                    
                    if (montant > disponible) {
                        $('#alert-content').text('Le montant saisi dépasse le budget disponible pour cette catégorie.');
                        $('#alert-message').show();
                    } else {
                        $('#alert-message').hide();
                    }
                }
            } else {
                $('#alert-message').hide();
            }
        });
    });
</script>
@endsection