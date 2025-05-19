<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Accueil') | {{ config('app.name', 'Budget Manager') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div id="app">
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="flex items-center">
                                <i class="fas fa-coins text-primary-600 text-2xl mr-2"></i>
                                <span class="text-xl font-bold text-gray-800">Budget<span class="text-primary-600">Manager</span></span>
                            </a>
                        </div>

                        <!-- Primary Navigation Menu -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition">
                                Accueil
                            </a>
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition">
                                    Tableau de bord
                                </a>
                                <a href="{{ route('budgets.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('budgets.*') ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition">
                                    Budgets
                                </a>
                                <a href="{{ route('expenses.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('expenses.*') ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition">
                                    Dépenses
                                </a>
                                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('reports.*') ? 'border-primary-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition">
                                    Rapports
                                </a>
                            @endauth
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Authentication Links -->
                        @guest
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 px-4 py-2 hover:text-primary-600">Connexion</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-3 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-800 focus:outline-none focus:border-primary-800 focus:ring ring-primary-300 disabled:opacity-25 transition">S'inscrire</a>
                            @endif
                        @else
                            <!-- Notifications -->
                            <div class="relative mr-3">
                                <button class="relative flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
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
                                <!-- Dropdown menu (hidden by default) -->
                            </div>

                            <!-- User Menu Dropdown -->
                            <div class="ml-3 relative" x-data="{ open: false }">
                                <div>
                                    <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                        <div class="flex items-center">
                                            @if(Auth::user()->avatar)
                                                <img class="h-8 w-8 rounded-full object-cover" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->nom }}">
                                            @else
                                                <div class="h-8 w-8 rounded-full bg-primary-600 flex items-center justify-center text-white font-semibold">
                                                    {{ substr(Auth::user()->nom, 0, 1) . substr(Auth::user()->prenom, 0, 1) }}
                                                </div>
                                            @endif
                                            <span class="ml-2">{{ Auth::user()->nom }}</span>
                                        </div>
                                        <div class="ml-1">
                                            <i class="fas fa-chevron-down"></i>
                                        </div>
                                    </button>
                                </div>

                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="user-menu-button" tabindex="-1">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Mon profil</a>
                                    
                                    @if(Auth::user()->role === 'admin')
                                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Administration</a>
                                    @endif
                                    
                                    <a href="{{ route('settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Paramètres</a>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Déconnexion</button>
                                    </form>
                                </div>
                            </div>
                        @endguest
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition" id="mobile-menu-button">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div class="hidden sm:hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'border-primary-500 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                        Accueil
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-primary-500 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                            Tableau de bord
                        </a>
                        <a href="{{ route('budgets.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('budgets.*') ? 'border-primary-500 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                            Budgets
                        </a>
                        <a href="{{ route('expenses.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('expenses.*') ? 'border-primary-500 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                            Dépenses
                        </a>
                        <a href="{{ route('reports.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('reports.*') ? 'border-primary-500 text-primary-700 bg-primary-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium">
                            Rapports
                        </a>
                    @endauth
                </div>

                <!-- Mobile Authentication Menu -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    @guest
                        <div class="mt-3 space-y-1">
                            <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                                Connexion
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                                    S'inscrire
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="flex items-center px-4">
                            @if(Auth::user()->avatar)
                                <div class="flex-shrink-0">
                                    <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->nom }}">
                                </div>
                            @else
                                <div class="h-10 w-10 rounded-full bg-primary-600 flex items-center justify-center text-white font-semibold">
                                    {{ substr(Auth::user()->nom, 0, 1) . substr(Auth::user()->prenom, 0, 1) }}
                                </div>
                            @endif
                            <div class="ml-3">
                                <div class="font-medium text-base text-gray-800">{{ Auth::user()->nom }} {{ Auth::user()->prenom }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <a href="{{ route('profile.edit') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                                Mon profil
                            </a>
                            
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                                    Administration
                                </a>
                            @endif
                            
                            <a href="{{ route('settings') }}" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                                Paramètres
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300">
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    @endguest
                </div>
            </div>
        </nav>

        <!-- Flash Messages -->
        @if (session('success'))
            <div id="flash-success" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
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
            <div id="flash-error" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
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

        <!-- Page Heading -->
        @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="mb-4 md:mb-0">
                        <p class="text-sm text-gray-500">&copy; {{ date('Y') }} {{ config('app.name', 'Budget Manager') }}. Tous droits réservés.</p>
                    </div>
                    <div class="flex space-x-6">
                        <a href="{{ route('about') }}" class="text-sm text-gray-500 hover:text-gray-700">À propos</a>
                        <a href="{{ route('contact') }}" class="text-sm text-gray-500 hover:text-gray-700">Contact</a>
                        <a href="{{ route('privacy') }}" class="text-sm text-gray-500 hover:text-gray-700">Confidentialité</a>
                        <a href="{{ route('terms') }}" class="text-sm text-gray-500 hover:text-gray-700">Conditions d'utilisation</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        // Toggle mobile menu
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
            const iconPaths = this.querySelectorAll('path');
            iconPaths.forEach(path => path.classList.toggle('hidden'));
            iconPaths.forEach(path => path.classList.toggle('inline-flex'));
        });
        
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