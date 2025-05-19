<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Tableau de bord') | {{ config('app.name', 'Budget Manager') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
    <div id="app" class="flex h-screen overflow-hidden">
        <!-- Off-canvas menu for mobile -->
        <div x-show="sidebarOpen" class="md:hidden">
            <div class="fixed inset-0 z-40 flex">
                <!-- Off-canvas menu overlay -->
                <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0" @click="sidebarOpen = false">
                    <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
                </div>
                
                <!-- Off-canvas menu -->
                <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex-1 flex flex-col max-w-xs w-full bg-gray-800">
                    <div class="absolute top-0 right-0 -mr-12 pt-2">
                        <button x-show="sidebarOpen" @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="sr-only">Fermer le menu</span>
                            <i class="fas fa-times text-white"></i>
                        </button>
                    </div>
                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center px-4">
                            <i class="fas fa-coins text-primary-500 text-2xl mr-2"></i>
                            <span class="text-xl font-bold text-white">Budget<span class="text-primary-500">Manager</span></span>
                        </div>
                        
                        <!-- Mobile Navigation -->
                        <nav class="mt-5 px-2 space-y-1">
                            <a href="{{ route('dashboard') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-tachometer-alt mr-3 text-lg {{ request()->routeIs('dashboard') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Tableau de bord
                            </a>
                            
                            <a href="{{ route('budgets.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('budgets.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-chart-pie mr-3 text-lg {{ request()->routeIs('budgets.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Budgets
                            </a>

                            <a href="{{ route('incomes.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('incomes.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-money-bill-wave mr-3 text-lg {{ request()->routeIs('incomes.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Revenus
                            </a>
                            
                            <a href="{{ route('expenses.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('expenses.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-shopping-cart mr-3 text-lg {{ request()->routeIs('expenses.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Dépenses
                            </a>
                            
                            <a href="{{ route('accounts.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('accounts.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-wallet mr-3 text-lg {{ request()->routeIs('accounts.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Comptes
                            </a>
                            
                            <a href="{{ route('goals.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('goals.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-bullseye mr-3 text-lg {{ request()->routeIs('goals.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Objectifs
                            </a>
                            
                            <a href="{{ route('reports.index') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('reports.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-file-alt mr-3 text-lg {{ request()->routeIs('reports.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Rapports
                            </a>
                            
                            <div class="pt-4 mt-4 border-t border-gray-700">
                                <h3 class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Paramètres</h3>
                                
                                <a href="{{ route('profile.edit') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('profile.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-user-circle mr-3 text-lg {{ request()->routeIs('profile.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    Profil
                                </a>
                                
                                <a href="{{ route('settings') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md {{ request()->routeIs('settings') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                    <i class="fas fa-cog mr-3 text-lg {{ request()->routeIs('settings') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                    Paramètres
                                </a>
                                
                                @if(Auth::user()->role === 'admin')
                                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                                        <i class="fas fa-user-shield mr-3 text-lg text-gray-400 group-hover:text-gray-300"></i>
                                        Administration
                                    </a>
                                @endif
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left group flex items-center px-2 py-2 text-base font-medium rounded-md text-gray-300 hover:bg-gray-700 hover:text-white">
                                        <i class="fas fa-sign-out-alt mr-3 text-lg text-gray-400 group-hover:text-gray-300"></i>
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </nav>
                    </div>
                </div>
                
                <div class="flex-shrink-0 w-14">
                    <!-- Force sidebar to shrink to fit close icon -->
                </div>
            </div>
        </div>

        <!-- Static sidebar for desktop -->
        <div class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64">
                <div class="flex flex-col h-0 flex-1 bg-gray-800">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex items-center flex-shrink-0 px-4">
                            <i class="fas fa-coins text-primary-500 text-2xl mr-2"></i>
                            <span class="text-xl font-bold text-white">Budget<span class="text-primary-500">Manager</span></span>
                        </div>
                        
                        <!-- Desktop Navigation -->
                        <nav class="mt-5 flex-1 px-2 space-y-1">
                            <a href="{{ route('dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-tachometer-alt mr-3 text-lg {{ request()->routeIs('dashboard') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Tableau de bord
                            </a>
                            
                            <a href="{{ route('budgets.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('budgets.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-chart-pie mr-3 text-lg {{ request()->routeIs('budgets.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Budgets
                            </a>

                            <a href="{{ route('incomes.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('incomes.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-money-bill-wave mr-3 text-lg {{ request()->routeIs('incomes.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Revenus
                            </a>
                            
                            <a href="{{ route('expenses.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('expenses.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-shopping-cart mr-3 text-lg {{ request()->routeIs('expenses.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Dépenses
                            </a>
                            
                            <a href="{{ route('accounts.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('accounts.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-wallet mr-3 text-lg {{ request()->routeIs('accounts.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Comptes
                            </a>
                            
                            <a href="{{ route('goals.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('goals.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-bullseye mr-3 text-lg {{ request()->routeIs('goals.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Objectifs
                            </a>
                            
                            <a href="{{ route('reports.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md {{ request()->routeIs('reports.*') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}">
                                <i class="fas fa-file-alt mr-3 text-lg {{ request()->routeIs('reports.*') ? 'text-gray-300' : 'text-gray-400 group-hover:text-gray-300' }}"></i>
                                Rapports
                            </a>
                        </nav>
                    </div>
                    
                    <div class="flex-shrink-0 flex border-t border-gray-700 p-4">
                        <div class="flex-shrink-0 w-full group block">
                            <div class="flex items-center">
                                <div>
                                    @if(Auth::user()->avatar)
                                        <img class="inline-block h-9 w-9 rounded-full" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->nom }}">
                                    @else
                                        <div class="inline-block h-9 w-9 rounded-full bg-primary-600 flex items-center justify-center text-white font-semibold">
                                            {{ substr(Auth::user()->nom, 0, 1) . substr(Auth::user()->prenom, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-white">{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</p>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <a href="{{ route('profile.edit') }}" class="text-xs text-gray-400 hover:text-gray-200">
                                            <i class="fas fa-user-circle"></i>
                                        </a>
                                        <a href="{{ route('settings') }}" class="text-xs text-gray-400 hover:text-gray-200">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-gray-400 hover:text-gray-200">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <div class="md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3">
                <button @click="sidebarOpen = true" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500">
                    <span class="sr-only">Ouvrir le menu</span>
                    <i class="fas fa-bars h-6 w-6"></i>
                </button>
            </div>
            
            <!-- Top header -->
            <div class="flex-shrink-0 bg-white shadow">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <div class="flex justify-between items-center">
                        <h1 class="text-xl font-semibold text-gray-900">@yield('header-title', 'Tableau de bord')</h1>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <div class="relative" x-data="{ notificationsOpen: false }">
                                <button @click="notificationsOpen = !notificationsOpen" class="relative p-1 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-full">
                                    <span class="sr-only">Voir les notifications</span>
                                    <i class="fas fa-bell"></i>
                                    @if(isset($notifications_count) && $notifications_count > 0)
                                        <span class="absolute top-0 right-0 -mt-1 -mr-1 flex h-4 w-4">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-4 w-4 bg-primary-500 text-xs text-white justify-center items-center">
                                                {{ $notifications_count > 9 ? '9+' : $notifications_count }}
                                            </span>
                                        </span>
                                    @endif
                                </button>
                                
                                <!-- Notifications dropdown -->
                                <div x-show="notificationsOpen" @click.away="notificationsOpen = false" class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-10" x-cloak>
                                    <div class="px-4 py-2 border-b border-gray-200">
                                        <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                    </div>
                                    
                                    <div class="max-h-72 overflow-y-auto">
                                        @forelse($notifications ?? [] as $notification)
                                            <a href="#" class="block px-4 py-2 hover:bg-gray-50 {{ $notification->read ? '' : 'bg-blue-50' }}">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0 pt-0.5">
                                                        <div class="h-8 w-8 rounded-full bg-{{ $notification->type_color }}-100 flex items-center justify-center">
                                                            <i class="fas fa-{{ $notification->icon }} text-{{ $notification->type_color }}-600"></i>
                                                        </div>
                                                    </div>
                                                    <div class="ml-3 w-0 flex-1">
                                                        <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                                                        <p class="text-sm text-gray-500">{{ $notification->message }}</p>
                                                        <p class="mt-1 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                        @empty
                                            <div class="px-4 py-6 text-center text-gray-500">
                                                <i class="fas fa-bell-slash text-2xl mb-2"></i>
                                                <p>Aucune notification</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    
                                    <div class="px-4 py-2 border-t border-gray-200 text-center">
                                        <a href="{{ route('notifications.index') }}" class="text-xs text-primary-600 hover:text-primary-800">Voir toutes les notifications</a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick actions -->
                            <div class="relative" x-data="{ actionsOpen: false }">
                                <button @click="actionsOpen = !actionsOpen" class="p-1 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-full">
                                    <span class="sr-only">Actions rapides</span>
                                    <i class="fas fa-plus"></i>
                                </button>
                                
                                <!-- Quick actions dropdown -->
                                <div x-show="actionsOpen" @click.away="actionsOpen = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-10" x-cloak>
                                    <a href="{{ route('expenses.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-shopping-cart mr-2 text-gray-500"></i> Nouvelle dépense
                                    </a>
                                    <a href="{{ route('incomes.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-money-bill-wave mr-2 text-gray-500"></i> Nouveau revenu
                                    </a>
                                    <a href="{{ route('budgets.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-chart-pie mr-2 text-gray-500"></i> Nouveau budget
                                    </a>
                                    <a href="{{ route('goals.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-bullseye mr-2 text-gray-500"></i> Nouvel objectif
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Flash Messages -->
            @if (session('success'))
                <div id="flash-success" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">{{ session('success') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" onclick="document.getElementById('flash-success').remove()" class="inline-flex rounded-md p-1.5 text-green-500 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <span class="sr-only">Fermer</span>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div id="flash-error" class="mx-4 sm:mx-6 lg:mx-8 mt-4">
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm">{{ session('error') }}</p>
                            </div>
                            <div class="ml-auto pl-3">
                                <div class="-mx-1.5 -my-1.5">
                                    <button type="button" onclick="document.getElementById('flash-error').remove()" class="inline-flex rounded-md p-1.5 text-red-500 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <span class="sr-only">Fermer</span>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Main content -->
            <main class="flex-1 relative z-0 overflow-y-auto focus:outline-none">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    
    <script>
        // Auto hide flash messages after 5 seconds
        setTimeout(function() {
            const successFlash = document.getElementById('flash-success');
            const errorFlash = document.getElementById('flash-error');
            
            if (successFlash) {
                successFlash.remove();
            }
            
            if (errorFlash) {
                errorFlash.remove();
            }
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>