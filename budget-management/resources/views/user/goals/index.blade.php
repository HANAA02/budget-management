@extends('layouts.user')

@section('title', 'Mes objectifs financiers')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Mes objectifs financiers</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('user.goals.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Nouvel objectif
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Filtres</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.goals.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="categorie_id">Catégorie</label>
                                <select class="form-control" id="categorie_id" name="categorie_id">
                                    <option value="">Toutes les catégories</option>
                                    @foreach($categories as $categorie)
                                        <option value="{{ $categorie->id }}" {{ request('categorie_id') == $categorie->id ? 'selected' : '' }}>
                                            {{ $categorie->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="type">Type d'objectif</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="">Tous les types</option>
                                    <option value="epargne" {{ request('type') == 'epargne' ? 'selected' : '' }}>Épargne</option>
                                    <option value="reduction" {{ request('type') == 'reduction' ? 'selected' : '' }}>Réduction</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="statut">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="">Tous les statuts</option>
                                    <option value="en cours" {{ request('statut') == 'en cours' ? 'selected' : '' }}>En cours</option>
                                    <option value="atteint" {{ request('statut') == 'atteint' ? 'selected' : '' }}>Atteint</option>
                                    <option value="annulé" {{ request('statut') == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="search">Recherche</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" name="search" placeholder="Rechercher..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i> Filtrer
                                </button>
                                <a href="{{ route('user.goals.index') }}" class="btn btn-secondary">
                                    <i class="fa fa-times"></i> Réinitialiser
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Liste des objectifs</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($goals) > 0)
                        <div class="row p-4">
                            @foreach($goals as $goal)
                                @php
                                    $progression = $goal->progression ?? 0;
                                    $progressClass = 'success';
                                    
                                    if ($goal->type == 'epargne') {
                                        if ($progression < 25) {
                                            $progressClass = 'danger';
                                        } elseif ($progression < 75) {
                                            $progressClass = 'warning';
                                        }
                                    } else { // réduction
                                        if ($progression > 90) {
                                            $progressClass = 'danger';
                                        } elseif ($progression > 75) {
                                            $progressClass = 'warning';
                                        }
                                    }
                                    
                                    $statusClass = 'info';
                                    if ($goal->statut == 'atteint') {
                                        $statusClass = 'success';
                                    } elseif ($goal->statut == 'annulé') {
                                        $statusClass = 'danger';
                                    }
                                @endphp
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border-{{ $progressClass }}">
                                        <div class="card-header bg-white">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="card-title mb-0">
                                                    <a href="{{ route('user.goals.show', $goal->id) }}">{{ $goal->titre }}</a>
                                                </h6>
                                                <span class="badge badge-{{ $statusClass }}">
                                                    {{ $goal->statut }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-2 text-muted small">
                                                <i class="fa {{ $goal->categorie->icone ?? 'fa-tag' }}"></i>
                                                {{ $goal->categorie->nom }} | 
                                                <i class="fa {{ $goal->type == 'epargne' ? 'fa-piggy-bank' : 'fa-chart-line' }}"></i>
                                                {{ $goal->type == 'epargne' ? 'Épargne' : 'Réduction' }}
                                            </div>
                                            
                                            <div class="text-center mb-3">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <h5 class="mb-0">{{ number_format($goal->montant_actuel ?? 0, 2) }}</h5>
                                                        <p class="text-muted small">{{ $goal->type == 'epargne' ? 'Épargné' : 'Dépensé' }}</p>
                                                    </div>
                                                    <div class="col-6">
                                                        <h5 class="mb-0">{{ number_format($goal->montant_cible, 2) }}</h5>
                                                        <p class="text-muted small">Objectif</p>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="progress" style="height: 15px;">
                                                <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" 
                                                    style="width: {{ $progression }}%;" 
                                                    aria-valuenow="{{ $progression }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                    {{ $progression }}%
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3 text-muted small">
                                                <i class="fa fa-calendar"></i> 
                                                {{ $goal->date_debut->format('d/m/Y') }} - {{ $goal->date_fin->format('d/m/Y') }}
                                            </div>
                                            
                                            @if($goal->description)
                                                <div class="mt-2">
                                                    <p class="text-muted small">{{ Str::limit($goal->description, 100) }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-footer bg-white">
                                            <div class="btn-group w-100">
                                                <a href="{{ route('user.goals.show', $goal->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fa fa-eye"></i> Détails
                                                </a>
                                                <a href="{{ route('user.goals.edit', $goal->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-edit"></i> Modifier
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fa fa-bullseye fa-3x text-muted mb-3"></i>
                                <h6 class="text-muted">Aucun objectif trouvé</h6>
                                <p class="text-muted">Créez des objectifs financiers pour suivre votre progression vers vos buts.</p>
                                <a href="{{ route('user.goals.create') }}" class="btn btn-primary mt-3">
                                    <i class="fa fa-plus"></i> Créer un objectif
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $goals->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
