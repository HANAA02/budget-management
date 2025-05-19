@extends('layouts.user')

@section('title', $goal->titre)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Objectif : {{ $goal->titre }}</h1>
            <p class="text-muted">
                <i class="fa {{ $goal->categorie->icone ?? 'fa-tag' }}"></i> {{ $goal->categorie->nom }} | 
                <i class="fa {{ $goal->type == 'epargne' ? 'fa-piggy-bank' : 'fa-chart-line' }}"></i> 
                {{ $goal->type == 'epargne' ? 'Objectif d\'épargne' : 'Objectif de réduction' }}
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <a href="{{ route('user.goals.edit', $goal->id) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> Modifier
                </a>
                <a href="{{ route('user.goals.index') }}" class="btn btn-secondary">
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
                    <h5 class="card-title">Progression de l'objectif</h5>
                </div>
                <div class="card-body">
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
                        
                        $statusClass = 'primary';
                        if ($goal->statut == 'atteint') {
                            $statusClass = 'success';
                        } elseif ($goal->statut == 'annulé') {
                            $statusClass = 'danger';
                        }
                    @endphp
                    
                    <div class="text-center mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <h2 class="mb-0">{{ number_format($goal->montant_actuel ?? 0, 2) }}</h2>
                                <p class="text-muted">{{ $goal->type == 'epargne' ? 'Montant épargné' : 'Montant dépensé' }}</p>
                            </div>
                            <div class="col-md-4">
                                <h2 class="mb-0">{{ number_format($goal->montant_cible, 2) }}</h2>
                                <p class="text-muted">Montant cible</p>
                            </div>
                            <div class="col-md-4">
                                <h2 class="mb-0">{{ $progression }}%</h2>
                                <p class="text-muted">Progression</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" 
                            style="width: {{ $progression }}%;" 
                            aria-valuenow="{{ $progression }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                            {{ $progression }}%
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="alert alert-{{ $statusClass }}">
                                <strong>Statut :</strong> {{ $goal->statut }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            @php
                                $daysLeft = now()->diffInDays($goal->date_fin, false);
                                $totalDays = $goal->date_debut->diffInDays($goal->date_fin);
                                $timeProgress = max(0, min(100, 100 - (($daysLeft / $totalDays) * 100)));
                            @endphp
                            
                            <div class="alert alert-info">
                                @if($daysLeft > 0)
                                    <strong>Temps restant :</strong> {{ $daysLeft }} jours
                                @elseif($daysLeft == 0)
                                    <strong>Dernier jour !</strong>
                                @else
                                    <strong>Objectif terminé</strong> ({{ abs($daysLeft) }} jours dépassés)
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body bg-light">
                                    <h6>Description :</h6>
                                    <p>{{ $goal->description ?: 'Aucune description disponible.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title">Historique des dépenses liées</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Budget</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($relatedExpenses as $expense)
                                    <tr>
                                        <td>{{ $expense->date_depense->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('user.expenses.show', $expense->id) }}">
                                                {{ $expense->description }}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.budgets.show', $expense->categorieBudget->budget->id) }}">
                                                {{ $expense->categorieBudget->budget->nom }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($expense->montant, 2) }} MAD</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fa fa-receipt fa-3x text-muted mb-3"></i>
                                                <h6 class="text-muted">Aucune dépense liée trouvée</h6>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(count($relatedExpenses) > 0)
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="mb-0">Total: <strong>{{ number_format($relatedExpenses->sum('montant'), 2) }} MAD</strong></p>
                            <a href="{{ route('user.expenses.index', ['categorie_id' => $goal->categorie_id]) }}" class="btn btn-sm btn-outline-primary">
                                Voir toutes les dépenses
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title">Informations détaillées</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="40%">Catégorie</th>
                            <td>
                                <i class="fa {{ $goal->categorie->icone ?? 'fa-tag' }}"></i>
                                {{ $goal->categorie->nom }}
                            </td>
                        </tr>
                        <tr>
                            <th>Type d'objectif</th>
                            <td>
                                <i class="fa {{ $goal->type == 'epargne' ? 'fa-piggy-bank' : 'fa-chart-line' }}"></i> 
                                {{ $goal->type == 'epargne' ? 'Épargne' : 'Réduction' }}
                            </td>
                        </tr>
                        <tr>
                            <th>Période</th>
                            <td>{{ $goal->date_debut->format('d/m/Y') }} - {{ $goal->date_fin->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Durée totale</th>
                            <td>{{ $goal->date_debut->diffInDays($goal->date_fin) + 1 }} jours</td>
                        </tr>
                        <tr>
                            <th>Date de création</th>
                            <td>{{ $goal->created_at->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title">Projections</h5>
                </div>
                <div class="card-body">
                    @php
                        $daysLeft = max(1, now()->diffInDays($goal->date_fin, false));
                        $daysElapsed = max(1, $goal->date_debut->diffInDays(now()));
                        $totalDays = max(1, $goal->date_debut->diffInDays($goal->date_fin));
                        $montantActuel = $goal->montant_actuel ?? 0;
                        
                        if ($goal->type == 'epargne') {
                            // Pour l'épargne, on veut atteindre ou dépasser la cible
                            $dailyRate = $daysElapsed > 0 ? $montantActuel / $daysElapsed : 0;
                            $projectedFinal = $montantActuel + ($dailyRate * $daysLeft);
                            $neededDaily = $daysLeft > 0 ? ($goal->montant_cible - $montantActuel) / $daysLeft : 0;
                        } else {
                            // Pour la réduction, on veut rester en dessous de la cible
                            $dailyRate = $daysElapsed > 0 ? $montantActuel / $daysElapsed : 0;
                            $projectedFinal = $montantActuel + ($dailyRate * $daysLeft);
                            $neededDaily = $daysLeft > 0 ? ($goal->montant_cible - $montantActuel) / $daysLeft : 0;
                        }
                        
                        $onTrack = $goal->type == 'epargne' 
                            ? $projectedFinal >= $goal->montant_cible 
                            : $projectedFinal <= $goal->montant_cible;
                    @endphp
                    
                    <div class="alert alert-{{ $onTrack ? 'success' : 'warning' }}">
                        <strong>
                            {{ $onTrack ? 'Vous êtes sur la bonne voie !' : 'Ajustement nécessaire' }}
                        </strong>
                    </div>
                    
                    <table class="table table-sm">
                        <tr>
                            <th>Rythme actuel</th>
                            <td>{{ number_format($dailyRate, 2) }} MAD/jour</td>
                        </tr>
                        <tr>
                            <th>Projection finale</th>
                            <td>{{ number_format($projectedFinal, 2) }} MAD</td>
                        </tr>
                        @if($goal->type == 'epargne')
                            <tr>
                                <th>À épargner par jour</th>
                                <td>{{ number_format($neededDaily, 2) }} MAD/jour</td>
                            </tr>
                        @else
                            <tr>
                                <th>Limite par jour</th>
                                <td>{{ number_format($neededDaily, 2) }} MAD/jour</td>
                            </tr>
                        @endif
                    </table>
                    
                    <div class="mt-3">
                        <h6>Conseils :</h6>
                        <ul class="text-muted small">
                            @if($goal->type == 'epargne')
                                @if($onTrack)
                                    <li>Continuez à ce rythme, vous êtes sur la bonne voie !</li>
                                    <li>Envisagez d'augmenter votre objectif d'épargne.</li>
                                @else
                                    <li>Essayez d'augmenter vos versements d'épargne pour atteindre votre objectif.</li>
                                    <li>Réduisez temporairement certaines dépenses non essentielles.</li>
                                @endif
                            @else
                                @if($onTrack)
                                    <li>Vous maîtrisez bien vos dépenses, continuez ainsi !</li>
                                    <li>Envisagez de réduire davantage votre limite pour économiser plus.</li>
                                @else
                                    <li>Réduisez vos dépenses dans cette catégorie pour respecter votre objectif.</li>
                                    <li>Identifiez les postes où vous pouvez faire des économies.</li>
                                @endif
                            @endif
                        </ul>
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
                <p>Êtes-vous sûr de vouloir supprimer l'objectif <span class="font-weight-bold">{{ $goal->titre }}</span> ?</p>
            </div>
            <div class="modal-footer">
                <form action="{{ route('user.goals.destroy', $goal->id) }}" method="POST">
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
