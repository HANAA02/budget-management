@extends('layouts.user')

@section('title', 'Mes revenus')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3">Mes revenus</h1>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('user.incomes.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Ajouter un revenu
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title">Filtres</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('user.incomes.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="compte_id">Compte</label>
                                <select class="form-control" id="compte_id" name="compte_id">
                                    <option value="">Tous les comptes</option>
                                    @foreach($comptes as $compte)
                                        <option value="{{ $compte->id }}" {{ request('compte_id') == $compte->id ? 'selected' : '' }}>
                                            {{ $compte->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="source">Source</label>
                                <input type="text" class="form-control" id="source" name="source" value="{{ request('source') }}" placeholder="Filtrer par source...">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="periodicite">Périodicité</label>
                                <select class="form-control" id="periodicite" name="periodicite">
                                    <option value="">Toutes les périodicités</option>
                                    <option value="ponctuel" {{ request('periodicite') == 'ponctuel' ? 'selected' : '' }}>Ponctuel</option>
                                    <option value="mensuel" {{ request('periodicite') == 'mensuel' ? 'selected' : '' }}>Mensuel</option>
                                    <option value="bimensuel" {{ request('periodicite') == 'bimensuel' ? 'selected' : '' }}>Bimensuel</option>
                                    <option value="hebdomadaire" {{ request('periodicite') == 'hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                                    <option value="trimestriel" {{ request('periodicite') == 'trimestriel' ? 'selected' : '' }}>Trimestriel</option>
                                    <option value="annuel" {{ request('periodicite') == 'annuel' ? 'selected' : '' }}>Annuel</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="date_start">Date de début</label>
                                <input type="date" class="form-control" id="date_start" name="date_start" value="{{ request('date_start') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <!-- Fin de la vue incomes/index.blade.php -->
                                <label for="date_end">Date de fin</label>
                                <input type="date" class="form-control" id="date_end" name="date_end" value="{{ request('date_end') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="search">Recherche</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search" name="search" placeholder="Rechercher..." value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3 text-right" style="margin-top: 32px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-filter"></i> Filtrer
                                </button>
                                <a href="{{ route('user.incomes.index') }}" class="btn btn-secondary">
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
                            <h5 class="card-title">Liste des revenus</h5>
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
                                    <th>Source</th>
                                    <th>Compte</th>
                                    <th>Montant</th>
                                    <th>Périodicité</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incomes as $income)
                                    <tr>
                                        <td>{{ $income->date_perception->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('user.incomes.show', $income->id) }}">
                                                {{ $income->source }}
                                            </a>
                                        </td>
                                        <td>{{ $income->compte->nom }}</td>
                                        <td>{{ number_format($income->montant, 2) }} {{ $income->compte->devise }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $income->periodicite }}
                                                @if($income->periodicite != 'ponctuel' && $income->jour_perception)
                                                    (jour {{ $income->jour_perception }})
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('user.incomes.edit', $income->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-id="{{ $income->id }}" data-source="{{ $income->source }}">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fa fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                                <h6 class="text-muted">Aucun revenu trouvé</h6>
                                                <a href="{{ route('user.incomes.create') }}" class="btn btn-sm btn-primary mt-3">
                                                    <i class="fa fa-plus"></i> Ajouter un revenu
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
                            <p class="mb-0">Total: <strong>
                                @php
                                    $totalRevenues = 0;
                                    $hasMixedDevises = false;
                                    $lastDevise = null;
                                    
                                    foreach($incomes as $income) {
                                        if ($lastDevise === null) {
                                            $lastDevise = $income->compte->devise;
                                        } elseif ($lastDevise !== $income->compte->devise) {
                                            $hasMixedDevises = true;
                                        }
                                        
                                        if (!$hasMixedDevises) {
                                            $totalRevenues += $income->montant;
                                        }
                                    }
                                @endphp
                                
                                @if($hasMixedDevises)
                                    (Devises multiples)
                                @else
                                    {{ number_format($totalRevenues, 2) }} {{ $lastDevise ?? 'MAD' }}
                                @endif
                            </strong></p>
                        </div>
                        <div>
                            {{ $incomes->appends(request()->except('page'))->links() }}
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
                <p>Êtes-vous sûr de vouloir supprimer le revenu <span id="income-source" class="font-weight-bold"></span> ?</p>
                <p class="text-danger">Cette action mettra à jour le solde de votre compte.</p>
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
            const source = $(this).data('source');
            
            $('#income-source').text(source);
            $('#delete-form').attr('action', '/user/incomes/' + id);
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