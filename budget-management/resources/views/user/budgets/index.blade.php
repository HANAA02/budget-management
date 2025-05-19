@extends('layouts.user')

@section('title', 'Mes budgets')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Mes budgets</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('user.budgets.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Nouveau budget
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="card-title">Liste des budgets</h5>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('user.budgets.index') }}" method="GET" class="form-inline justify-content-end">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-outline-secondary">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Période</th>
                                    <th>Montant total</th>
                                    <th>Progression</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($budgets as $budget)
                                    <tr>
                                        <td>
                                            <a href="{{ route('user.budgets.show', $budget->id) }}">
                                                {{ $budget->nom }}
                                            </a>
                                        </td>
                                        <td>{{ $budget->date_debut->format('d/m/Y') }} - {{ $budget->date_fin->format('d/m/Y') }}</td>
                                        <td>{{ number_format($budget->montant_total, 2) }} MAD</td>
                                        <td width="25%">
                                            @php
                                                $depensesTotal = $budget->depensesTotal ?? 0;
                                                $pourcentage = $budget->montant_total > 0 ? min(100, round(($depensesTotal / $budget->montant_total) * 100)) : 0;
                                                $progressClass = 'success';
                                                
                                                if ($pourcentage >= 90) {
                                                    $progressClass = 'danger';
                                                } elseif ($pourcentage >= 75) {
                                                    $progressClass = 'warning';
                                                }
                                            @endphp
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-{{ $progressClass }}" role="progressbar" 
                                                    style="width: {{ $pourcentage }}%;" 
                                                    aria-valuenow="{{ $pourcentage }}" 
                                                    aria-valuemin="0" 
                                                    aria-valuemax="100">
                                                    {{ $pourcentage }}%
                                                </div>
                                            </div>
                                            <small>{{ number_format($depensesTotal, 2) }} / {{ number_format($budget->montant_total, 2) }} MAD</small>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('user.budgets.show', $budget->id) }}" class="btn btn-sm btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('user.budgets.edit', $budget->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="{{ $budget->id }}" data-name="{{ $budget->nom }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fa fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                                <h6 class="text-muted">Aucun budget trouvé</h6>
                                                <a href="{{ route('user.budgets.create') }}" class="btn btn-sm btn-primary mt-3">
                                                    <i class="fa fa-plus"></i> Créer votre premier budget
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
                    <div class="d-flex justify-content-center">
                        {{ $budgets->links() }}
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
                <p>Êtes-vous sûr de vouloir supprimer le budget <span id="budget-name" class="font-weight-bold"></span> ?</p>
                <p class="text-danger">Cette action est irréversible et supprimera toutes les dépenses associées.</p>
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
        $('.delete-btn').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            
            $('#budget-name').text(name);
            $('#delete-form').attr('action', '/user/budgets/' + id);
        });
    });
</script>
@endsection
