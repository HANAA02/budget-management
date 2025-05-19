@extends('layouts.user')

@section('header-title', 'Mes comptes')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Tous mes comptes</h2>
        <p class="mt-1 text-sm text-gray-500">Gérez vos différents comptes bancaires et leur solde</p>
    </div>
    <div class="mt-4 md:mt-0">
        <a href="{{ route('accounts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i class="fas fa-plus mr-2"></i> Ajouter un compte
        </a>
    </div>
</div>

<!-- Solde total -->
<div class="bg-white shadow rounded-lg p-4 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Solde total</h3>
            <p class="text-sm text-gray-500">Tous comptes confondus</p>
        </div>
        <div class="mt-4 md:mt-0">
            <span class="text-3xl font-bold {{ $totalBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ number_format($totalBalance, 2) }} MAD
            </span>
        </div>
    </div>
</div>

@if(count($accounts) > 0)
    <!-- Liste des comptes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($accounts as $account)
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="p-5">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $account->nom }}</h3>
                            <p class="text-sm text-gray-500">{{ $account->type }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('accounts.edit', $account->id) }}" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('accounts.show', $account->id) }}" class="text-primary-600 hover:text-primary-800">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <span class="text-2xl font-bold {{ $account->solde >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($account->solde, 2) }} {{ $account->devise }}
                        </span>
                    </div>
                    
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span>Revenus (30j)</span>
                            <span class="text-green-600">+{{ number_format($account->monthly_income, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span>Dépenses (30j)</span>
                            <span class="text-red-600">-{{ number_format($account->monthly_expenses, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Transactions récentes -->
                <div class="bg-gray-50 px-5 py-3">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-sm font-medium text-gray-700">Transactions récentes</h4>
                        <a href="{{ route('accounts.show', $account->id) }}" class="text-xs text-primary-600 hover:text-primary-800">
                            Voir toutes
                        </a>
                    </div>
                    
                    @if(count($account->recent_transactions) > 0)
                        <div class="space-y-2">
                            @foreach($account->recent_transactions as $transaction)
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center text-sm">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center mr-2">
                                            <i class="fas {{ $transaction->type == 'income' ? 'fa-arrow-down text-green-500' : 'fa-arrow-up text-red-500' }}"></i>
                                        </div>
                                        <span class="truncate" style="max-width: 150px;">{{ $transaction->description }}</span>
                                    </div>
                                    <span class="text-sm font-medium {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-2">Aucune transaction récente</p>
                    @endif
                </div>
                
                <!-- Actions -->
                <div class="px-5 py-3 border-t border-gray-200 bg-white">
                    <div class="flex justify-between">
                        <a href="{{ route('expenses.create', ['account_id' => $account->id]) }}" class="text-xs text-red-600 hover:text-red-800">
                            <i class="fas fa-minus-circle mr-1"></i> Ajouter une dépense
                        </a>
                        <a href="{{ route('incomes.create', ['account_id' => $account->id]) }}" class="text-xs text-green-600 hover:text-green-800">
                            <i class="fas fa-plus-circle mr-1"></i> Ajouter un revenu
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Aucun compte -->
    <div class="bg-white shadow rounded-lg p-6 text-center">
        <div class="mb-4">
            <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                <i class="fas fa-wallet text-gray-400 text-3xl"></i>
            </div>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Vous n'avez pas encore de compte</h3>
        <p class="text-gray-500 mb-6">Commencez par créer un compte pour suivre vos revenus et dépenses.</p>
        
        <a href="{{ route('accounts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i class="fas fa-plus mr-2"></i> Créer mon premier compte
        </a>
    </div>
@endif

<!-- Activité récente -->
@if(count($accounts) > 0)
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Activité récente</h2>
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="divide-y divide-gray-200">
                @forelse($recentActivities as $activity)
                    <div class="p-4 sm:p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-{{ $activity->type_color }}-100 flex items-center justify-center">
                                    <i class="fas fa-{{ $activity->icon }} text-{{ $activity->type_color }}-600"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                                    <p class="text-sm text-gray-500">{{ $activity->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <div class="mt-1 flex items-center">
                                    <p class="text-sm text-gray-500">
                                        <span class="font-medium">Compte:</span> {{ $activity->account_name }}
                                    </p>
                                    @if($activity->amount)
                                        <p class="ml-6 text-sm font-medium {{ $activity->amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $activity->amount >= 0 ? '+' : '' }}{{ number_format($activity->amount, 2) }} MAD
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <p class="text-gray-500">Aucune activité récente</p>
                    </div>
                @endforelse
            </div>
            
            @if(count($recentActivities) > 0)
                <div class="bg-gray-50 px-4 py-3 sm:px-6 text-center">
                    <a href="{{ route('activities') }}" class="text-sm text-primary-600 hover:text-primary-800">
                        Voir toutes les activités
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
@endsection