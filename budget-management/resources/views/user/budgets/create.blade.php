@extends('layouts.user')

@section('title', 'Créer un nouveau budget')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Créer un nouveau budget</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.budgets.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="nom" class="col-md-3 col-form-label">Nom du budget</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required>
                                @error('nom')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="date_debut" class="col-md-3 col-form-label">Date de début</label>
                            <div class="col-md-9">
                                <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', now()->format('Y-m-d')) }}" required>
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
                                <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin', now()->addMonth()->format('Y-m-d')) }}" required>
                                @error('date_fin')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="montant_total" class="col-md-3 col-form-label">Montant total</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('montant_total') is-invalid @enderror" id="montant_total" name="montant_total" value="{{ old('montant_total') }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">MAD</span>
                                    </div>
                                </div>
                                @error('montant_total')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">Répartition du budget</h5>
                        
                        <div id="categories-container">
                            @foreach($categories as $categorie)
                            <div class="form-group row mb-3 categorie-row" data-id="{{ $categorie->id }}">
                                <label class="col-md-3 col-form-label">
                                    <i class="fa {{ $categorie->icone ?? 'fa-tag' }}"></i>
                                    {{ $categorie->nom }}
                                </label>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <input type="number" min="0" max="100" class="form-control pourcentage-input" name="categories[{{ $categorie->id }}][pourcentage]" value="{{ $categorie->pourcentage_defaut }}" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="number" step="0.01" class="form-control montant-input" name="categories[{{ $categorie->id }}][montant]" value="0" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text">MAD</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label class="col-md-3 col-form-label font-weight-bold">Total</label>
                            <div class="col-md-3">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="total-pourcentage" value="0" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="total-montant" value="0" readonly>
                                    <div class="input-group-append">
                                        <span class="input-group-text">MAD</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row my-4">
                            <div class="col-md-6 offset-md-3">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                                    <i class="fa fa-save"></i> Créer le budget
                                </button>
                                <a href="{{ route('user.budgets.index') }}" class="btn btn-secondary">
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
        function updateMontants() {
            let totalPourcentage = 0;
            let totalMontant = 0;
            const montantTotal = parseFloat($('#montant_total').val()) || 0;
            
            $('.categorie-row').each(function() {
                const pourcentage = parseFloat($(this).find('.pourcentage-input').val()) || 0;
                totalPourcentage += pourcentage;
                
                const montant = (montantTotal * pourcentage / 100).toFixed(2);
                $(this).find('.montant-input').val(montant);
                
                totalMontant += parseFloat(montant);
            });
            
            $('#total-pourcentage').val(totalPourcentage.toFixed(2));
            $('#total-montant').val(totalMontant.toFixed(2));
            
            // Mettre à jour la barre de progression
            const progressBar = $('.progress-bar');
            progressBar.css('width', totalPourcentage + '%');
            progressBar.attr('aria-valuenow', totalPourcentage);
            progressBar.text(totalPourcentage + '%');
            
            // Changer la couleur en fonction de la valeur
            if (totalPourcentage < 100) {
                progressBar.removeClass('bg-success bg-danger').addClass('bg-warning');
            } else if (totalPourcentage === 100) {
                progressBar.removeClass('bg-warning bg-danger').addClass('bg-success');
                $('#submit-btn').prop('disabled', false);
            } else {
                progressBar.removeClass('bg-success bg-warning').addClass('bg-danger');
            }
        }
        
        $('#montant_total, .pourcentage-input').on('input', updateMontants);
        
        // Distribution automatique égale
        $('#distribute-evenly').click(function() {
            const categoryCount = $('.categorie-row').length;
            const evenPercentage = (100 / categoryCount).toFixed(2);
            
            $('.pourcentage-input').val(evenPercentage);
            updateMontants();
        });
        
        // Initialisation
        updateMontants();
    });
</script>
@endsection
