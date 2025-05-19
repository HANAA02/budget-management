@extends('layouts.user')

@section('title', 'Mes dépenses')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Mes dépenses</h1>
            @if(isset($budget))
                <p class="text-muted">Budget : {{ $budget->nom }} ({{ $budget->date_debut->format('d/m/Y') }} - {{ $budget->date_fin->format('d/m/Y') }})</p>
            @endif
            @if(isset($categorie))
                <p class="text-muted">Catégorie : {{ $categorie->nom }}</p>
            @endif
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('user.expenses.create', isset($budget) ? ['budget_id' => $budget->id] : []) }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Nouvelle dépense
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">Filtres</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.expenses.index') }}" method="GET" id="filter-form">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="budget_id">Budget</label>
                                <select class="form-control" id="budget_id" name="budget_id">
                                    <option value="">Tous les budgets</option>
                                    @foreach($budgets as $budgetItem)
                                        <option value="{{ $budgetItem->id }}" {{ (isset($budget) && $budget->id == $budgetItem->id) ? 'selected' : '' }}>
                                            {{ $budgetItem->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="categorie_id">Catégorie</label>
                                <select class="form-control" id="categorie_id" name="categorie_id">
                                    <option value="">Toutes les catégories</option>
                                    @foreach($categories as $categorieItem)
                                        <option value="{{ $categorieItem->id }}" {{ (isset($categorie) && $categorie->id == $categorieItem->id) ? 'selected' : '' }}>
                                            {{ $categorieItem->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_debut">Date début</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="date_fin">Date fin</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="statut">Statut</label>
                                <select class="form-control" id="statut" name="statut">
                                    <option value="">Tous les statuts</option>
                                    <option value="validée" {{ request('statut') == 'validée' ? 'selected' : '' }}>Validée</option>
                                    <option value="en attente" {{ request('statut') == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="annulée" {{ request('statut') == 'annulée' ? 'selected' : '' }}>Annulée</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Rechercher..." name="search" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i> Filtrer
                                </button>
                                <a href="{{ route('user.expenses.index') }}" class="btn btn-secondary">
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
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Liste des dépenses</h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportCSV">
                                    <i class="fa fa-file-csv"></i> Exporter CSV
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="exportPDF">
                                    <i class="fa fa-file-pdf"></i> Exporter PDF
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Budget</th>
                                    <th>Catégorie</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->date_depense->format('d/m/Y') }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td>
                                            <a href="{{ route('user.budgets.show', $expense->categorieBudget->budget->id) }}">
                                                {{ $expense->categorieBudget->budget->nom }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-light">
                                                <i class="fa {{ $expense->categorieBudget->categorie->icone ?? 'fa-tag' }}"></i>
                                                {{ $expense->categorieBudget->categorie->nom }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($expense->montant, 2) }} MAD</td>
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
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('user.expenses.edit', $expense->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="{{ $expense->id }}" data-description="{{ $expense->description }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fa fa-receipt fa-3x text-muted mb-3"></i>
                                                <h6 class="text-muted">Aucune dépense trouvée</h6>
                                                <a href="{{ route('user.expenses.create') }}" class="btn btn-sm btn-primary mt-3">
                                                    <i class="fa fa-plus"></i> Ajouter une dépense
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="mb-0">Total: <strong>{{ number_format($expenses->sum('montant'), 2) }} MAD</strong></p>
                        </div>
                        <div>
                            {{ $expenses->appends(request()->except('page'))->links() }}
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
                <p>Êtes-vous sûr de vouloir supprimer la dépense <span id="expense-description" class="font-weight-bold"></span> ?</p>
            </div>
            <div class="modal-footer">
                <form id="delete-form" action="" method="POST">
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
<script>
    $(document).ready(function() {
        // Configuration de la suppression
        $('.delete-btn').click(function() {
            const id = $(this).data('id');
            const description = $(this).data('description');
            
            $('#expense-description').text(description);
            $('#delete-form').attr('action', '/user/expenses/' + id);
        });
        
        // Exportation CSV
        $('#exportCSV').click(function() {
            const currentUrl = window.location.href;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('export', 'csv');
            
            const exportUrl = window.location.pathname + '?' + urlParams.toString();
            window.location.href = exportUrl;
        });
        
        // Exportation PDF
        $('#exportPDF').click(function() {
            const currentUrl = window.location.href;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('export', 'pdf');
            
            const exportUrl = window.location.pathname + '?' + urlParams.toString();
            window.location.href = exportUrl;
        });
    });
</script>
@endsection