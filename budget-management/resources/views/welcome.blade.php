<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Budget Manager') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
        
        .hero-section {
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
            color: white;
            padding: 100px 0;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #3498db;
        }
        
        .testimonial-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        
        .pricing-card {
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .pricing-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .pricing-header {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
        }
        
        .pricing-price {
            font-size: 2rem;
            font-weight: 700;
            margin: 20px 0;
        }
        
        .cta-section {
            background: #f8f9fa;
            padding: 80px 0;
        }
        
        .section-padding {
            padding: 80px 0;
        }
    </style>
</head>
<body>
    @include('partials.navigation')
    
    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <h1 class="display-4 mb-4">Maîtrisez vos finances personnelles</h1>
                    <p class="lead mb-5">Une solution simple et efficace pour gérer votre budget, suivre vos dépenses et atteindre vos objectifs financiers.</p>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg mr-3">Commencer gratuitement</a>
                        <a href="{{ route('features') }}" class="btn btn-outline-light btn-lg">Découvrir les fonctionnalités</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="mb-3">Fonctionnalités principales</h2>
                <p class="text-muted">Tout ce dont vous avez besoin pour gérer efficacement votre argent.</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <h4>Gestion de budget</h4>
                        <p class="text-muted">Créez des budgets personnalisés et suivez vos dépenses par catégorie.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <h4>Visualisation des données</h4>
                        <p class="text-muted">Analysez vos habitudes financières avec des graphiques clairs et intuitifs.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <h4>Objectifs d'épargne</h4>
                        <p class="text-muted">Définissez des objectifs financiers et suivez votre progression en temps réel.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4>Alertes personnalisées</h4>
                        <p class="text-muted">Recevez des notifications lorsque vous approchez des limites de votre budget.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4>Rapports détaillés</h4>
                        <p class="text-muted">Générez des rapports personnalisés pour analyser vos finances sur la durée.</p>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h4>Sécurité garantie</h4>
                        <p class="text-muted">Vos données financières sont protégées par un chiffrement de pointe.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- How It Works Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="mb-3">Comment ça marche ?</h2>
                <p class="text-muted">Commencez à gérer vos finances en trois étapes simples.</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 60px; height: 60px;">
                                <h3 class="mb-0">1</h3>
                            </div>
                            <h4>Créez votre compte</h4>
                            <p class="text-muted">Inscrivez-vous gratuitement en quelques secondes et configurez votre profil.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 60px; height: 60px;">
                                <h3 class="mb-0">2</h3>
                            </div>
                            <h4>Définissez votre budget</h4>
                            <p class="text-muted">Créez des catégories de dépenses et allouez-y des montants selon vos besoins.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-4" style="width: 60px; height: 60px;">
                                <h3 class="mb-0">3</h3>
                            </div>
                            <h4>Suivez vos dépenses</h4>
                            <p class="text-muted">Enregistrez facilement vos dépenses et visualisez votre progression financière.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="section-padding">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="mb-3">Ce que nos utilisateurs disent</h2>
                <p class="text-muted">Des milliers de personnes améliorent leur gestion financière grâce à notre application.</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <img src="{{ asset('images/testimonial-1.jpg') }}" alt="Témoignage" class="testimonial-avatar">
                            <h5>Sarah Dubois</h5>
                            <p class="text-muted mb-3">Consultante</p>
                            <p>"Grâce à cette application, j'ai enfin pu mettre de l'ordre dans mes finances. J'économise maintenant plus de 200€ par mois !"</p>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <img src="{{ asset('images/testimonial-2.jpg') }}" alt="Témoignage" class="testimonial-avatar">
                            <h5>Thomas Martin</h5>
                            <p class="text-muted mb-3">Développeur</p>
                            <p>"Interface intuitive et fonctionnalités complètes. J'utilise l'application quotidiennement pour suivre mes dépenses et atteindre mes objectifs."</p>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <img src="{{ asset('images/testimonial-3.jpg') }}" alt="Témoignage" class="testimonial-avatar">
                            <h5>Marie Leclerc</h5>
                            <p class="text-muted mb-3">Enseignante</p>
                            <p>"Les rapports mensuels m'ont permis d'identifier où je dépensais trop. J'ai pu économiser pour mes vacances d'été !"</p>
                            <div class="text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pricing Section -->
    <section class="section-padding bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="mb-3">Forfaits adaptés à vos besoins</h2>
                <p class="text-muted">Choisissez le plan qui correspond à votre situation financière.</p>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card pricing-card h-100 border-0 shadow-sm">
                        <div class="pricing-header">
                            <h4>Gratuit</h4>
                        </div>
                        <div class="card-body text-center">
                            <div class="pricing-price">0 MAD</div>
                            <p class="text-muted">par mois</p>
                            <ul class="list-unstyled">
                                <li class="py-2 border-bottom">Gestion de budget de base</li>
                                <li class="py-2 border-bottom">Suivi des dépenses limité</li>
                                <li class="py-2 border-bottom">1 compte</li>
                                <li class="py-2 border-bottom">Rapports mensuels</li>
                                <li class="py-2 text-muted">Pas d'objectifs financiers</li>
                                <li class="py-2 text-muted">Pas d'alertes personnalisées</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-block mt-4">Commencer gratuitement</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card pricing-card h-100 border-0 shadow">
                        <div class="pricing-header bg-primary text-white">
                            <h4>Premium</h4>
                            <span class="badge badge-light">Populaire</span>
                        </div>
                        <div class="card-body text-center">
                            <div class="pricing-price">59 MAD</div>
                            <p class="text-muted">par mois</p>
                            <ul class="list-unstyled">
                                <li class="py-2 border-bottom">Gestion de budget avancée</li>
                                <li class="py-2 border-bottom">Suivi des dépenses illimité</li>
                                <li class="py-2 border-bottom">Jusqu'à 5 comptes</li>
                                <li class="py-2 border-bottom">Rapports détaillés</li>
                                <li class="py-2 border-bottom">Objectifs financiers</li>
                                <li class="py-2 border-bottom">Alertes personnalisées</li>
                            </ul>
                            <a href="{{ route('register') }}?plan=premium" class="btn btn-primary btn-block mt-4">Choisir Premium</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card pricing-card h-100 border-0 shadow-sm">
                        <div class="pricing-header">
                            <h4>Famille</h4>
                        </div>
                        <div class="card-body text-center">
                            <div class="pricing-price">99 MAD</div>
                            <p class="text-muted">par mois</p>
                            <ul class="list-unstyled">
                                <li class="py-2 border-bottom">Tout dans Premium</li>
                                <li class="py-2 border-bottom">Jusqu'à 10 comptes</li>
                                <li class="py-2 border-bottom">Budgets partagés</li>
                                <li class="py-2 border-bottom">Rapports familiaux</li>
                                <li class="py-2 border-bottom">Contrôle parental</li>
                                <li class="py-2 border-bottom">Support prioritaire</li>
                            </ul>
                            <a href="{{ route('register') }}?plan=family" class="btn btn-outline-primary btn-block mt-4">Choisir Famille</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="cta-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <h2 class="mb-4">Prêt à prendre le contrôle de vos finances ?</h2>
                    <p class="lead mb-5">Inscrivez-vous gratuitement et commencez à gérer votre budget dès aujourd'hui.</p>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Créer mon compte gratuitement</a>
                </div>
            </div>
        </div>
    </section>
    
    @include('partials.footer')
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
