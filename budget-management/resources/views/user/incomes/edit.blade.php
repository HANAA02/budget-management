@extends('layouts.user')

@section('title', 'Modifier le revenu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Modifier le revenu</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.incomes.update', $income->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group row mb-3">
                            <label for="compte_id" class="col-md-3 col-form-label">Compte</label>
                            <div class="col-md-9">
                                <select class="form-control @error('compte_id') is-invalid @enderror" id="compte_id" name="compte_id" required>
                                    <option value="">Sélectionnez un compte</option>
                                    @foreach($comptes as $compte)
                                        <option value="{{ $compte->id }}" {{ old('compte_id', $income->compte_id) == $compte->id ? 'selected' : '' }}>
                                            {{ $compte->nom }} ({{ number_format($compte->solde, 2) }} {{ $compte->devise }})
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
                        
                        <div class="form-group row mb-3">
                            <label for="source" class="col-md-3 col-form-label">Source du revenu</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control @error('source') is-invalid @enderror" id="source" name="source" value="{{ old('source', $income->source) }}" required>
                                @error('source')
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
                                    <input type="number" step="0.01" min="0" class="form-control @error('montant') is-invalid @enderror" id="montant" name="montant" value="{{ old('montant', $income->montant) }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text devise-label">{{ $income->compte->devise }}</span>
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
                                <input type="date" class="form-control @error('date_perception') is-invalid @enderror" id="date_perception" name="date_perception" value="{{ old('date_perception', $income->date_perception->format('Y-m-d')) }}" required>
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
                                    <option value="ponctuel" {{ old('periodicite', $income->periodicite) == 'ponctuel' ? 'selected' : '' }}>Ponctuel (une seule fois)</option>
                                    <option value="mensuel" {{ old('periodicite', $income->periodicite) == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                                    <option value="bimensuel" {{ old('periodicite', $income->periodicite) == 'bimensuel' ? 'selected' : '' }}>Bimensuel (deux fois par mois)</option>
                                    <option value="hebdomadaire" {{ old('periodicite', $income->periodicite) == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                                    <option value="trimestriel" {{ old('periodicite', $income->periodicite) == 'trimestriel' ? 'selected' : '' }}>Trimestriel</option>
                                    <option value="annuel" {{ old('periodicite', $income->periodicite) == 'annuel' ? 'selected' : '' }}>Annuel</option>
                                </select>
                                @error('periodicite')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3" id="date-recurrence-group" style="{{ $income->periodicite == 'ponctuel' ? 'display: none;' : '' }}">
                            <label for="jour_perception" class="col-md-3 col-form-label">Jour de perception</label>
                            <div class="col-md-9">
                                <input type="number" min="1" max="31" class="form-control @error('jour_perception') is-invalid @enderror" id="jour_perception" name="jour_perception" value="{{ old('jour_perception', $income->jour_perception) }}">
                                <small class="form-text text-muted">Jour du mois où le revenu est perçu habituellement.</small>
                                @error('jour_perception')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Mettre à jour
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
        $('#compte_id').trigger('change');
    });
</script>
@endsection
