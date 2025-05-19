@extends('layouts.user')

@section('title', $budget->nom)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">{{ $budget->nom }}</h1>
            <p class="text-muted">
                Période : {{ $budget->date_debut->format('d/m/Y') }} - {{ $budget->date_fin->format('d/m/Y') }}
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <a href="{{ route('user.expenses.create', ['budget_id' => $budget->id]) }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> Ajouter une dépense
                </a>
                <a href="{{ route('user.budgets.edit', $budget->id) }}" class="btn btn-primary">
                    <i class="fa fa-edit"></i> Modifier
                </a>
                <a href="{{ route('user.budgets.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Résumé du budget -->
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title">Résumé du budget</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="mb-0">{{ number_format($budget->montant_total, 2) }} MAD</h2>
                        <p class="text-muted">Budget total</p>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h4 class="mb-0 text-success">{{ number_format($budget->montant_total - $depensesTotal, 2) }}</h4>
                            <p class="text-muted small">Restant</p>
                        </div>
                        <div class="col-md-4">
                            <h4 class="mb-0 text-info">{{ number_format($depensesTotal, 2) }}</h4>
                            <p class="text-muted small">Dépensé</p>
                        </div>
                        <div class="col-md-4">
                            <h4 class="mb-0 text-warning">{{ $progression }}%</h4>
                            <p class="text-muted small">Utilisé</p>
                        </div>
                    </div>
                    
                    <div class="progress mt-4" style="height: 20px;">
                        <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" 
                            style="width: {{ $progression }}%;" 
                            aria-valuenow="{{ $progression }}" 
                            aria-valuemin="0" 
                            aria-valuemax="100">
                            {{ $progression }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Graphique de répartition -->
        <div class="col-md-8 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title">Répartition du budget</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-7">
                            <canvas id="budget-chart" width="400" height="300"></canvas>
                        </div>
                        <div class="col-md-5">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Montant</th>
                                            <th>%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($categorieBudgets as $cb)
                                        <tr>
                                            <td>
                                                <i class="fa {{ $cb->categorie->icone ?? 'fa-tag' }} text-{{ $colors[$loop->index % count($colors)] }}"></i>
                                                {{ $cb->categorie->nom }}
                                            </td>
                                            <td>{{ number_format($cb->montant_alloue, 2) }}</td>
                                            <td>{{ $cb->pourcentage }}%</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails par catégorie -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Détails par catégorie</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($categorieBudgets as $cb)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-{{ $colors[$loop->index % count($colors)] }}">
                                <div class="card-header bg-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-title mb-0">
                                            <i class="fa {{ $cb->categorie->icone ?? 'fa-tag' }} text-{{ $colors[$loop->index % count($colors)] }}"></i>
                                            {{ $cb->categorie->nom }}
                                        </h6>
                                        <span class="badge badge-{{ $colors[$loop->index % count($colors)] }}">
                                            {{ $cb->pourcentage }}%
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @php
                                        $depensesCategorie = $cb->depensesTotal ?? 0;
                                        $pourcentageCategorie = $cb->montant_alloue > 0 ? min(100, round(($depensesCategorie / $cb->montant_alloue) * 100)) : 0;
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
                                                <h5 class="mb-0">{{ number_format($cb->montant_alloue, 2) }}</h5>
                                                <p class="text-muted small">Alloué</p>
                                            </div>
                                            <div class="col-6">
                                                <h5 class="mb-0">{{ number_format($depensesCategorie, 2) }}</h5>
                                                <p class="text-muted small">Dépensé</p>
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
                                        <h6 class="{{ $cb->montant_alloue - $depensesCategorie >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($cb->montant_alloue - $depensesCategorie, 2) }} MAD
                                        </h6>
                                        <p class="text-muted small">
                                            {{ $cb->montant_alloue - $depensesCategorie >= 0 ? 'Restant' : 'Dépassement' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="{{ route('user.expenses.index', ['categorie_budget_id' => $cb->id]) }}" class="btn btn-sm btn-outline-secondary btn-block">
                                        <i class="fa fa-list"></i> Voir les dépenses
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dernières dépenses -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Dernières dépenses</h5>
                    <a href="{{ route('user.expenses.index', ['budget_id' => $budget->id]) }}" class="btn btn-sm btn-outline-primary">
                        Voir toutes les dépenses
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Catégorie</th>
                                    <th>Montant</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentExpenses as $expense)
                                    <tr>
                                        <td>{{ $expense->date_depense->format('d/m/Y') }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td>
                                            <span class="badge badge-light">
                                                <i class="fa {{ $expense->categorieBudget->categorie->icone ?? 'fa-tag' }}"></i>
                                                {{ $expense->categorieBudget->categorie->nom }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($expense->montant, 2) }} MAD</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('user.expenses.edit', $expense->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-expense-btn" data-toggle="modal" data-target="#deleteExpenseModal" data-id="{{ $expense->id }}" data-description="{{ $expense->description }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fa fa-receipt fa-3x text-muted mb-3"></i>
                                                <h6 class="text-muted">Aucune dépense enregistrée</h6>
                                                <a href="{{ route('user.expenses.create', ['budget_id' => $budget->id]) }}" class="btn btn-sm btn-success mt-3">
                                                    <i class="fa fa-plus"></i> Enregistrer une dépense
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de suppression de dépense -->
<div class="modal fade" id="deleteExpenseModal" tabindex="-1" role="dialog" aria-labelledby="deleteExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteExpenseModalLabel">Confirmer la suppression</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la dépense <span id="expense-description" class="font-weight-bold"></span> ?</p>
            </div>
            <div class="modal-footer">
                <form id="delete-expense-form" action="" method="POST">
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Configuration du graphique
        const ctx = document.getElementById('budget-chart').getContext('2d');
        
        const budgetData = {
            labels: {!! json_encode($categorieBudgets->pluck('categorie.nom')) !!},
            datasets: [{
                data: {!! json_encode($categorieBudgets->pluck('montant_alloue')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(199, 199, 199, 0.7)',
                    'rgba(83, 102, 255, 0.7)',
                    'rgba(40, 159, 64, 0.7)',
                    'rgba(210, 199, 199, 0.7)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(199, 199, 199, 1)',
                    'rgba(83, 102, 255, 1)',
                    'rgba(40, 159, 64, 1)',
                    'rgba(210, 199, 199, 1)',
                ],
                borderWidth: 1
            }]
        };
        
        const budgetChart = new Chart(ctx, {
            type: 'doughnut',
            data: budgetData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('fr-FR', { 
                                    style: 'currency', 
                                    currency: 'MAD',
                                    minimumFractionDigits: 2
                                }).format(context.raw);
                                return label;
                            }
                        }
                    }
                }
            }
        });
        
        // Configuration de la suppression de dépense
        $('.delete-expense-btn').click(function() {
            const id = $(this).data('id');
            const description = $(this).data('description');
            
            $('#expense-description').text(description);
            $('#delete-expense-form').attr('action', '/user/expenses/' + id);
        });
    });
</script>
@endsection