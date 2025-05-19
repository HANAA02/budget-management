<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" height="30">
                {{ config('app.name') }}
            </a>
            
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Menu de navigation gauche -->
                <ul class="navbar-nav mr-auto">
                    @auth
                        <li class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('user.dashboard') }}">
                                <i class="fa fa-tachometer-alt"></i> Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('user.budgets.*') || request()->routeIs('user.expenses.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#" id="financeDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-wallet"></i> Finances
                            </a>
                            <div class="dropdown-menu" aria-labelledby="financeDropdown">
                                <a class="dropdown-item" href="{{ route('user.budgets.index') }}">
                                    <i class="fa fa-wallet fa-fw"></i> Budgets
                                </a>
                                <a class="dropdown-item" href="{{ route('user.expenses.index') }}">
                                    <i class="fa fa-receipt fa-fw"></i> Dépenses
                                </a>
                                <a class="dropdown-item" href="{{ route('user.incomes.index') }}">
                                    <i class="fa fa-money-bill-wave fa-fw"></i> Revenus
                                </a>
                            </div>
                        </li>
                        <li class="nav-item dropdown {{ request()->routeIs('user.goals.*') || request()->routeIs('user.alerts.*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle" href="#" id="planningDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bullseye"></i> Planification
                            </a>
                            <div class="dropdown-menu" aria-labelledby="planningDropdown">
                                <a class="dropdown-item" href="{{ route('user.goals.index') }}">
                                    <i class="fa fa-bullseye fa-fw"></i> Objectifs
                                </a>
                                <a class="dropdown-item" href="{{ route('user.alerts.index') }}">
                                    <i class="fa fa-bell fa-fw"></i> Alertes
                                </a>
                            </div>
                        </li>
                        <li class="nav-item {{ request()->routeIs('user.reports.*') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('user.reports.index') }}">
                                <i class="fa fa-chart-pie"></i> Rapports
                            </a>
                        </li>
                    @endauth
                </ul>

                <!-- Menu de navigation droite -->
                <ul class="navbar-nav ml-auto">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Connexion') }}</a>
                        </li>
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Inscription') }}</a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item dropdown">
                            <a id="notificationsDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                @if(isset($notificationsCount) && $notificationsCount > 0)
                                    <span class="badge badge-danger">{{ $notificationsCount }}</span>
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notificationsDropdown">
                                <h6 class="dropdown-header">Notifications</h6>
                                <div class="notifications-container">
                                    @if(isset($notifications) && count($notifications) > 0)
                                        @foreach($notifications as $notification)
                                            <a class="dropdown-item notification-item {{ $notification->read_at ? '' : 'unread' }}" href="{{ $notification->data['url'] ?? '#' }}">
                                                <div class="notification-icon">
                                                    <i class="fa {{ $notification->data['icon'] ?? 'fa-bell' }}"></i>
                                                </div>
                                                <div class="notification-content">
                                                    <div class="notification-text">{{ $notification->data['message'] }}</div>
                                                    <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                                                </div>
                                            </a>
                                        @endforeach
                                    @else
                                        <div class="dropdown-item text-center">
                                            <span class="text-muted">Aucune notification</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-center" href="{{ route('user.notifications.index') }}">
                                    Voir toutes les notifications
                                </a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->prenom }}" class="rounded-circle mr-1" style="width: 24px; height: 24px; object-fit: cover;">
                                @else
                                    <i class="fa fa-user-circle mr-1"></i>
                                @endif
                                {{ Auth::user()->prenom }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('user.profile.edit') }}">
                                    <i class="fa fa-user-cog fa-fw"></i> Mon profil
                                </a>
                                <a class="dropdown-item" href="{{ route('user.accounts.index') }}">
                                    <i class="fa fa-university fa-fw"></i> Mes comptes
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out-alt fa-fw"></i> {{ __('Déconnexion') }}
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
</header>

<style>
    .notification-dropdown {
        width: 320px;
        padding: 0;
    }
    
    .notifications-container {
        max-height: 350px;
        overflow-y: auto;
    }
    
    .notification-item {
        display: flex;
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .notification-item.unread {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .notification-icon {
        margin-right: 10px;
        color: #007bff;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 50%;
    }
    
    .notification-content {
        flex: 1;
    }
    
    .notification-text {
        font-size: 0.9rem;
        margin-bottom: 3px;
    }
    
    .notification-time {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>
