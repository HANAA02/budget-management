@extends('layouts.user')

@section('title', 'Ajouter un revenu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Ajouter un nouveau revenu</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.incomes.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="compte_id" class="col-md-3 col-form-label">Compte</label>
                            <div class="col-md-9">
                                <select class="form-control @error('compte_id') is-invalid @enderror" id="compte_id" name="compte_id" required>
                                    <option value="">Sélectionnez un compte</option>
                                    @foreach($comptes as $compte)
                                        <option value="{{ $compte->id }}" {{ old('compte_id') == $compte->id ? 'selected' : '' }}>
                                            {{ $compte->nom }} ({{ number_format($compte->solde, 2) }} {{ $compte->devise }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('compte_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">
                                    <a href="{{ route('user.accounts.create') }}">
                                        <i class="fa fa-plus-circle"></i> Créer un nouveau compte
                                    </a>
                                </small>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="source" class="col-md-3 col-form-label">Source du revenu</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control @error('source') is-invalid @enderror" id="source" name="source" value="{{ old('source') }}" required>
                                @error('source')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <small class="form-text text-muted">Exemple : Salaire, Freelance, Location, etc.</small>
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="montant" class="col-md-3 col-form-label">Montant</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control @error('montant') is-invalid @enderror" id="montant" name="montant" value="{{ old('montant') }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text devise-label">MAD</span>
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
                            <label for="date_perception" class="col-md-3 col-form-label">Date de perception</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control @error('date_perception') is-invalid @enderror" id="date_perception" name="date_perception" value="{{ old('date_perception', now()->format('Y-m-d')) }}" required>
                                @error('date_perception')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="periodicite" class="col-md-3 col-form-label">Périodicité</label>
                            <div class="col-md-9">
                                <select class="form-control @error('periodicite') is-invalid @enderror" id="periodicite" name="periodicite">
                                    <option value="ponctuel" {{ old('periodicite') == 'ponctuel' ? 'selected' : '' }}>Ponctuel (une seule fois)</option>
                                    <option value="mensuel" {{ old('periodicite', 'mensuel') == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                                    <option value="bimensuel" {{ old('periodicite') == 'bimensuel' ? 'selected' : '' }}>Bimensuel (deux fois par mois)</option>
                                    <option value="hebdomadaire" {{ old('periodicite') == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                                    <option value="trimestriel" {{ old('periodicite') == 'trimestriel' ? 'selected' : '' }}>Trimestriel</option>
                                    <option value="annuel" {{ old('periodicite') == 'annuel' ? 'selected' : '' }}>Annuel</option>
                                </select>
                                @error('periodicite')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3" id="date-recurrence-group" style="display: none;">
                            <label for="jour_perception" class="col-md-3 col-form-label">Jour de perception</label>
                            <div class="col-md-9">
                                <input type="number" min="1" max="31" class="form-control @error('jour_perception') is-invalid @enderror" id="jour_perception" name="jour_perception" value="{{ old('jour_perception') }}">
                                <small class="form-text text-muted">Jour du mois où le revenu est perçu habituellement.</small>
                                @error('jour_perception')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <div class="col-md-9 offset-md-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="creer_budget" name="creer_budget" value="1" {{ old('creer_budget') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="creer_budget">Créer un budget automatiquement avec ce revenu</label>
                                </div>
                                <small class="form-text text-muted">Un budget mensuel sera créé avec ce montant.</small>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Enregistrer
                                </button>
                                <a href="{{ route('user.incomes.index') }}" class="btn btn-secondary">
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
        // Mise à jour de la devise en fonction du compte sélectionné
        $('#compte_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                const text = selectedOption.text();
                const match = text.match(/\(([\d,.]+) ([A-Z]+)\)/);
                if (match && match[2]) {
                    $('.devise-label').text(match[2]);
                }
            } else {
                $('.devise-label').text('MAD');
            }
        });
        
        // Affichage conditionnel du champ de jour de perception
        $('#periodicite').change(function() {
            const periodicite = $(this).val();
            
            if (periodicite !== 'ponctuel') {
                $('#date-recurrence-group').show();
                
                // Pré-remplir le jour à partir de la date de perception
                if (!$('#jour_perception').val()) {
                    const datePerception = $('#date_perception').val();
                    if (datePerception) {
                        const date = new Date(datePerception);
                        $('#jour_perception').val(date.getDate());
                    }
                }
            } else {
                $('#date-recurrence-group').hide();
            }
        });
        
        // Initialisation au chargement de la page
        $('#periodicite').trigger('change');
        $('#compte_id').trigger('change');
        
        // Mise à jour du jour de perception lorsque la date change
        $('#date_perception').change(function() {
            const datePerception = $(this).val();
            if (datePerception && $('#periodicite').val() !== 'ponctuel') {
                const date = new Date(datePerception);
                $('#jour_perception').val(date.getDate());
            }
        });
    });
</script>
@endsection
