@extends('layouts.user')

@section('title', 'Détail du revenu')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Détail du revenu</h1>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <a href="{{ route('user.incomes.edit', $income->id) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> Modifier
                </a>
                <a href="{{ route('user.incomes.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Retour
                </a>
                <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                    <i class="fa fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title">Informations sur le revenu</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Source</th>
                                    <td>{{ $income->source }}</td>
                                </tr>
                                <tr>
                                    <th>Date de perception</th>
                                    <td>{{ $income->date_perception->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Montant</th>
                                    <td>
                                        <span class="font-weight-bold">{{ number_format($income->montant, 2) }} {{ $income->compte->devise }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Compte</th>
                                    <td>
                                        <a href="{{ route('user.accounts.show', $income->compte->id) }}">
                                            {{ $income->compte->nom }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Périodicité</th>
                                    <td>{{ $income->periodicite }}</td>
                                </tr>
                                @if($income->periodicite != 'ponctuel' && $income->jour_perception)
                                <tr>
                                    <th>Jour de perception</th>
                                    <td>Jour {{ $income->jour_perception }} de chaque {{ $income->periodicite == 'mensuel' ? 'mois' : ($income->periodicite == 'trimestriel' ? 'trimestre' : 'période') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <th>Prochaine perception</th>
                                    <td>
                                        @if($income->periodicite == 'ponctuel')
                                            Revenu ponctuel (pas de récurrence)
                                        @else
                                            {{ $nextPerception->format('d/m/Y') }}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date de création</th>
                                    <td>{{ $income->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($income->periodicite != 'ponctuel')
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title">Prévisions des prochaines perceptions</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($futurePerceptions as $date)
                                    <tr>
                                        <td>{{ $date->format('d/m/Y') }}</td>
                                        <td>{{ number_format($income->montant, 2) }} {{ $income->compte->devise }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title">Résumé du compte</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="mb-0">{{ number_format($income->compte->solde, 2) }} {{ $income->compte->devise }}</h2>
                        <p class="text-muted">Solde actuel</p>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <th>Nom du compte</th>
                            <td>{{ $income->compte->nom }}</td>
                        </tr>
                        <tr>
                            <th>Devise</th>
                            <td>{{ $income->compte->devise }}</td>
                        </tr>
                        <tr>
                            <th>Date de création</th>
                            <td>{{ $income->compte->date_creation->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <a href="{{ route('user.accounts.show', $income->compte->id) }}" class="btn btn-outline-primary btn-block">
                            <i class="fa fa-eye"></i> Voir les détails du compte
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title">Statistiques</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="card border-0 bg-light p-3">
                                <h4 class="mb-0">{{ $totalPerceptions }}</h4>
                                <p class="text-muted small">Perceptions totales</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card border-0 bg-light p-3">
                                <h4 class="mb-0">{{ number_format($totalAmount, 2) }}</h4>
                                <p class="text-muted small">Montant total</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="card border-0 bg-light p-3">
                                <h4 class="mb-0">{{ number_format($averageMonthly, 2) }}</h4>
                                <p class="text-muted small">Moyenne mensuelle</p>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card border-0 bg-light p-3">
                                <h4 class="mb-0">{{ number_format($income->montant * 12, 2) }}</h4>
                                <p class="text-muted small">Annualisé</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression -->
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
                <p>Êtes-vous sûr de vouloir supprimer le revenu <span class="font-weight-bold">{{ $income->source }}</span> ?</p>
                <p class="text-danger">Cette action mettra à jour le solde de votre compte.</p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('user.incomes.destroy', $income->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection