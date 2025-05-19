@extends('layouts.user')

@section('title', $report->titre)

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">{{ $report->titre }}</h1>
            <p class="text-muted">
                Généré le {{ $report->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <a href="{{ route('user.reports.download', ['id' => $report->id, 'format' => 'pdf']) }}" class="btn btn-primary">
                    <i class="fa fa-file-pdf"></i> Télécharger PDF
                </a>
                <a href="{{ route('user.reports.download', ['id' => $report->id, 'format' => 'csv']) }}" class="btn btn-secondary">
                    <i class="fa fa-file-csv"></i> Exporter CSV
                </a>
                <a href="{{ route('user.reports.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Résumé</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center">
                                    <h2 class="mb-0">{{ number_format($report->data['totaux']['budget'] ?? 0, 2) }}</h2>
                                    <p class="text-muted">Budget total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center">
                                    <h2 class="mb-0">{{ number_format($report->data['totaux']['depenses'] ?? 0, 2) }}</h2>
                                    <p class="text-muted">Dépenses totales</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center">
                                    <h2 class="mb-0">{{ number_format($report->data['totaux']['revenus'] ?? 0, 2) }}</h2>
                                    <p class="text-muted">Revenus totaux</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body text-center">
                                    <h2 class="mb-0">{{ number_format(($report->data['totaux']['revenus'] ?? 0) - ($report->data['totaux']['depenses'] ?? 0), 2) }}</h2>
                                    <p class="text-muted">Solde net</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($report->data['graphiques'] ?? [])
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Graphiques</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($report->data['graphiques']['repartition_categories']))
                        <div class="col-md-6 mb-4">
                            <h6 class="text-center">Répartition par catégorie</h6>
                            <canvas id="chart-categories" width="400" height="300"></canvas>
                        </div>
                        @endif
                        
                        @if(isset($report->data['graphiques']['evolution_depenses']))
                        <div class="col-md-6 mb-4">
                            <h6 class="text-center">Évolution des dépenses</h6>
                            <canvas id="chart-evolution" width="400" height="300"></canvas>
                        </div>
                        @endif
                        
                        @if(isset($report->data['graphiques']['budget_vs_depenses']))
                        <div class="col-md-6 mb-4">
                            <h6 class="text-center">Budget vs Dépenses par catégorie</h6>
                            <canvas id="chart-budget-vs-depenses" width="400" height="300"></canvas>
                        </div>
                        @endif
                        
                        @if(isset($report->data['graphiques']['revenus_par_source']))
                        <div class="col-md-6 mb-4">
                            <h6 class="text-center">Revenus par source</h6>
                            <canvas id="chart-revenus" width="400" height="300"></canvas>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($report->type == 'budget' || $report->type == 'complet')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Détails du budget</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th>Budget alloué</th>
                                    <th>Dépenses</th>
                                    <th>Pourcentage utilisé</th>
                                    <th>Restant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report->data['details']['categories'] ?? [] as $categorie)
                                    <tr>
                                        <td>{{ $categorie['nom'] }}</td>
                                        <td>{{ number_format($categorie['budget_alloue'], 2) }} MAD</td>
                                        <td>{{ number_format($categorie['depenses'], 2) }} MAD</td>
                                        <td>
                                            @php
                                                $pourcentage = $categorie['budget_alloue'] > 0 ? min(100, round(($categorie['depenses'] / $categorie['budget_alloue']) * 100)) : 0;
                                                $progressClass = 'success';
                                                
                                                if ($pourcentage >= 90) {
                                                    $progressClass = 'danger';
                                                } elseif ($pourcentage >= 75) {
                                                    $progressClass = 'warning';
                                                }
                                            @endphp
                                            <div class="progress" style="height: 15px;">
                                                <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" 
                                                    style="width: {{ $pourcentage }}%;" 
                                                    aria-valuenow="{{ $pourcentage }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                    {{ $pourcentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="{{ $categorie['budget_alloue'] - $categorie['depenses'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($categorie['budget_alloue'] - $categorie['depenses'], 2) }} MAD
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td>Total</td>
                                    <td>{{ number_format($report->data['totaux']['budget'] ?? 0, 2) }} MAD</td>
                                    <td>{{ number_format($report->data['totaux']['depenses'] ?? 0, 2) }} MAD</td>
                                    <td>
                                        @php
                                            $totalPourcentage = $report->data['totaux']['budget'] > 0 ? min(100, round(($report->data['totaux']['depenses'] / $report->data['totaux']['budget']) * 100)) : 0;
                                            $totalProgressClass = 'success';
                                            
                                            if ($totalPourcentage >= 90) {
                                                $totalProgressClass = 'danger';
                                            } elseif ($totalPourcentage >= 75) {
                                                $totalProgressClass = 'warning';
                                            }
                                        @endphp
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-{{ $totalProgressClass }}" role="progressbar" 
                                                style="width: {{ $totalPourcentage }}%;" 
                                                aria-valuenow="{{ $totalPourcentage }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                {{ $totalPourcentage }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="{{ $report->data['totaux']['budget'] - $report->data['totaux']['depenses'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($report->data['totaux']['budget'] - $report->data['totaux']['depenses'], 2) }} MAD
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($report->type == 'depenses' || $report->type == 'complet')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Détails des dépenses</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Catégorie</th>
                                    <th>Budget</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report->data['details']['depenses'] ?? [] as $depense)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($depense['date'])->format('d/m/Y') }}</td>
                                        <td>{{ $depense['description'] }}</td>
                                        <td>{{ $depense['categorie'] }}</td>
                                        <td>{{ $depense['budget'] }}</td>
                                        <td>{{ number_format($depense['montant'], 2) }} MAD</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="4">Total</td>
                                    <td>{{ number_format($report->data['totaux']['depenses'] ?? 0, 2) }} MAD</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($report->type == 'revenus' || $report->type == 'complet')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Détails des revenus</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Source</th>
                                    <th>Compte</th>
                                    <th>Périodicité</th>
                                    <th>Montant</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report->data['details']['revenus'] ?? [] as $revenu)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($revenu['date'])->format('d/m/Y') }}</td>
                                        <td>{{ $revenu['source'] }}</td>
                                        <td>{{ $revenu['compte'] }}</td>
                                        <td>{{ $revenu['periodicite'] }}</td>
                                        <td>{{ number_format($revenu['montant'], 2) }} {{ $revenu['devise'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="4">Total</td>
                                    <td>{{ number_format($report->data['totaux']['revenus'] ?? 0, 2) }} MAD</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($report->type == 'objectifs' || $report->type == 'complet')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Progrès des objectifs</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Objectif</th>
                                    <th>Catégorie</th>
                                    <th>Type</th>
                                    <th>Montant cible</th>
                                    <th>Progression</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report->data['details']['objectifs'] ?? [] as $objectif)
                                    <tr>
                                        <td>{{ $objectif['titre'] }}</td>
                                        <td>{{ $objectif['categorie'] }}</td>
                                        <td>{{ $objectif['type'] == 'epargne' ? 'Épargne' : 'Réduction' }}</td>
                                        <td>{{ number_format($objectif['montant_cible'], 2) }} MAD</td>
                                        <td>
                                            @php
                                                $pourcentage = $objectif['progression'] ?? 0;
                                                $progressClass = 'success';
                                                
                                                if ($objectif['type'] == 'epargne') {
                                                    if ($pourcentage < 25) {
                                                        $progressClass = 'danger';
                                                    } elseif ($pourcentage < 75) {
                                                        $progressClass = 'warning';
                                                    }
                                                } else { // réduction
                                                    if ($pourcentage > 90) {
                                                        $progressClass = 'danger';
                                                    } elseif ($pourcentage > 75) {
                                                        $progressClass = 'warning';
                                                    }
                                                }
                                            @endphp
                                            <div class="progress" style="height: 15px;">
                                                <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" 
                                                    style="width: {{ $pourcentage }}%;" 
                                                    aria-valuenow="{{ $pourcentage }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                    {{ $pourcentage }}%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = 'primary';
                                                if ($objectif['statut'] == 'atteint') {
                                                    $statusClass = 'success';
                                                } elseif ($objectif['statut'] == 'annulé') {
                                                    $statusClass = 'danger';
                                                }
                                            @endphp
                                            <span class="badge badge-{{ $statusClass }}">
                                                {{ $objectif['statut'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($report->data['tendances'] ?? false)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Analyse des tendances</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Tendances générales</h6>
                            <ul>
                                @foreach($report->data['tendances']['generales'] ?? [] as $tendance)
                                    <li>{{ $tendance }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Catégories en hausse</h6>
                            <ul class="text-danger">
                                @foreach($report->data['tendances']['categories_hausse'] ?? [] as $categorie)
                                    <li>{{ $categorie['nom'] }} : +{{ $categorie['pourcentage'] }}%</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Catégories en baisse</h6>
                            <ul class="text-success">
                                @foreach($report->data['tendances']['categories_baisse'] ?? [] as $categorie)
                                    <li>{{ $categorie['nom'] }} : -{{ $categorie['pourcentage'] }}%</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($report->data['recommandations'] ?? false)
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Recommandations</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Conseils généraux</h6>
                            <ul>
                                @foreach($report->data['recommandations']['generales'] ?? [] as $recommandation)
                                    <li>{{ $recommandation }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6>Recommandations spécifiques</h6>
                            <ul>
                                @foreach($report->data['recommandations']['specifiques'] ?? [] as $recommandation)
                                    <li>{{ $recommandation }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> 
                                <strong>Économies potentielles :</strong> 
                                En suivant ces recommandations, vous pourriez économiser environ 
                                {{ number_format($report->data['recommandations']['economie_potentielle'] ?? 0, 2) }} MAD.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        @if(isset($report->data['graphiques']['repartition_categories']))
        // Graphique de répartition par catégorie
        const ctxCategories = document.getElementById('chart-categories').getContext('2d');
        const categoriesData = @json($report->data['graphiques']['repartition_categories']);
        
        new Chart(ctxCategories, {
            type: 'doughnut',
            data: {
                labels: categoriesData.labels,
                datasets: [{
                    data: categoriesData.data,
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
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        @endif
        
        @if(isset($report->data['graphiques']['evolution_depenses']))
        // Graphique d'évolution des dépenses
        const ctxEvolution = document.getElementById('chart-evolution').getContext('2d');
        const evolutionData = @json($report->data['graphiques']['evolution_depenses']);
        
        new Chart(ctxEvolution, {
            type: 'line',
            data: {
                labels: evolutionData.labels,
                datasets: [{
                    label: 'Dépenses',
                    data: evolutionData.data,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        @endif
        
        @if(isset($report->data['graphiques']['budget_vs_depenses']))
        // Graphique Budget vs Dépenses
        const ctxBudgetVsDepenses = document.getElementById('chart-budget-vs-depenses').getContext('2d');
        const budgetVsDepensesData = @json($report->data['graphiques']['budget_vs_depenses']);
        
        new Chart(ctxBudgetVsDepenses, {
            type: 'bar',
            data: {
                labels: budgetVsDepensesData.labels,
                datasets: [
                    {
                        label: 'Budget',
                        data: budgetVsDepensesData.budget,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Dépenses',
                        data: budgetVsDepensesData.depenses,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        @endif
        
        @if(isset($report->data['graphiques']['revenus_par_source']))
        // Graphique Revenus par source
        const ctxRevenus = document.getElementById('chart-revenus').getContext('2d');
        const revenusData = @json($report->data['graphiques']['revenus_par_source']);
        
        new Chart(ctxRevenus, {
            type: 'pie',
            data: {
                labels: revenusData.labels,
                datasets: [{
                    data: revenusData.data,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection