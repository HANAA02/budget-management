@extends('layouts.user')

@section('header-title', 'Tableau de bord')

@section('content')
<div class="space-y-6">
    <!-- Résumé des finances -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Résumé des finances</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Solde total -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Solde total</h3>
                <div class="mt-2 flex items-baseline">
                    <span class="text-2xl font-semibold text-gray-900">{{ number_format($totalBalance, 2) }} MAD</span>
                    @if($balanceChange > 0)
                        <span class="ml-2 text-xs font-medium text-green-600">+{{ $balanceChange }}%</span>
                    @elseif($balanceChange < 0)
                        <span class="ml-2 text-xs font-medium text-red-600">{{ $balanceChange }}%</span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-gray-500">Tous comptes confondus</p>
            </div>
            
            <!-- Revenus du mois -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Revenus (ce mois)</h3>
                <div class="mt-2 flex items-baseline">
                    <span class="text-2xl font-semibold text-green-600">{{ number_format($monthlyIncome, 2) }} MAD</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $incomeTransactionsCount }} transactions</p>
            </div>
            
            <!-- Dépenses du mois -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Dépenses (ce mois)</h3>
                <div class="mt-2 flex items-baseline">
                    <span class="text-2xl font-semibold text-red-600">{{ number_format($monthlyExpenses, 2) }} MAD</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $expenseTransactionsCount }} transactions</p>
            </div>
            
            <!-- Budget restant -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-sm font-medium text-gray-500">Budget restant</h3>
                <div class="mt-2 flex items-baseline">
                    <span class="text-2xl font-semibold {{ $budgetHealth === 'good' ? 'text-green-600' : ($budgetHealth === 'warning' ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ number_format($remainingBudget, 2) }} MAD
                    </span>
                </div>
                <div class="mt-1 w-full bg-gray-200 rounded-full h-2.5">
                    <div class="h-2.5 rounded-full {{ $budgetHealth === 'good' ? 'bg-green-600' : ($budgetHealth === 'warning' ? 'bg-yellow-600' : 'bg-red-600') }}" style="width: {{ $budgetPercentage }}%"></div>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $budgetPercentage }}% utilisé</p>
            </div>
        </div>
    </div>
    
    <!-- Budget actuel et dépenses -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Budget par catégorie -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900">Budget actuel par catégorie</h2>
                
                @if($currentBudget)
                    <a href="{{ route('budgets.show', $currentBudget->id) }}" class="text-sm text-primary-600 hover:text-primary-900">Voir tous les détails</a>
                @else
                    <a href="{{ route('budgets.create') }}" class="text-sm text-primary-600 hover:text-primary-900">Créer un budget</a>
                @endif
            </div>
            
            <div class="space-y-4">
                @if($currentBudget)
                    @foreach($budgetCategories as $category)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center">
                                    <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $category->color }}"></span>
                                    <span class="text-sm font-medium text-gray-900">{{ $category->name }}</span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ number_format($category->spent, 2) }} / {{ number_format($category->allocated, 2) }} MAD
                                </div>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                                <div class="h-2.5 rounded-full" 
                                     style="width: {{ $category->percentage }}%; background-color: {{ $category->color }}"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>{{ $category->percentage }}% utilisé</span>
                                <span>{{ number_format($category->remaining, 2) }} MAD restant</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-300 text-5xl mb-3"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Aucun budget actif</h3>
                        <p class="text-sm text-gray-500 mb-4">Vous n'avez pas encore créé de budget pour la période en cours.</p>
                        <a href="{{ route('budgets.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-plus mr-2"></i> Créer un budget
                        </a>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Dépenses récentes -->
        <div class="bg-white shadow rounded-lg p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900">Dernières dépenses</h2>
                <a href="{{ route('expenses.index') }}" class="text-sm text-primary-600 hover:text-primary-900">Voir toutes</a>
            </div>
            
            <div class="flow-root">
                <ul class="-my-5 divide-y divide-gray-200">
                    @forelse($recentExpenses as $expense)
                        <li class="py-3">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-{{ $expense->category_color }}-100 flex items-center justify-center">
                                        <i class="fas fa-{{ $expense->category_icon }} text-{{ $expense->category_color }}-600"></i>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $expense->description }}
                                    </p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $expense->category_name }} · {{ $expense->date->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-red-600">
                                        -{{ number_format($expense->amount, 2) }} MAD
                                    </span>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center">
                            <p class="text-sm text-gray-500">Aucune dépense récente</p>
                        </li>
                    @endforelse
                </ul>
            </div>
            
            <div class="mt-6">
                <a href="{{ route('expenses.create') }}" class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-plus mr-2"></i> Ajouter une dépense
                </a>
            </div>
        </div>
    </div>
    
    <!-- Graphiques et analyses -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Évolution des dépenses -->
        <div class="bg-white shadow rounded-lg p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900">Évolution des dépenses</h2>
                <div class="flex space-x-3">
                    <button @click="changeExpensesPeriod('month')" class="text-xs text-gray-500 hover:text-primary-600" :class="expensesPeriod === 'month' ? 'font-medium text-primary-600' : ''">
                        Mois
                    </button>
                    <button @click="changeExpensesPeriod('quarter')" class="text-xs text-gray-500 hover:text-primary-600" :class="expensesPeriod === 'quarter' ? 'font-medium text-primary-600' : ''">
                        Trimestre
                    </button>
                    <button @click="changeExpensesPeriod('year')" class="text-xs text-gray-500 hover:text-primary-600" :class="expensesPeriod === 'year' ? 'font-medium text-primary-600' : ''">
                        Année
                    </button>
                </div>
            </div>
            
            <div class="h-60">
                <canvas id="expensesChart"></canvas>
            </div>
        </div>
        
        <!-- Répartition par catégorie -->
        <div class="bg-white shadow rounded-lg p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900">Répartition des dépenses</h2>
                <select id="categoryChartPeriod" class="text-xs text-gray-500 border-0 focus:ring-0">
                    <option value="month">Ce mois</option>
                    <option value="quarter">Ce trimestre</option>
                    <option value="year">Cette année</option>
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="h-48 flex items-center justify-center">
                    <canvas id="categoryPieChart"></canvas>
                </div>
                
                <div class="flex flex-col justify-center space-y-3">
                    @foreach($topCategories as $idx => $category)
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $category->color }}"></span>
                            <p class="text-sm text-gray-600 flex-1">{{ $category->name }}</p>
                            <p class="text-sm font-medium">{{ $category->percentage }}%</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dernières activités et objectifs -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Activités récentes -->
        <div class="lg:col-span-2 bg-white shadow rounded-lg p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900">Activités récentes</h2>
            </div>
            
            <div class="flow-root">
                <ul class="-mb-8">
                    @forelse($recentActivities as $activity)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex items-start space-x-3">
                                    <div class="relative">
                                        <div class="h-10 w-10 rounded-full bg-{{ $activity->type_color }}-100 flex items-center justify-center ring-8 ring-white">
                                            <i class="fas fa-{{ $activity->icon }} text-{{ $activity->type_color }}-600"></i>
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div>
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900">{{ $activity->title }}</span>
                                            </div>
                                            <p class="mt-0.5 text-sm text-gray-500">
                                                {{ $activity->description }}
                                            </p>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500">
                                            <span>{{ $activity->created_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center">
                            <p class="text-sm text-gray-500">Aucune activité récente</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
        
        <!-- Objectifs financiers -->
        <div class="bg-white shadow rounded-lg p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-900">Objectifs financiers</h2>
                <a href="{{ route('goals.index') }}" class="text-sm text-primary-600 hover:text-primary-900">Voir tous</a>
            </div>
            
            <div class="space-y-4">
                @forelse($activeGoals as $goal)
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-900">{{ $goal->title }}</span>
                            <span class="text-xs text-gray-500">{{ $goal->progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="h-2.5 rounded-full {{ $goal->progress_color }}" style="width: {{ $goal->progress }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>{{ number_format($goal->current_amount, 2) }} / {{ number_format($goal->target_amount, 2) }} MAD</span>
                            <span>{{ $goal->days_left }} jours restants</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6">
                        <i class="fas fa-bullseye text-gray-300 text-4xl mb-3"></i>
                        <h3 class="text-md font-medium text-gray-900 mb-1">Aucun objectif actif</h3>
                        <p class="text-sm text-gray-500 mb-4">Définissez des objectifs pour mieux gérer vos finances.</p>
                        <a href="{{ route('goals.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-plus mr-2"></i> Définir un objectif
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour les graphiques provenant du serveur
        const expensesData = @json($expensesChartData);
        const categoryData = @json($categoryChartData);
        
        // Configuration des couleurs
        const categoryColors = @json($categoryColors);
        
        // Graphique d'évolution des dépenses
        const expensesCtx = document.getElementById('expensesChart').getContext('2d');
        const expensesChart = new Chart(expensesCtx, {
            type: 'line',
            data: {
                labels: expensesData.labels,
                datasets: [{
                    label: 'Dépenses',
                    data: expensesData.values,
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
                                        currency: 'MAD'
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
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' MAD';
                            }
                        }
                    }
                }
            }
        });
        
        // Graphique de répartition des dépenses par catégorie
        const categoryCtx = document.getElementById('categoryPieChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.labels,
                datasets: [{
                    data: categoryData.values,
                    backgroundColor: categoryColors,
                    borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const percentage = Math.round((value / context.dataset.data.reduce((a, b) => a + b, 0)) * 100);
                                return `${label}: ${percentage}%`;
                            }
                        }
                    }
                }
            }
        });
        
        // Gestion du changement de période pour le graphique des dépenses
        window.changeExpensesPeriod = function(period) {
            fetch(`/api/dashboard/expenses-chart?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    expensesChart.data.labels = data.labels;
                    expensesChart.data.datasets[0].data = data.values;
                    expensesChart.update();
                });
        };
        
        // Gestion du changement de période pour le graphique des catégories
        document.getElementById('categoryChartPeriod').addEventListener('change', function() {
            const period = this.value;
            fetch(`/api/dashboard/category-chart?period=${period}`)
                .then(response => response.json())
                .then(data => {
                    categoryChart.data.labels = data.labels;
                    categoryChart.data.datasets[0].data = data.values;
                    categoryChart.update();
                });
        });
    });
</script>
@endpush