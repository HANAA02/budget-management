<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        {{-- Logo ou Titre --}}
        <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
            {{-- <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" height="30" class="mr-2"> --}}
            <h4 class="mb-0">MaîtriseBudget</h4>
        </a>

        {{-- Bouton menu mobile --}}
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Menu principal --}}
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('home') ? 'active' : '' }}" href="{{ url('/') }}">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('features') ? 'active' : '' }}" href="{{ route('features') }}">Fonctionnalités</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('pricing') ? 'active' : '' }}" href="{{ route('pricing') }}">Tarifs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('about') ? 'active' : '' }}" href="{{ route('about') }}">À propos</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                    </li>
                    
                @else
                    <li class="nav-item {{ Route::is('user.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.dashboard') }}">
                            <i class="fa fa-tachometer-alt"></i> Tableau de bord
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('user.budgets.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.budgets.index') }}">
                            <i class="fa fa-wallet"></i> Budgets
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('user.expenses.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.expenses.index') }}">
                            <i class="fa fa-receipt"></i> Dépenses
                        </a>
                    </li>
                    <li class="nav-item {{ Route::is('user.incomes.*') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('user.incomes.index') }}">
                            <i class="fa fa-money-bill-wave"></i> Revenus
                        </a>
                    </li>

                    {{-- Menu déroulant Plus --}}
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-plus"></i> Plus
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('user.goals.index') }}">
                                <i class="fa fa-bullseye"></i> Objectifs
                            </a>
                            <a class="dropdown-item" href="{{ route('user.alerts.index') }}">
                                <i class="fa fa-bell"></i> Alertes
                            </a>
                            <a class="dropdown-item" href="{{ route('user.reports.index') }}">
                                <i class="fa fa-chart-pie"></i> Rapports
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('user.accounts.index') }}">
                                <i class="fa fa-university"></i> Comptes
                            </a>
                        </div>
                    </li>
                @endguest
            </ul>

            {{-- Espace de droite (connexion / profil) --}}
            <ul class="navbar-nav ml-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('login') ? 'active' : '' }}" href="{{ route('login') }}">Connexion</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-primary btn-sm mt-1 {{ Route::is('register') ? 'active' : '' }}"
                                href="{{ route('register') }}">Inscription</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            @if(Auth::user()->avatar)
                                <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->prenom }}"
                                    class="rounded-circle mr-1" width="24">
                            @else
                                <i class="fa fa-user-circle"></i>
                            @endif
                            {{ Auth::user()->prenom }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('user.profile.edit') }}">
                                <i class="fa fa-user-cog"></i> Mon profil
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out-alt"></i> Déconnexion
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
