@extends('layouts.user')

@section('title', 'Détail de la dépense')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Détail de la dépense</h1>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <a href="{{ route('user.expenses.edit', $expense->id) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> Modifier
                </a>
                <a href="{{ route('user.expenses.index') }}" class="btn btn-secondary">
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
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Informations sur la dépense</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Description</th>
                                    <td>{{ $expense->description }}</td>
                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $expense->date_depense->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Montant</th>
                                    <td>
                                        <span class="font-weight-bold">{{ number_format($expense->montant, 2) }} MAD</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td>
                                        @php
                                            $statusClass = 'success';
                                            if($expense->statut == 'en attente') {
                                                $statusClass = 'warning';
                                            } elseif($expense->statut == 'annulée') {
                                                $statusClass = 'danger';
                                            }
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }}">
                                            {{ $expense->statut }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Budget</th>
                                    <td>
                                        <a href="{{ route('user.budgets.show', $expense->categorieBudget->budget->id) }}">
                                            {{ $expense->categorieBudget->budget->nom }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Période</th>
                                    <td>
                                        {{ $expense->categorieBudget->budget->date_debut->format('d/m/Y') }} - 
                                        {{ $expense->categorieBudget->budget->date_fin->format('d/m/Y') }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Catégorie</th>
                                    <td>
                                        <span class="badge badge-light">
                                            <i class="fa {{ $expense->categorieBudget->categorie->icone ?? 'fa-tag' }}"></i>
                                            {{ $expense->categorieBudget->categorie->nom }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Date de création</th>
                                    <td>{{ $expense->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">État du budget pour cette catégorie</h5>
                </div>
                <div class="card-body">
                    @php
                        $categorieBudget = $expense->categorieBudget;
                        $depensesCategorie = $categorieBudget->depensesTotal ?? 0;
                        $pourcentageCategorie = $categorieBudget->montant_alloue > 0 ? min(100, round(($depensesCategorie / $categorieBudget->montant_alloue) * 100)) : 0;
                        $categorieProgressClass = 'success';
                        
                        if ($pourcentageCategorie >= 90) {
                            $categorieProgressClass = 'danger';
                        } elseif ($pourcentageCategorie >= 75) {
                            $categorieProgressClass = 'warning';
                        }
                    @endphp
                    
                    <div class="text-center mb-3">
                        <div class="row">
                            <div class="col-6">
                                <h5 class="mb-0">{{ number_format($categorieBudget->montant_alloue, 2) }}</h5>
                                <p class="text-muted small">Budget alloué</p>
                            </div>
                            <div class="col-6">
                                <h5 class="mb-0">{{ number_format($depensesCategorie, 2) }}</h5>
                                <p class="text-muted small">Total dépensé</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="progress" style="height: 15px;">
                        <div class="progress-bar bg-{{ $categorieProgressClass }}" role="progressbar" 
                            style="width: {{ $pourcentageCategorie }}%;" 
                            aria-valuenow="{{ $pourcentageCategorie }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                            {{ $pourcentageCategorie }}%
                        </div>
                    </div>
                    
                    <div class="mt-3 text-center">
                        <h6 class="{{ $categorieBudget->montant_alloue - $depensesCategorie >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($categorieBudget->montant_alloue - $depensesCategorie, 2) }} MAD
                        </h6>
                        <p class="text-muted small">
                            {{ $categorieBudget->montant_alloue - $depensesCategorie >= 0 ? 'Restant' : 'Dépassement' }}
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('user.expenses.index', ['categorie_budget_id' => $categorieBudget->id]) }}" class="btn btn-sm btn-outline-secondary btn-block">
                            <i class="fa fa-list"></i> Voir toutes les dépenses de cette catégorie
                        </a>
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
                <p>Êtes-vous sûr de vouloir supprimer la dépense <span class="font-weight-bold">{{ $expense->description }}</span> ?</p>
            </div>
            <div class="modal-footer">
                               <form action="{{ route('user.expenses.destroy', $expense->id) }}" method="POST">
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