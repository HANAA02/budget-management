<div class="sidebar">
    <div class="sidebar-header">
        <div class="app-brand">
            <a href="{{ route('user.dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" height="30">
                <span class="brand-name">{{ config('app.name') }}</span>
            </a>
        </div>
        <button class="sidebar-toggler d-md-none" onclick="toggleSidebar()">
            <i class="fa fa-times"></i>
        </button>
    </div>
    
    <div class="sidebar-body">
        <ul class="nav">
            <li class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <a href="{{ route('user.dashboard') }}" class="nav-link">
                    <i class="fa fa-tachometer-alt"></i>
                    <span class="link-title">Tableau de bord</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->routeIs('user.accounts.*') ? 'active' : '' }}">
                <a href="{{ route('user.accounts.index') }}" class="nav-link">
                    <i class="fa fa-university"></i>
                    <span class="link-title">Comptes</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->routeIs('user.budgets.*') ? 'active' : '' }}">
                <a href="{{ route('user.budgets.index') }}" class="nav-link">
                    <i class="fa fa-wallet"></i>
                    <span class="link-title">Budgets</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->routeIs('user.expenses.*') ? 'active' : '' }}">
                <a href="{{ route('user.expenses.index') }}" class="nav-link">
                    <i class="fa fa-receipt"></i>
                    <span class="link-title">Dépenses</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->routeIs('user.incomes.*') ? 'active' : '' }}">
                <a href="{{ route('user.incomes.index') }}" class="nav-link">
                    <i class="fa fa-money-bill-wave"></i>
                    <span class="link-title">Revenus</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->routeIs('user.goals.*') ? 'active' : '' }}">
                <a href="{{ route('user.goals.index') }}" class="nav-link">
                    <i class="fa fa-bullseye"></i>
                    <span class="link-title">Objectifs</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->routeIs('user.alerts.*') ? 'active' : '' }}">
                <a href="{{ route('user.alerts.index') }}" class="nav-link">
                    <i class="fa fa-bell"></i>
                    <span class="link-title">Alertes</span>
                </a>
            </li>
            
            <li class="nav-item {{ request()->routeIs('user.reports.*') ? 'active' : '' }}">
                <a href="{{ route('user.reports.index') }}" class="nav-link">
                    <i class="fa fa-chart-pie"></i>
                    <span class="link-title">Rapports</span>
                </a>
            </li>
            
            <li class="nav-header">COMPTE</li>
            
            <li class="nav-item {{ request()->routeIs('user.profile.*') ? 'active' : '' }}">
                <a href="{{ route('user.profile.edit') }}" class="nav-link">
                    <i class="fa fa-user-cog"></i>
                    <span class="link-title">Mon profil</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('logout') }}" class="nav-link" 
                   onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                    <i class="fa fa-sign-out-alt"></i>
                    <span class="link-title">Déconnexion</span>
                </a>
                <form id="sidebar-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
</div>

<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 260px;
        background: #2c3e50;
        z-index: 1000;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        background: #1a2836;
    }
    
    .app-brand {
        display: flex;
        align-items: center;
    }
    
    .app-brand a {
        display: flex;
        align-items: center;
        color: #fff;
        text-decoration: none;
    }
    
    .brand-name {
        margin-left: 10px;
        font-weight: 600;
        color: #fff;
    }
    
    .sidebar-toggler {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 20px;
        cursor: pointer;
    }
    
    .sidebar-body {
        padding: 15px 0;
        height: calc(100% - 60px);
        overflow-y: auto;
    }
    
    .nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .nav-header {
        color: #8a97a8;
        font-size: 12px;
        font-weight: 600;
        padding: 15px 15px 5px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .nav-item {
        margin-bottom: 2px;
    }
    
    .nav-link {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        color: #cbd5e0;
        transition: all 0.3s ease;
        border-radius: 4px;
        margin: 0 8px;
    }
    
    .nav-link:hover, .nav-item.active .nav-link {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }
    
    .nav-link i {
        width: 20px;
        font-size: 16px;
        margin-right: 10px;
        text-align: center;
    }
    
    .link-title {
        font-size: 14px;
        font-weight: 500;
    }
    
    @media (max-width: 992px) {
        .sidebar {
            margin-left: -260px;
        }
        
        .sidebar.open {
            margin-left: 0;
        }
        
        .content-wrapper {
            margin-left: 0;
            width: 100%;
        }
    }
</style>

<script>
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('open');
    }
</script>
