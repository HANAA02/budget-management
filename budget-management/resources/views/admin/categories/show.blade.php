@extends('layouts.admin')

@section('title', 'Détails de la catégorie')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de la catégorie</h1>
        <div>
            <a href="{{ route('admin.categories.edit', $category->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Modifier
            </a>
            <a href="{{ route('admin.categories.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Retour à la liste
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Informations générales -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="{{ route('admin.categories.edit', $category->id) }}">Modifier</a>
                            <button class="dropdown-item text-danger" type="button" data-toggle="modal" data-target="#deleteModal">Supprimer</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-4">
                        <div class="col-auto mr-3">
                            <div class="icon-circle bg-primary text-white">
                                <i class="fas fa-{{ $category->icone ?? 'tag' }}"></i>
                            </div>
                        </div>
                        <div class="col">
                            <h4 class="font-weight-bold text-gray-800">{{ $category->nom }}</h4>
                            <div class="badge {{ $category->active ? 'badge-success' : 'badge-secondary' }} mb-2">
                                {{ $category->active ? 'Active' : 'Inactive' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="font-weight-bold">Description:</h6>
                        <p>{{ $category->description ?? 'Aucune description disponible.' }}</p>
                    </div>
                    
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 40%;">Pourcentage par défaut</th>
                                <td>{{ $category->pourcentage_defaut }}%</td>
                            </tr>
                            <tr>
                                <th>Date de création</th>
                                <td>{{ $category->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Dernière modification</th>
                                <td>{{ $category->updated_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Afficher dans les rapports</th>
                                <td>
                                    @if($category->show_in_reports)
                                        <span class="text-success"><i class="fas fa-check-circle"></i> Oui</span>
                                    @else
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Sous-catégories autorisées</th>
                                <td>
                                    @if($category->allow_subcategories)
                                        <span class="text-success"><i class="fas fa-check-circle"></i> Oui</span>
                                    @else
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Non</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Couleur</th>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="color-sample mr-2" style="background-color: {{ $category->couleur ?? '#4e73df' }}"></div>
                                        {{ $category->couleur ?? '#4e73df' }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistiques d'utilisation -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques d'utilisation</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-primary h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Budgets associés</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $budgetsCount }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-success h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Dépenses associées</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expensesCount }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graphique utilisation -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold mb-3">Répartition des dépenses sur 6 mois</h6>
                        <div class="chart-area">
                            <canvas id="expensesChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Pourcentage moyen d'utilisation -->
                    <div class="mb-4">
                        <h6 class="font-weight-bold mb-2">Pourcentage moyen d'utilisation budgétaire</h6>
                        <div class="progress mb-2">
                            <div class="progress-bar" role="progressbar" style="width: {{ $averageUsagePercentage }}%"
                                 aria-valuenow="{{ $averageUsagePercentage }}" aria-valuemin="0" aria-valuemax="100">
                                {{ $averageUsagePercentage }}%
                            </div>
                        </div>
                        <p class="text-sm text-muted">Pourcentage moyen d'utilisation du budget alloué à cette catégorie.</p>
                    </div>
                    
                    <!-- Top utilisateurs -->
                    <div>
                        <h6 class="font-weight-bold mb-2">Top 5 utilisateurs par dépenses</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Dépenses</th>
                                        <th>Montant total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->expenses_count }}</td>
                                        <td>{{ number_format($user->total_amount, 2) }} MAD</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Aucune donnée disponible</td>
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

    <!-- Budgets et dépenses récentes -->
    <div class="row">
        <!-- Budgets récents -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Budgets récents avec cette catégorie</h6>
                </div>
                <div class="card-body">
                    @if(count($recentBudgets) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Utilisateur</th>
                                        <th>Période</th>
                                        <th>Montant alloué</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBudgets as $budget)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.budgets.show', $budget->id) }}">
                                                {{ $budget->nom }}
                                            </a>
                                        </td>
                                        <td>{{ $budget->user->name }}</td>
                                        <td>{{ $budget->date_debut->format('d/m/Y') }} - {{ $budget->date_fin->format('d/m/Y') }}</td>
                                        <td>{{ number_format($budget->pivot->montant_alloue, 2) }} MAD</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Aucun budget récent trouvé pour cette catégorie</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Dépenses récentes -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Dépenses récentes dans cette catégorie</h6>
                </div>
                <div class="card-body">
                    @if(count($recentExpenses) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Utilisateur</th>
                                        <th>Date</th>
                                        <th>Montant</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentExpenses as $expense)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.expenses.show', $expense->id) }}">
                                                {{ Str::limit($expense->description, 30) }}
                                            </a>
                                        </td>
                                        <td>{{ $expense->user->name }}</td>
                                        <td>{{ $expense->date_depense->format('d/m/Y') }}</td>
                                        <td>{{ number_format($expense->montant, 2) }} MAD</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Aucune dépense récente trouvée pour cette catégorie</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
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
                <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ $category->nom }}</strong> ?</p>
                <p class="text-danger">Cette action est irréversible et pourrait affecter les budgets et dépenses associés à cette catégorie.</p>
                
                @if($budgetsCount > 0 || $expensesCount > 0)
                    <div class="alert alert-danger">
                        <h6 class="font-weight-bold">Attention !</h6>
                        <p class="mb-0">Cette catégorie est actuellement utilisée par :</p>
                        <ul class="mb-0">
                            @if($budgetsCount > 0)
                                <li>{{ $budgetsCount }} budget(s)</li>
                            @endif
                            @if($expensesCount > 0)
                                <li>{{ $expensesCount }} dépense(s)</li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .icon-circle {
        height: 60px;
        width: 60px;
        border-radius: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .color-sample {
        width: 25px;
        height: 25px;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialisation du graphique d'évolution des dépenses
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("expensesChart");
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($expensesChartData['labels']) !!},
                datasets: [{
                    label: "Dépenses",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: {!! json_encode($expensesChartData['data']) !!},
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value, index, values) {
                                return value + ' MAD';
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': ' + tooltipItem.yLabel + ' MAD';
                        }
                    }
                }
            }
        });
    });
</script>
@endpush