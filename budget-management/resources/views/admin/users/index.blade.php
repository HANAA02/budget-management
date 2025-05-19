@extends('layouts.admin')

@section('title', 'Liste des utilisateurs')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des utilisateurs</h1>
        <a href="{{ route('admin.users.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user-plus fa-sm text-white-50"></i> Ajouter un utilisateur
        </a>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row">
        <!-- Total des utilisateurs -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des utilisateurs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Utilisateurs actifs -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Utilisateurs actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nouveaux utilisateurs (30 jours) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Nouveaux (30 jours)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $newUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Administrateurs -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Administrateurs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $adminUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des utilisateurs</h6>
            
            <!-- Filtre et recherche -->
            <div class="input-group" style="width: 300px;">
                <input type="text" id="searchInput" class="form-control bg-light border-0 small" placeholder="Rechercher un utilisateur..." 
                    aria-label="Rechercher" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Date d'inscription</th>
                            <th>Dernière connexion</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar mr-2">
                                        @if($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="rounded-circle" width="32" height="32">
                                        @else
                                            <div class="avatar-initials rounded-circle">
                                                {{ substr($user->nom, 0, 1) . substr($user->prenom, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    {{ $user->nom }} {{ $user->prenom }}
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge badge-danger">Administrateur</span>
                                @else
                                    <span class="badge badge-primary">Utilisateur</span>
                                @endif
                            </td>
                            <td>{{ $user->date_creation->format('d/m/Y') }}</td>
                            <td>
                                @if($user->dernier_login)
                                    {{ $user->dernier_login->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">Jamais connecté</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge badge-success">Actif</span>
                                @else
                                    <span class="badge badge-secondary">Inactif</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-sm" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" title="Supprimer" 
                                            onclick="confirmDelete({{ $user->id }}, '{{ $user->nom }} {{ $user->prenom }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Aucun utilisateur trouvé</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- Analyse des utilisateurs -->
    <div class="row">
        <!-- Évolution des inscriptions -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Évolution des inscriptions</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="userRegistrationsChart"></canvas>
                    </div>
                    <hr>
                    <p class="small text-muted">Cette visualisation montre l'évolution du nombre d'inscriptions au fil du temps.</p>
                </div>
            </div>
        </div>

        <!-- Répartition par activité -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition par activité</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="userActivityChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Très actifs
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Actifs
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Peu actifs
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Inactifs
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Jamais connectés
                        </span>
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
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <span id="userName" class="font-weight-bold"></span> ?</p>
                <p class="text-danger">Cette action est irréversible et supprimera toutes les données associées à cet utilisateur (budgets, dépenses, comptes, etc.).</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <form id="deleteForm" action="" method="POST">
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
    .avatar-initials {
        width: 32px;
        height: 32px;
        background-color: #4e73df;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialisation de DataTables
    $(document).ready(function() {
        var table = $('#usersTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
            },
            "pageLength": 10,
            "order": [[0, "desc"]],
            "searching": true,
            "dom": '<"top"f>rt<"bottom"ip><"clear">',
            "info": false
        });
        
        // Gestion de la recherche
        $('#searchInput').keyup(function() {
            table.search($(this).val()).draw();
        });
    });
    
    // Fonction de confirmation de suppression
    function confirmDelete(id, name) {
        document.getElementById('userName').textContent = name;
        document.getElementById('deleteForm').action = '/admin/users/' + id;
        $('#deleteModal').modal('show');
    }

    // Chart.js - Évolution des inscriptions
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("userRegistrationsChart");
        var userRegistrationsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($registrationsChartData['labels']) !!},
                datasets: [{
                    label: "Inscriptions",
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
                    data: {!! json_encode($registrationsChartData['data']) !!},
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
                            beginAtZero: true,
                            callback: function(value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
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
                }
            }
        });

        // Chart.js - Répartition par activité
        var ctx2 = document.getElementById("userActivityChart");
        var userActivityChart = new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ["Très actifs", "Actifs", "Peu actifs", "Inactifs", "Jamais connectés"],
                datasets: [{
                    data: {!! json_encode($activityChartData) !!},
                    backgroundColor: ['#1cc88a', '#4e73df', '#36b9cc', '#f6c23e', '#e74a3b'],
                    hoverBackgroundColor: ['#17a673', '#2e59d9', '#2c9faf', '#dda20a', '#be2617'],
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