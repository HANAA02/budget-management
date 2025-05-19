@extends('layouts.user')

@section('header-title', 'Détails du compte')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center">
        <a href="{{ route('accounts.index') }}" class="text-primary-600 hover:text-primary-900 mr-2">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $account->nom }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $account->institution ?? 'Compte personnel' }}</p>
        </div>
    </div>
    <div class="mt-4 sm:mt-0 flex space-x-3">
        <a href="{{ route('expenses.create', ['account_id' => $account->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <i class="fas fa-minus-circle mr-2"></i> Dépense
        </a>
        <a href="{{ route('incomes.create', ['account_id' => $account->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <i class="fas fa-plus-circle mr-2"></i> Revenu
        </a>
        <a href="{{ route('accounts.edit', $account->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i class="fas fa-edit mr-2"></i> Modifier
        </a>
    </div>
</div>

<!-- Résumé du compte -->
<div class="bg-white shadow rounded-lg overflow-hidden mb-6">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Solde actuel -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Solde actuel</h3>
                <span class="text-3xl font-bold {{ $account->solde >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($account->solde, 2) }} {{ $account->devise }}
                </span>
                <p class="mt-1 text-xs text-gray-500">
                    @if($account->date_dernier_releve)
                        Dernière mise à jour: {{ $account->date_dernier_releve->format('d/m/Y') }}
                    @else
                        Solde au {{ now()->format('d/m/Y') }}
                    @endif
                </p>
            </div>
            
            <!-- Revenus du mois -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Revenus ({{ now()->format('F Y') }})</h3>
                <span class="text-2xl font-bold text-green-600">
                    {{ number_format($monthlyStats['revenus'], 2) }} {{ $account->devise }}
                </span>
                <p class="mt-1 text-xs text-gray-500">
                    {{ $monthlyStats['nombre_revenus'] }} transactions
                </p>
            </div>
            
            <!-- Dépenses du mois -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Dépenses ({{ now()->format('F Y') }})</h3>
                <span class="text-2xl font-bold text-red-600">
                    {{ number_format($monthlyStats['depenses'], 2) }} {{ $account->devise }}
                </span>
                <p class="mt-1 text-xs text-gray-500">
                    {{ $monthlyStats['nombre_depenses'] }} transactions
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Graphique et informations -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Graphique d'évolution du solde -->
    <div class="lg:col-span-2 bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Évolution du solde</h3>
                <div class="flex space-x-3">
                    <button @click="changePeriod('month')" class="text-xs text-gray-500 hover:text-primary-600" :class="period === 'month' ? 'font-medium text-primary-600' : ''">
                        Mois
                    </button>
                    <button @click="changePeriod('quarter')" class="text-xs text-gray-500 hover:text-primary-600" :class="period === 'quarter' ? 'font-medium text-primary-600' : ''">
                        Trimestre
                    </button>
                    <button @click="changePeriod('year')" class="text-xs text-gray-500 hover:text-primary-600" :class="period === 'year' ? 'font-medium text-primary-600' : ''">
                        Année
                    </button>
                </div>
            </div>
            
            <div class="h-60">
                <canvas id="balanceChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Détails du compte -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Détails du compte</h3>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Type de compte:</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if($account->type == 'courant')
                            Compte courant
                        @elseif($account->type == 'epargne')
                            Compte d'épargne
                        @elseif($account->type == 'carte_credit')
                            Carte de crédit
                        @elseif($account->type == 'especes')
                            Espèces
                        @elseif($account->type == 'investissement')
                            Compte d'investissement
                        @else
                            {{ $account->type }}
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Institution:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $account->institution ?? 'Non spécifiée' }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Numéro de compte:</span>
                    <span class="text-sm font-medium text-gray-900">
                        @if($account->numero_compte)
                            ••••{{ substr($account->numero_compte, -4) }}
                        @else
                            Non spécifié
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Devise:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $account->devise }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Date de création:</span>
                    <span class="text-sm font-medium text-gray-900">{{ $account->created_at->format('d/m/Y') }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Statut:</span>
                    <span class="text-sm font-medium">
                        @if($account->actif)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactif
                            </span>
                        @endif
                    </span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Inclus dans le total:</span>
                    <span class="text-sm font-medium">
                        @if(!$account->exclure_du_total)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Oui
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Non
                            </span>
                        @endif
                    </span>
                </div>
                
                @if($account->notes)
                    <div class="pt-3 mt-3 border-t border-gray-200">
                        <span class="text-sm text-gray-500">Notes:</span>
                        <p class="mt-1 text-sm text-gray-900">{{ $account->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Transactions récentes -->
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Transactions récentes</h3>
            
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-500">Filtrer par:</span>
                <select id="transactionType" class="text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                    <option value="all">Toutes</option>
                    <option value="expenses">Dépenses</option>
                    <option value="incomes">Revenus</option>
                </select>
                
                <select id="transactionPeriod" class="text-sm border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                    <option value="month">Ce mois</option>
                    <option value="quarter">Ce trimestre</option>
                    <option value="year">Cette année</option>
                    <option value="all">Toutes les dates</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto" id="transactions-container">
        @forelse($transactions as $transaction)
            <div class="p-4 hover:bg-gray-50 transaction-item" 
                 data-type="{{ $transaction->type }}" 
                 data-date="{{ $transaction->date->format('Y-m-d') }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-{{ $transaction->type == 'income' ? 'green' : 'red' }}-100 flex items-center justify-center">
                                <i class="fas {{ $transaction->type == 'income' ? 'fa-arrow-down text-green-600' : 'fa-arrow-up text-red-600' }}"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $transaction->description }}
                            </div>
                            <div class="text-sm text-gray-500 flex">
                                <span>{{ $transaction->date->format('d/m/Y') }}</span>
                                @if($transaction->category)
                                    <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $transaction->category->nom }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-medium {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type == 'income' ? '+' : '-' }}{{ number_format($transaction->montant, 2) }} {{ $account->devise }}
                        </div>
                        <div class="text-xs text-gray-500">
                            Solde: {{ number_format($transaction->solde_apres, 2) }} {{ $account->devise }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center">
                <p class="text-gray-500">Aucune transaction trouvée pour ce compte</p>
            </div>
        @endforelse
    </div>
    
    @if(count($transactions) >= 20)
        <div class="bg-gray-50 px-6 py-3 text-center">
            <a href="{{ route('transactions.index', ['account_id' => $account->id]) }}" class="text-sm text-primary-600 hover:text-primary-800">
                Voir toutes les transactions
            </a>
        </div>
    @endif
</div>

<!-- Bouton d'export -->
<div class="mt-6 text-right">
    <a href="{{ route('accounts.export', $account->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
        <i class="fas fa-file-download mr-2"></i> Exporter les transactions
    </a>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique provenant du serveur
        const balanceData = @json($balanceChartData);
        
        // Graphique d'évolution du solde
        const balanceCtx = document.getElementById('balanceChart').getContext('2d');
        const balanceChart = new Chart(balanceCtx, {
            type: 'line',
            data: {
                labels: balanceData.labels,
                datasets: [{
                    label: 'Solde',
                    data: balanceData.values,
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderColor: 'rgba(79, 70, 229, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fr-MA', {
                                        style: 'currency',
                                        currency: '{{ $account->devise }}'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return value + ' {{ $account->devise }}';
                            }
                        }
                    }
                }
            }
        });
        
        // Gestion du changement de période pour le graphique d'évolution
        window.changePeriod = function(period) {
            fetch(`/api/accounts/{{ $account->id }}/balance-chart?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    balanceChart.data.labels = data.labels;
                    balanceChart.data.datasets[0].data = data.values;
                    balanceChart.update();
                });
        };
        
        // Filtrage des transactions
        const filterTransactions = () => {
            const type = document.getElementById('transactionType').value;
            const period = document.getElementById('transactionPeriod').value;
            const items = document.querySelectorAll('.transaction-item');
            
            const now = new Date();
            let startDate = null;
            
            if (period === 'month') {
                startDate = new Date(now.getFullYear(), now.getMonth(), 1);
            } else if (period === 'quarter') {
                const quarterMonth = Math.floor(now.getMonth() / 3) * 3;
                startDate = new Date(now.getFullYear(), quarterMonth, 1);
            } else if (period === 'year') {
                startDate = new Date(now.getFullYear(), 0, 1);
            }
            
            items.forEach(item => {
                const itemType = item.dataset.type;
                const itemDate = new Date(item.dataset.date);
                
                let showByType = type === 'all' || itemType === type;
                let showByDate = period === 'all' || (startDate && itemDate >= startDate);
                
                if (showByType && showByDate) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Afficher un message si aucune transaction n'est visible
            const visibleItems = document.querySelectorAll('.transaction-item[style=""]');
            const container = document.getElementById('transactions-container');
            const noTransactionsMsg = container.querySelector('.no-transactions-msg');
            
            if (visibleItems.length === 0) {
                if (!noTransactionsMsg) {
                    const msg = document.createElement('div');
                    msg.className = 'p-6 text-center no-transactions-msg';
                    msg.innerHTML = '<p class="text-gray-500">Aucune transaction trouvée pour les filtres sélectionnés</p>';
                    container.appendChild(msg);
                }
            } else if (noTransactionsMsg) {
                noTransactionsMsg.remove();
            }
        };
        
        document.getElementById('transactionType').addEventListener('change', filterTransactions);
        document.getElementById('transactionPeriod').addEventListener('change', filterTransactions);
    });
</script>
@endpush