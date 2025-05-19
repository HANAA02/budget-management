@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">{{ __('Tableau de bord') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="text-center mb-5">
                        <h2>Bienvenue sur {{ config('app.name') }}</h2>
                        <p class="lead">Vous êtes maintenant connecté.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-wallet fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Gérer vos budgets</h5>
                                    <p class="card-text">Créez et personnalisez vos budgets mensuels.</p>
                                    <a href="{{ route('user.budgets.index') }}" class="btn btn-outline-primary">Voir mes budgets</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-receipt fa-3x text-success"></i>
                                    </div>
                                    <h5 class="card-title">Suivre vos dépenses</h5>
                                    <p class="card-text">Enregistrez et catégorisez vos dépenses quotidiennes.</p>
                                    <a href="{{ route('user.expenses.index') }}" class="btn btn-outline-success">Gérer mes dépenses</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-chart-pie fa-3x text-info"></i>
                                    </div>
                                    <h5 class="card-title">Analyser vos finances</h5>
                                    <p class="card-text">Visualisez et analysez votre situation financière.</p>
                                    <a href="{{ route('user.reports.index') }}" class="btn btn-outline-info">Voir mes rapports</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Conseil du jour</h5>
                                <p class="mb-0">Savez-vous que définir des objectifs d'épargne clairs augmente vos chances d'économiser ? Créez votre premier objectif dès maintenant !</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary btn-lg">Accéder à mon tableau de bord complet</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection