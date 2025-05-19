@extends('layouts.user')

@section('title', 'Créer un objectif')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Créer un nouvel objectif financier</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.goals.store') }}" method="POST">
                        @csrf
                        
                        <div class="form-group row mb-3">
                            <label for="categorie_id" class="col-md-3 col-form-label">Catégorie</label>
                            <div class="col-md-9">
                                <select class="form-control @error('categorie_id') is-invalid @enderror" id="categorie_id" name="categorie_id" required>
                                    <option value="">Sélectionnez une catégorie</option>
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
                        
                        <div class="form-group row mb-3">
                            <label for="titre" class="col-md-3 col-form-label">Titre de l'objectif</label>
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
                            <label for="description" class="col-md-3 col-form-label">Description</label>
                            <div class="col-md-9">
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="type" class="col-md-3 col-form-label">Type d'objectif</label>
                            <div class="col-md-9">
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="epargne" {{ old('type') == 'epargne' ? 'selected' : '' }}>Épargne (atteindre un montant cible)</option>
                                    <option value="reduction" {{ old('type') == 'reduction' ? 'selected' : '' }}>Réduction (limiter les dépenses)</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-group row mb-3">
                            <label for="montant_cible" class="col-md-3 col-form-label">Montant cible</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control @error('montant_cible') is-invalid @enderror" id="montant_cible" name="montant_cible" value="{{ old('montant_cible') }}" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">MAD</span>
                                    </div>
                                </div>
                                <small id="montant-help" class="form-text text-muted">
                                    Pour un objectif d'épargne, c'est le montant à atteindre. Pour un objectif de réduction, c'est la limite de dépenses à ne pas dépasser.
                                </small>
                                @error('montant_cible')
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
                                <input type="date" class="form-control @error('date_fin') is-invalid @enderror" id="date_fin" name="date_fin" value="{{ old('date_fin', now()->addMonths(3)->format('Y-m-d')) }}" required>
                                @error('date_fin')
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
                                    <option value="en cours" {{ old('statut') == 'en cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="atteint" {{ old('statut') == 'atteint' ? 'selected' : '' }}>Atteint</option>
                                    <option value="annulé" {{ old('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Créer l'objectif
                                </button>
                                <a href="{{ route('user.goals.index') }}" class="btn btn-secondary">
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
        // Mise à jour du texte d'aide en fonction du type d'objectif
        $('#type').change(function() {
            const objectifType = $(this).val();
            
            if (objectifType === 'epargne') {
                $('#montant-help').text('Pour un objectif d\'épargne, c\'est le montant à atteindre.');
            } else if (objectifType === 'reduction') {
                $('#montant-help').text('Pour un objectif de réduction, c\'est la limite de dépenses à ne pas dépasser.');
            }
        });
    });
</script>
@endsection
