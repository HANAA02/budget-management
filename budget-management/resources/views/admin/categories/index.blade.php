@extends('layouts.admin')

@section('title', 'Liste des catégories')

@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des catégories</h1>
        <a href="{{ route('admin.categories.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Ajouter une catégorie
        </a>
    </div>

    <!-- Carte principale -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Liste des catégories</h6>
            
            <!-- Filtre et recherche -->
            <div class="input-group" style="width: 300px;">
                <input type="text" id="searchInput" class="form-control bg-light border-0 small" placeholder="Rechercher une catégorie..." 
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
                <table class="table table-bordered" id="categoriesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Icône</th>
                            <th>Pourcentage par défaut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->nom }}</td>
                            <td>{{ Str::limit($category->description, 50) }}</td>
                            <td>
                                @if($category->icone)
                                    <i class="fas fa-{{ $category->icone }} fa-lg"></i>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $category->pourcentage_defaut }}%</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.categories.show', $category->id) }}" class="btn btn-info btn-sm" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-warning btn-sm" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" title="Supprimer" 
                                            onclick="confirmDelete({{ $category->id }}, '{{ $category->nom }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Aucune catégorie trouvée</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <!-- Analyse des catégories -->
    <div class="row">
        <!-- Graphique de répartition -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Répartition des budgets par catégorie</h6>
                </div>
                <div class="card-body">
                    <div class="chart-bar">
                        <canvas id="categoryDistributionChart"></canvas>
                    </div>
                    <hr>
                    <p class="small text-muted">Cette visualisation montre la répartition moyenne des budgets par catégorie.</p>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques des catégories</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h4 class="small font-weight-bold">Catégorie la plus utilisée <span class="float-right">{{ $mostUsedCategory->nom ?? 'N/A' }}</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $mostUsedPercentage ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h4 class="small font-weight-bold">Catégorie avec le plus haut budget <span class="float-right">{{ $highestBudgetCategory->nom ?? 'N/A' }}</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $highestBudgetPercentage ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h4 class="small font-weight-bold">Utilisation des catégories <span class="float-right">{{ $categoriesUsagePercentage ?? 0 }}%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $categoriesUsagePercentage ?? 0 }}%"></div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <h5 class="mb-0">Total des catégories</h5>
                        <div class="h2 font-weight-bold">{{ $categories->total() }}</div>
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
                <p>Êtes-vous sûr de vouloir supprimer la catégorie <span id="categoryName" class="font-weight-bold"></span> ?</p>
                <p class="text-danger">Cette action est irréversible et pourrait affecter les budgets et dépenses associés à cette catégorie.</p>
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

@push('scripts')
<script>
    // Initialisation de DataTables
    $(document).ready(function() {
        var table = $('#categoriesTable').DataTable({
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
        document.getElementById('categoryName').textContent = name;
        document.getElementById('deleteForm').action = '/admin/categories/' + id;
        $('#deleteModal').modal('show');
    }

    // Chart.js - Graphique de répartition des catégories
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById("categoryDistributionChart");
        var categoryDistributionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($categoryChartData['labels']) !!},
                datasets: [{
                    label: "Pourcentage moyen",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: {!! json_encode($categoryChartData['data']) !!},
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
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 6
                        },
                        maxBarThickness: 25,
                    }],
                    yAxes: [{
                        ticks: {
                            min: 0,
                            max: 100,
                            maxTicksLimit: 5,
                            padding: 10,
                            callback: function(value) {
                                return value + "%";
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
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.yLabel + "%";
                        }
                    }
                },
            }
        });
    });
</script>
@endpush