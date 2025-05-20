<!-- pages/features.blade.php -->
@extends('layouts.app')
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

 
@section('content')
<!-- Header Section -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Fonctionnalités</h1>
                <p class="lead mb-5">Découvrez comment notre application de gestion de budget va transformer votre relation avec l'argent.</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Features Section -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-5 fadeIn">
                <div class="feature-block">
                    <div class="feature-icon-large">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <h3>Gestion de budget complète</h3>
                    <p>Créez et personnalisez facilement vos budgets mensuels. Répartissez automatiquement votre revenu entre différentes catégories de dépenses et suivez votre progression en temps réel.</p>
                    <ul class="feature-list">
                        <li>Création de budgets personnalisés</li>
                        <li>Répartition automatique des revenus</li>
                        <li>Catégories personnalisables</li>
                        <li>Suivi de progression en temps réel</li>
                        <li>Historique des budgets passés</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 mb-5 fadeIn">
                <div class="feature-image">
                    <img src="{{ asset('images/features/budget-management.png') }}" alt="Gestion de budget" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
        
        <div class="row flex-row-reverse">
            <div class="col-lg-6 mb-5 fadeIn">
                <div class="feature-block">
                    <div class="feature-icon-large">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <h3>Suivi des dépenses intelligent</h3>
                    <p>Gardez un œil sur toutes vos dépenses avec notre système de suivi intuitif. Catégorisez automatiquement vos transactions et identifiez facilement les tendances de consommation.</p>
                    <ul class="feature-list">
                        <li>Enregistrement rapide des dépenses</li>
                        <li>Catégorisation automatique</li>
                        <li>Reconnaissance des transactions récurrentes</li>
                        <li>Photos des reçus et factures</li>
                        <li>Historique complet des dépenses</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 mb-5 fadeIn">
                <div class="feature-image">
                    <img src="{{ asset('images/features/expense-tracking.png') }}" alt="Suivi des dépenses" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-5 fadeIn">
                <div class="feature-block">
                    <div class="feature-icon-large">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <h3>Visualisation des données</h3>
                    <p>Transformez vos données financières en informations exploitables grâce à nos graphiques et tableaux de bord interactifs. Visualisez clairement où va votre argent.</p>
                    <ul class="feature-list">
                        <li>Graphiques interactifs personnalisables</li>
                        <li>Tableau de bord intuitif</li>
                        <li>Comparaisons mensuelles et annuelles</li>
                        <li>Analyse des tendances de dépenses</li>
                        <li>Exportation des graphiques et rapports</li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-6 mb-5 fadeIn">
                <div class="feature-image">
                    <img src="{{ asset('images/features/data-visualization.png') }}" alt="Visualisation des données" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Secondary Features Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Encore plus de fonctionnalités</h2>
                <p class="section-subtitle">Découvrez toutes les façons dont notre application peut vous aider à maîtriser vos finances.</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4 fadeIn">
                <div class="secondary-feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h4>Objectifs financiers</h4>
                    <p>Définissez des objectifs d'épargne ou de réduction de dépenses et suivez votre progression vers ces objectifs avec des indicateurs visuels clairs.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="secondary-feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h4>Alertes personnalisées</h4>
                    <p>Configurez des alertes pour être notifié lorsque vous approchez des limites de budget ou lorsque des dépenses inhabituelles sont détectées.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="secondary-feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>Rapports détaillés</h4>
                    <p>Générez des rapports personnalisés pour analyser vos habitudes financières sur différentes périodes et identifiez les opportunités d'économies.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="secondary-feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h4>Gestion des revenus</h4>
                    <p>Suivez vos différentes sources de revenus, qu'ils soient réguliers ou ponctuels, et obtenez une vision claire de vos entrées d'argent.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="secondary-feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <h4>Comptes multiples</h4>
                    <p>Gérez plusieurs comptes bancaires, cartes de crédit et portefeuilles d'investissement dans une seule interface unifiée.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="secondary-feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h4>Sécurité renforcée</h4>
                    <p>Vos données financières sont protégées par un chiffrement de bout en bout et des mesures de sécurité avancées.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Features Section -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0 fadeIn">
                <div class="section-heading">
                    <h2>Accédez à vos finances partout</h2>
                    <p class="lead">Notre application mobile vous permet de gérer votre budget où que vous soyez.</p>
                </div>
                <div class="mobile-features">
                    <div class="mobile-feature-item">
                        <i class="fas fa-sync-alt feature-check"></i>
                        <div>
                            <h5>Synchronisation en temps réel</h5>
                            <p>Toutes vos données sont synchronisées entre vos appareils automatiquement.</p>
                        </div>
                    </div>
                    
                    <div class="mobile-feature-item">
                        <i class="fas fa-camera feature-check"></i>
                        <div>
                            <h5>Scan de reçus</h5>
                            <p>Prenez simplement une photo de vos reçus pour enregistrer vos dépenses rapidement.</p>
                        </div>
                    </div>
                    
                    <div class="mobile-feature-item">
                        <i class="fas fa-bell feature-check"></i>
                        <div>
                            <h5>Notifications mobiles</h5>
                            <p>Recevez des alertes importantes directement sur votre smartphone.</p>
                        </div>
                    </div>
                    
                    <div class="mobile-feature-item">
                        <i class="fas fa-map-marker-alt feature-check"></i>
                        <div>
                            <h5>Géolocalisation des dépenses</h5>
                            <p>Visualisez où vous dépensez votre argent sur une carte interactive.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 fadeIn">
                <div class="mobile-app-showcase">
                    <img src="{{ asset('images/features/mobile-app.png') }}" alt="Application mobile" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Integrations Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="section-title">Intégrations transparentes</h2>
                <p class="section-subtitle">Notre application s'intègre facilement avec de nombreux services financiers.</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-5 fadeIn">
                <div class="integration-card">
                    <img src="{{ asset('images/features/integration-bank.png') }}" alt="Intégration bancaire" class="integration-logo">
                    <h4>Banques</h4>
                    <p>Connectez-vous à plus de 1000 institutions financières pour importer automatiquement vos transactions.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-5 fadeIn">
                <div class="integration-card">
                    <img src="{{ asset('images/features/integration-payment.png') }}" alt="Services de paiement" class="integration-logo">
                    <h4>Services de paiement</h4>
                    <p>Intégrez facilement des services comme PayPal, Stripe et autres plateformes de paiement populaires.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-5 fadeIn">
                <div class="integration-card">
                    <img src="{{ asset('images/features/integration-accounting.png') }}" alt="Logiciels comptables" class="integration-logo">
                    <h4>Logiciels comptables</h4>
                    <p>Synchronisez vos données avec des logiciels de comptabilité pour une gestion financière complète.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-5 fadeIn">
                <div class="integration-card">
                    <img src="{{ asset('images/features/integration-investment.png') }}" alt="Plateformes d'investissement" class="integration-logo">
                    <h4>Investissements</h4>
                    <p>Suivez vos investissements et intégrez des plateformes de trading pour une vue d'ensemble de votre patrimoine.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-5 fadeIn">
                <div class="integration-card">
                    <img src="{{ asset('images/features/integration-export.png') }}" alt="Exportation de données" class="integration-logo">
                    <h4>Exportation de données</h4>
                    <p>Exportez facilement vos données financières aux formats CSV, PDF ou Excel pour une utilisation externe.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-5 fadeIn">
                <div class="integration-card">
                    <img src="{{ asset('images/features/integration-tax.png') }}" alt="Préparation fiscale" class="integration-logo">
                    <h4>Préparation fiscale</h4>
                    <p>Simplifiez votre déclaration d'impôts en exportant les données pertinentes vers des logiciels fiscaux.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container cta-content">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10 text-center fadeIn">
                <h2 class="mb-4">Prêt à transformer votre gestion financière ?</h2>
                <p class="lead mb-5">Rejoignez des milliers d'utilisateurs qui ont déjà amélioré leur santé financière grâce à notre application.</p>
                <a href="{{ route('register') }}" class="btn btn-lg">Commencer gratuitement</a>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #4361ee, #4cc9f0);
        color: white;
        padding: 100px 0 80px;
        margin-bottom: 0;
    }
    
    .feature-block {
        height: 100%;
        padding: 20px;
    }
    
    .feature-icon-large {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        color: white;
        border-radius: 50%;
        font-size: 2rem;
        box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
    }
    
    .feature-list {
        list-style: none;
        padding-left: 0;
        margin-top: 20px;
    }
    
    .feature-list li {
        padding: 8px 0;
        border-bottom: 1px solid var(--gray-200);
        position: relative;
        padding-left: 30px;
    }
    
    .feature-list li::before {
        content: "\f00c";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        color: var(--success);
        position: absolute;
        left: 0;
    }
    
    .feature-image {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .secondary-feature-card {
        background: white;
        border-radius: var(--card-border-radius);
        padding: 30px;
        height: 100%;
        transition: var(--transition);
        box-shadow: var(--box-shadow);
        text-align: center;
    }
    
    .secondary-feature-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-box-shadow);
    }
    
    .secondary-feature-card .feature-icon {
        margin-bottom: 20px;
    }
    
    .mobile-feature-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 25px;
    }
    
    .feature-check {
        color: var(--success);
        font-size: 1.2rem;
        margin-right: 15px;
        background: rgba(6, 214, 160, 0.1);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .mobile-app-showcase {
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .integration-card {
        background: white;
        border-radius: var(--card-border-radius);
        padding: 30px;
        height: 100%;
        transition: var(--transition);
        box-shadow: var(--box-shadow);
        text-align: center;
    }
    
    .integration-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-box-shadow);
    }
    
    .integration-logo {
        height: 70px;
        margin-bottom: 20px;
    }
    
    /* Animation pour l'apparition des sections lors du défilement */
    .fadeIn {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }
    
    .fadeIn.active {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation des éléments au scroll
        const fadeElements = document.querySelectorAll('.fadeIn');
        
        function checkFade() {
            fadeElements.forEach(el => {
                const rect = el.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                
                if (rect.top <= windowHeight * 0.85) {
                    el.classList.add('active');
                }
            });
        }
        
        // Exécuter au chargement
        checkFade();
        
        // Exécuter au scroll
        window.addEventListener('scroll', checkFade);
    });
</script>
@endsection