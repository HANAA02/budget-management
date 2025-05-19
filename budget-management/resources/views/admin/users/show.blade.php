@extends('layouts.admin')

@section('title', 'Détails utilisateur')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Détails de l'utilisateur</h1>
        <div>
            <a href="{{ route('admin.users.edit', $user->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
                <i class="fas fa-edit fa-sm text-white-50"></i> Modifier
            </a>
            <a href="{{ route('admin.users.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
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
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Actions:</div>
                            <a class="dropdown-item" href="{{ route('admin.users.edit', $user->id) }}">Modifier</a>
                            <a class="dropdown-item text-primary" href="{{ route('admin.password.reset', $user->id) }}">Réinitialiser le mot de passe</a>
                            <button class="dropdown-item text-danger" type="button" data-toggle="modal" data-target="#deleteModal">Supprimer</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="img-profile rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                            <div class="avatar-placeholder rounded-circle mx-auto">
                                {{ substr($user->nom, 0, 1) . substr($user->prenom, 0, 1) }}
                            </div>
                        @endif
                        <h4 class="mt-3 font-weight-bold">{{ $user->nom }} {{ $user->prenom }}</h4>
                        <div class="badge {{ $user->is_active ? 'badge-success' : 'badge-secondary' }} mb-2">
                            {{ $user->is_active ? 'Actif' : 'Inactif' }}
                        </div>
                        <div class="badge {{ $user->role === 'admin' ? 'badge-danger' : 'badge-primary' }}">
                            {{ $user->role === 'admin' ? 'Administrateur' : 'Utilisateur standard' }}
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="info-group mb-3">
                        <p class="mb-1"><strong><i class="fas fa-envelope mr-2"></i> Email:</strong></p>
                        <p>{{ $user->email }}
                            @if($user->email_verified_at)
                                <i class="fas fa-check-circle text-success ml-2" title="Email vérifié"></i>
                            @else
                                <i class="fas fa-times-circle text-danger ml-2" title="Email non vérifié"></i>
                            @endif
                        </p>
                    </div>
                    
                    <div class="info-group mb-3">
                        <p class="mb-1"><strong><i class="fas fa-phone mr-2"></i> Téléphone:</strong></p>
                        <p>{{ $user->telephone ?? 'Non renseigné' }}</p>
                    </div>
                    
                    <div class="info-group mb-3">
                        <p class="mb-1"><strong><i class="fas fa-calendar-alt mr-2"></i> Inscrit le:</strong></p>
                        <p>{{ $user->date_creation->format('d/m/Y à H:i') }}</p>
                    </div>
                    
                    <div class="info-group mb-3">
                        <p class="mb-1"><strong><i class="fas fa-sign-in-alt mr-2"></i> Dernière connexion:</strong></p>
                        <p>{{ $user->dernier_login ? $user->dernier_login->format('d/m/Y à H:i') : 'Jamais connecté' }}</p>
                    </div>
                    
                    <div class="info-group mb-3">
                        <p class="mb-1"><strong><i class="fas fa-globe mr-2"></i> Langue:</strong></p>
                        <p>
                            @if($user->langue == 'fr')
                                <img src="{{ asset('img/flags/fr.png') }}" alt="Français" class="flag-icon mr-1" style="width: 20px;"> Français
                            @elseif($user->langue == 'en')
                                <img src="{{ asset('img/flags/gb.png') }}" alt="Anglais" class="flag-icon mr-1" style="width: 20px;"> Anglais
                            @elseif($user->langue == 'ar')
                                <img src="{{ asset('img/flags/ma.png') }}" alt="Arabe" class="flag-icon mr-1" style="width: 20px;"> Arabe
                            @else
                                {{ $user->langue ?: 'Non définie' }}
                            @endif
                        </p>
                    </div>
                    
                    <div class="info-group mb-3">
                        <p class="mb-1"><strong><i class="fas fa-money-bill-wave mr-2"></i> Devise par défaut:</strong></p>
                        <p>{{ $user->devise_defaut ?? 'MAD' }}</p>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm mr-2">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Préférences et paramètres -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Préférences et paramètres</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Notifications par email
                            <span>
                                @if($user->notifications_enabled)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Thème préféré
                            <span class="badge badge-primary badge-pill">{{ ucfirst($user->theme ?? 'light') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Multi-facteur
                            <span>
                                @if($user->two_factor_enabled)
                                    <i class="fas fa-check-circle text-success"></i>
                                @else
                                    <i class="fas fa-times-circle text-danger"></i>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Résumé de l'activité -->
        <div class="col-lg-8">
            <!-- Statistiques -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques d'utilisation</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Comptes</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userData['comptes_count'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Budgets</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userData['budgets_count'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Dépenses</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userData['depenses_count'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Objectifs</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userData['objectifs_count'] }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graphique d'activité -->
                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Évolution des dépenses</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="expensesChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Répartition par catégories</h6>
                                </div>
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="categoriesChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        @foreach($userData['categories_top5'] as $index => $category)
                                            <span class="mr-2">
                                                <i class="fas fa-circle" style="color: {{ $chartColors[$index % count($chartColors)] }};"></i> {{ $category->nom }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Activités récentes -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Activités récentes</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Filtrer par:</div>
                            <a class="dropdown-item" href="#">Toutes les activités</a>
                            <a class="dropdown-item" href="#">Dépenses</a>
                            <a class="dropdown-item" href="#">Budgets</a>
                            <a class="dropdown-item" href="#">Revenus</a>
                            <a class="dropdown-item" href="#">Connexions</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @forelse($recentActivities as $activity)
                            <div class="timeline-item">
                                <div class="timeline-item-marker">
                                    <div class="timeline-item-marker-text">{{ $activity->created_at->format('d/m') }}</div>
                                    <div class="timeline-item-marker-indicator bg-{{ $activity->type_color }}">
                                        <i class="fas fa-{{ $activity->icon }}"></i>
                                    </div>
                                </div>
                                <div class="timeline-item-content">
                                    <p class="timeline-item-title">{{ $activity->title }}</p>
                                    <p class="text-sm text-muted mb-0">{{ $activity->description }}</p>
                                    <p class="text-xs text-muted">{{ $activity->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">Aucune activité récente</p>
                            </div>
                        @endforelse
                    </div>
                    
                    @if(count($recentActivities) > 0)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.users.activities', $user->id) }}" class="btn btn-sm btn-primary">
                                Voir toutes les activités
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Comptes et budgets -->
            <div class="row">
                <!-- Comptes -->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Comptes</h6>
                        </div>
                        <div class="card-body">
                            @if(count($userData['comptes']) > 0)
                                <div class="list-group">
                                    @foreach($userData['comptes'] as $compte)
                                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">{{ $compte->nom }}</h5>
                                                <small>{{ $compte->devise }}</small>
                                            </div>
                                            <p class="mb-1 font-weight-bold {{ $compte->solde >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($compte->solde, 2) }} {{ $compte->devise }}
                                            </p>
                                            <small class="text-muted">Créé le {{ $compte->date_creation->format('d/m/Y') }}</small>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">Aucun compte</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Budgets actifs -->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Budgets actifs</h6>
                        </div>
                        <div class="card-body">
                            @if(count($userData['budgets_actifs']) > 0)
                                <div class="list-group">
                                    @foreach($userData['budgets_actifs'] as $budget)
                                        <div class="list-group-item list-group-item-action flex-column align-items-start">
                                            <div class="d-flex w-100 justify-content-between">
                                                <h5 class="mb-1">{{ $budget->nom }}</h5>
                                                <small>{{ $budget->date_debut->format('d/m/Y') }} - {{ $budget->date_fin->format('d/m/Y') }}</small>
                                            </div>
                                            <div class="progress mt-2 mb-2">
                                                <div class="progress-bar bg-{{ $budget->pourcentage_utilisation > 90 ? 'danger' : ($budget->pourcentage_utilisation > 70 ? 'warning' : 'success') }}" 
                                                     role="progressbar" style="width: {{ $budget->pourcentage_utilisation }}%"
                                                     aria-valuenow="{{ $budget->pourcentage_utilisation }}" aria-valuemin="0" aria-valuemax="100">
                                                    {{ $budget->pourcentage_utilisation }}%
                                                </div>
                                            </div>
                                            <p class="mb-1">
                                                <strong>{{ number_format($budget->montant_depense, 2) }}</strong> / {{ number_format($budget->montant_total, 2) }} {{ $budget->devise }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <p class="text-muted mb-0">Aucun budget actif</p>
                                </div>
                            @endif
                        </div>
                    </div>
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
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong>{{ $user->nom }} {{ $user->prenom }}</strong> ?</p>
                <p class="text-danger">Cette action est irréversible et supprimera toutes les données associées à cet utilisateur (budgets, dépenses, comptes, etc.).</p>
                
                @if($userData['comptes_count'] > 0 || $userData['budgets_count'] > 0 || $userData['depenses_count'] > 0)
                    <div class="alert alert-danger">
                        <h6 class="font-weight-bold">Attention !</h6>
                        <p class="mb-0">Cet utilisateur possède :</p>
                        <ul class="mb-0">
                            @if($userData['comptes_count'] > 0)
                                <li>{{ $userData['comptes_count'] }} compte(s)</li>
                            @endif
                            @if($userData['budgets_count'] > 0)
                                <li>{{ $userData['budgets_count'] }} budget(s)</li>
                            @endif
                            @if($userData['depenses_count'] > 0)
                                <li>{{ $userData['depenses_count'] }} dépense(s)</li>
                            @endif
                            @if($userData['objectifs_count'] > 0)
                                <li>{{ $userData['objectifs_count'] }} objectif(s)</li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
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
    .avatar-placeholder {
        width: 150px;
        height: 150px;
        background-color: #4e73df;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: bold;
    }
    
    .timeline {
        position: relative;
        padding-left: 1rem;
        margin: 1rem 0;
        margin-left: 1.5rem;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    
    .timeline-item-marker {
        position: absolute;
        left: -1.5rem;
        width: 1.5rem;
    }
    
    .timeline-item-marker-text {
        position: absolute;
        top: 0;
        left: -3rem;
        width: 2rem;
        text-align: right;
        font-size: 0.8rem;
        font-weight: 700;
    }
    
    .timeline-item-marker-indicator {
        position: absolute;
        top: 0;
        left: -0.75rem;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 1.5rem;
        height: 1.5rem;
        border-radius: 100%;
        color: #fff;
        font-size: 0.8rem;
    }
    
    .timeline-item-content {
        padding-left: 1.5rem;
        padding-right: 0.5rem;
        padding-bottom: 1.5rem;
        border-left: 1px solid #ddd;
    }
    
    .timeline-item:last-child .timeline-item-content {
        border-left-color: transparent;
        padding-bottom: 0;
    }
    
    .timeline-item-title {
        margin-bottom: 0.25rem;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialisation des graphiques
    document.addEventListener('DOMContentLoaded', function() {
        // Graphique des dépenses
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

        // Graphique des catégories
        var ctx2 = document.getElementById("categoriesChart");
        var myPieChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoriesChartData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($categoriesChartData['data']) !!},
                    backgroundColor: {!! json_encode($chartColors) !!},
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: false
                },
                cutoutPercentage: 80,
            },
        });
    });
</script>
@endpush