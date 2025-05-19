@props([
    'title' => 'Budget Manager',
    'username' => null,
    'avatarUrl' => null,
])

<div class="sidebar">
    <div class="sidebar-header">
        <h3 class="sidebar-title">{{ $title }}</h3>
        <div class="sidebar-toggle d-lg-none" onclick="toggleSidebar()">
            <i class="fa fa-bars"></i>
        </div>
    </div>
    
    @if($username)
        <div class="sidebar-user">
            <div class="sidebar-user-info">
                <div class="sidebar-user-avatar">
                    <img src="{{ $avatarUrl ?? asset('images/default-avatar.png') }}" alt="{{ $username }}">
                </div>
                <div class="sidebar-user-details">
                    <span class="sidebar-user-name">{{ $username }}</span>
                    <span class="sidebar-user-role text-muted">{{ auth()->user()->role ?? 'Utilisateur' }}</span>
                </div>
            </div>
        </div>
    @endif
    
    <div class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="sidebar-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <a href="{{ route('user.dashboard') }}" class="sidebar-link">
                    <i class="fa fa-tachometer-alt sidebar-icon"></i>
                    <span class="sidebar-text">Tableau de bord</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('user.accounts.*') ? 'active' : '' }}">
                <a href="{{ route('user.accounts.index') }}" class="sidebar-link">
                    <i class="fa fa-university sidebar-icon"></i>
                    <span class="sidebar-text">Comptes</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('user.budgets.*') ? 'active' : '' }}">
                <a href="{{ route('user.budgets.index') }}" class="sidebar-link">
                    <i class="fa fa-wallet sidebar-icon"></i>
                    <span class="sidebar-text">Budgets</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('user.expenses.*') ? 'active' : '' }}">
                <a href="{{ route('user.expenses.index') }}" class="sidebar-link">
                    <i class="fa fa-receipt sidebar-icon"></i>
                    <span class="sidebar-text">Dépenses</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('user.incomes.*') ? 'active' : '' }}">
                <a href="{{ route('user.incomes.index') }}" class="sidebar-link">
                    <i class="fa fa-money-bill-wave sidebar-icon"></i>
                    <span class="sidebar-text">Revenus</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('user.goals.*') ? 'active' : '' }}">
                <a href="{{ route('user.goals.index') }}" class="sidebar-link">
                    <i class="fa fa-bullseye sidebar-icon"></i>
                    <span class="sidebar-text">Objectifs</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('user.alerts.*') ? 'active' : '' }}">
                <a href="{{ route('user.alerts.index') }}" class="sidebar-link">
                    <i class="fa fa-bell sidebar-icon"></i>
                    <span class="sidebar-text">Alertes</span>
                </a>
            </li>
            
            <li class="sidebar-item {{ request()->routeIs('user.reports.*') ? 'active' : '' }}">
                <a href="{{ route('user.reports.index') }}" class="sidebar-link">
                    <i class="fa fa-chart-pie sidebar-icon"></i>
                    <span class="sidebar-text">Rapports</span>
                </a>
            </li>
            
            <li class="sidebar-divider"></li>
            
            <li class="sidebar-item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                <a href="{{ route('user.profile.edit') }}" class="sidebar-link">
                    <i class="fa fa-user-cog sidebar-icon"></i>
                    <span class="sidebar-text">Mon profil</span>
                </a>
            </li>
            
            <li class="sidebar-item">
                <a href="#" class="sidebar-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa fa-sign-out-alt sidebar-icon"></i>
                    <span class="sidebar-text">Déconnexion</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div>

<style>
    .sidebar {
        width: 260px;
        min-height: 100vh;
        background-color: #2c3e50;
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        transition: all 0.3s;
        z-index: 1000;
    }
    
    .sidebar-header {
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-title {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 500;
    }
    
    .sidebar-toggle {
        cursor: pointer;
        font-size: 1.2rem;
    }
    
    .sidebar-user {
        padding: 15px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-user-info {
        display: flex;
        align-items: center;
    }
    
    .sidebar-user-avatar {
        margin-right: 10px;
    }
    
    .sidebar-user-avatar img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .sidebar-user-details {
        display: flex;
        flex-direction: column;
    }
    
    .sidebar-user-name {
        font-weight: 500;
    }
    
    .sidebar-user-role {
        font-size: 0.8rem;
    }
    
    .sidebar-nav {
        padding: 15px 0;
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-item {
        margin-bottom: 5px;
    }
    
    .sidebar-link {
        display: block;
        padding: 10px 15px;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: all 0.3s;
        border-radius: 4px;
        margin: 0 10px;
    }
    
    .sidebar-link:hover, .sidebar-item.active .sidebar-link {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-icon {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .sidebar-divider {
        height: 1px;
        background-color: rgba(255, 255, 255, 0.1);
        margin: 15px 15px;
    }
    
    .content-wrapper {
        margin-left: 260px;
        padding: 20px;
        transition: all 0.3s;
    }
    
    @media (max-width: 992px) {
        .sidebar {
            margin-left: -260px;
        }
        
        .sidebar.active {
            margin-left: 0;
        }
        
        .content-wrapper {
            margin-left: 0;
        }
        
        .content-wrapper.active {
            margin-left: 260px;
        }
    }
    
    @media (max-width: 576px) {
        .content-wrapper.active {
            margin-left: 0;
            transform: translateX(260px);
        }
    }
</style>

<script>
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('active');
        document.querySelector('.content-wrapper').classList.toggle('active');
    }
</script>