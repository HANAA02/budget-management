<!-- pages/about.blade.php -->
@extends('layouts.app')

@section('content')
<!-- Header Section -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">À propos de nous</h1>
                <p class="lead mb-5">Découvrez notre mission et notre vision pour une meilleure gestion financière.</p>
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="section-padding">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0 fadeIn">
                <div class="about-content">
                    <h2 class="section-title">Notre histoire</h2>
                    <p class="lead mb-4">Une solution née d'un besoin personnel.</p>
                    <p>Tout a commencé en 2020 lorsque notre fondateur, confronté à des difficultés pour gérer efficacement son budget personnel, a décidé de créer un outil simple mais puissant pour résoudre ce problème.</p>
                    <p>Après des mois de développement et de tests avec un petit groupe d'utilisateurs, notre application de gestion de budget a été lancée officiellement en janvier 2021. Depuis, nous avons connu une croissance constante et aidé des milliers d'utilisateurs à prendre le contrôle de leurs finances.</p>
                    <p>Notre équipe s'est agrandie, mais notre mission reste la même : rendre la gestion financière accessible à tous et aider chacun à atteindre ses objectifs financiers.</p>
                </div>
            </div>
            <div class="col-lg-6 fadeIn">
                <div class="about-image">
                    <img src="{{ asset('images/about/our-story.jpg') }}" alt="Notre histoire" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Values Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5 fadeIn">
                <h2 class="section-title">Notre mission et nos valeurs</h2>
                <p class="section-subtitle">Ce qui nous guide au quotidien.</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-5 fadeIn">
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Notre mission</h3>
                    <p>Notre mission est de démocratiser l'éducation financière et de fournir des outils accessibles qui permettent à chacun de prendre des décisions financières éclairées.</p>
                    <p>Nous croyons que la transparence financière et une bonne compréhension de ses dépenses sont essentielles pour atteindre la liberté financière. C'est pourquoi nous développons continuellement des fonctionnalités innovantes qui simplifient la gestion budgétaire et rendent l'épargne plus intuitive.</p>
                </div>
            </div>
            
            <div class="col-lg-6 mb-5 fadeIn">
                <div class="mission-card">
                    <div class="mission-icon">
                        <i class="fas fa-compass"></i>
                    </div>
                    <h3>Notre vision</h3>
                    <p>Nous aspirons à créer un monde où le stress financier n'existe plus, où chaque personne dispose des connaissances et des outils nécessaires pour gérer efficacement ses finances.</p>
                    <p>Notre vision est de devenir la référence mondiale en matière de gestion financière personnelle, en offrant une plateforme complète qui accompagne nos utilisateurs à chaque étape de leur vie financière.</p>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12 text-center mb-5 fadeIn">
                <h3 class="mb-5">Nos valeurs fondamentales</h3>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4 fadeIn">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h4>Confiance et sécurité</h4>
                    <p>Nous prenons très au sérieux la protection de vos données. La sécurité et la confidentialité sont au cœur de toutes nos décisions.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4>Innovation constante</h4>
                    <p>Nous cherchons continuellement à améliorer notre application avec des fonctionnalités innovantes qui simplifient la gestion financière.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                    <h4>Accessibilité pour tous</h4>
                    <p>Nous croyons que chacun devrait avoir accès à des outils de gestion financière de qualité, quel que soit son niveau de revenus.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>Centré sur l'utilisateur</h4>
                    <p>Nos décisions sont guidées par les besoins et les retours de nos utilisateurs. Votre satisfaction est notre priorité absolue.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Impact positif</h4>
                    <p>Nous mesurons notre succès à l'aune de l'impact positif que nous avons sur la santé financière de nos utilisateurs.</p>
                </div>
            </div>
            
            <div class="col-md-4 mb-4 fadeIn">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-balance-scale"></i>
                    </div>
                    <h4>Transparence</h4>
                    <p>Nous pratiquons une transparence totale, tant dans notre modèle économique que dans notre communication avec nos utilisateurs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Team Section -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5 fadeIn">
                <h2 class="section-title">Notre équipe</h2>
                <p class="section-subtitle">Passionnés par la technologie et l'éducation financière.</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 fadeIn">
                <div class="team-card">
                    <div class="team-image">
                        <img src="{{ asset('images/about/team-1.jpg') }}" alt="Membre de l'équipe" class="img-fluid">
                    </div>
                    <div class="team-info">
                        <h4>Ahmed Khalil</h4>
                        <p class="team-role">Fondateur & CEO</p>
                        <p class="team-bio">Passionné de technologie et d'éducation financière, Ahmed a fondé l'entreprise après avoir constaté le manque d'outils de gestion budgétaire accessibles.</p>
                        <div class="team-social">
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 fadeIn">
                <div class="team-card">
                    <div class="team-image">
                        <img src="{{ asset('images/about/team-2.jpg') }}" alt="Membre de l'équipe" class="img-fluid">
                    </div>
                    <div class="team-info">
                        <h4>Sara Belkadi</h4>
                        <p class="team-role">Directrice Produit</p>
                        <p class="team-bio">Avec plus de 8 ans d'expérience dans la gestion de produits financiers, Sara veille à ce que notre application réponde parfaitement aux besoins des utilisateurs.</p>
                        <div class="team-social">
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 fadeIn">
                <div class="team-card">
                    <div class="team-image">
                        <img src="{{ asset('images/about/team-3.jpg') }}" alt="Membre de l'équipe" class="img-fluid">
                    </div>
                    <div class="team-info">
                        <h4>Karim Tazi</h4>
                        <p class="team-role">Lead Développeur</p>
                        <p class="team-bio">Expert en technologies web et mobiles, Karim dirige notre équipe de développement et s'assure que notre application est toujours à la pointe de l'innovation.</p>
                        <div class="team-social">
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 fadeIn">
                <div class="team-card">
                    <div class="team-image">
                        <img src="{{ asset('images/about/team-4.jpg') }}" alt="Membre de l'équipe" class="img-fluid">
                    </div>
                    <div class="team-info">
                        <h4>Leila Amrani</h4>
                        <p class="team-role">Responsable UX/UI</p>
                        <p class="team-bio">Designer passionnée par l'expérience utilisateur, Leila s'assure que notre application est non seulement fonctionnelle mais aussi intuitive et agréable à utiliser.</p>
                        <div class="team-social">
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-dribbble"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Achievement Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5 fadeIn">
                <h2 class="section-title">Notre impact</h2>
                <p class="section-subtitle">Des chiffres qui parlent d'eux-mêmes.</p>
            </div>
        </div>
        
        <div class="row text-center">
            <div class="col-md-3 mb-4 fadeIn">
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="achievement-number">50K+</div>
                    <div class="achievement-label">Utilisateurs actifs</div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4 fadeIn">
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="achievement-number">500M MAD</div>
                    <div class="achievement-label">Budgets gérés</div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4 fadeIn">
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <div class="achievement-number">100M MAD</div>
                    <div class="achievement-label">Économies réalisées</div>
                </div>
            </div>
            
            <div class="col-md-3 mb-4 fadeIn">
                <div class="achievement-card">
                    <div class="achievement-icon">
                        <i class="fas fa-globe-africa"></i>
                    </div>
                    <div class="achievement-number">15+</div>
                    <div class="achievement-label">Pays desservis</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partners Section -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5 fadeIn">
                <h2 class="section-title">Nos partenaires</h2>
                <p class="section-subtitle">Ils nous font confiance et nous accompagnent dans notre mission.</p>
            </div>
        </div>
        
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-2 col-md-3 col-6 mb-4 fadeIn">
                <div class="partner-logo">
                    <img src="{{ asset('images/about/partner-1.png') }}" alt="Partenaire" class="img-fluid">
                </div>
            </div>
            
            <div class="col-lg-2 col-md-3 col-6 mb-4 fadeIn">
                <div class="partner-logo">
                    <img src="{{ asset('images/about/partner-2.png') }}" alt="Partenaire" class="img-fluid">
                </div>
            </div>
            
            <div class="col-lg-2 col-md-3 col-6 mb-4 fadeIn">
                <div class="partner-logo">
                    <img src="{{ asset('images/about/partner-3.png') }}" alt="Partenaire" class="img-fluid">
                </div>
            </div>
            
            <div class="col-lg-2 col-md-3 col-6 mb-4 fadeIn">
                <div class="partner-logo">
                    <img src="{{ asset('images/about/partner-4.png') }}" alt="Partenaire" class="img-fluid">
                </div>
            </div>
            
            <div class="col-lg-2 col-md-3 col-6 mb-4 fadeIn">
                <div class="partner-logo">
                    <img src="{{ asset('images/about/partner-5.png') }}" alt="Partenaire" class="img-fluid">
                </div>
            </div>
            
            <div class="col-lg-2 col-md-3 col-6 mb-4 fadeIn">
                <div class="partner-logo">
                    <img src="{{ asset('images/about/partner-6.png') }}" alt="Partenaire" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Join Us Section -->
<section class="join-us-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="join-us-card fadeIn">
                    <div class="row align-items-center">
                        <div class="col-lg-8 mb-4 mb-lg-0">
                            <h2 class="mb-3">Rejoignez notre équipe</h2>
                            <p class="lead mb-4">Vous souhaitez contribuer à notre mission de démocratisation de l'éducation financière ?</p>
                            <p>Nous sommes toujours à la recherche de talents passionnés pour rejoindre notre équipe en pleine croissance. Découvrez nos opportunités de carrière et aidez-nous à transformer la façon dont les gens gèrent leurs finances.</p>
                        </div>
                        <div class="col-lg-4 text-center text-lg-end">
                            <a href="" class="btn btn-primary btn-lg">Voir nos offres</a>
                        </div>
                    </div>
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
                <h2 class="mb-4">Prêt à transformer votre relation avec l'argent ?</h2>
                <p class="lead mb-5">Rejoignez notre communauté d'utilisateurs qui ont pris le contrôle de leurs finances.</p>
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
    
    /* Mission Card Styles */
    .mission-card {
        background: white;
        border-radius: var(--card-border-radius);
        padding: 40px;
        height: 100%;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
    }
    
    .mission-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-box-shadow);
    }
    
    .mission-icon {
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
    
    /* Value Card Styles */
    .value-card {
        background: white;
        border-radius: var(--card-border-radius);
        padding: 30px;
        height: 100%;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        text-align: center;
    }
    
    .value-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-box-shadow);
    }
    
    .value-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        color: white;
        border-radius: 50%;
        font-size: 1.5rem;
        box-shadow: 0 8px 15px rgba(67, 97, 238, 0.3);
    }
    
    /* Team Card Styles */
    .team-card {
        border-radius: var(--card-border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        background: white;
        height: 100%;
    }
    
    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-box-shadow);
    }
    
    .team-image {
        position: relative;
        overflow: hidden;
    }
    
    .team-image img {
        width: 100%;
        transition: var(--transition);
    }
    
    .team-card:hover .team-image img {
        transform: scale(1.05);
    }
    
    .team-info {
        padding: 25px 20px;
        text-align: center;
    }
    
    .team-role {
        color: var(--primary);
        font-weight: 600;
        margin-bottom: 15px;
    }
    
    .team-bio {
        font-size: 0.9rem;
        color: var(--gray-600);
        margin-bottom: 20px;
    }
    
    .team-social {
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    
    .social-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gray-100);
        color: var(--gray-700);
        border-radius: 50%;
        transition: var(--transition);
    }
    
    .social-icon:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-3px);
    }
    
    /* Achievement Card Styles */
    .achievement-card {
        background: white;
        border-radius: var(--card-border-radius);
        padding: 30px;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        height: 100%;
    }
    
    .achievement-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--hover-box-shadow);
    }
    
    .achievement-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        color: white;
        border-radius: 50%;
        font-size: 1.5rem;
        box-shadow: 0 8px 15px rgba(67, 97, 238, 0.3);
    }
    
    .achievement-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--heading-color);
        margin-bottom: 10px;
    }
    
    .achievement-label {
        color: var(--gray-600);
        font-weight: 500;
    }
    
    /* Partner Logo Styles */
    .partner-logo {
        background: white;
        border-radius: var(--card-border-radius);
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--box-shadow);
        transition: var(--transition);
        height: 100px;
    }
    
    .partner-logo:hover {
        transform: translateY(-5px);
        box-shadow: var(--hover-box-shadow);
    }
    
    .partner-logo img {
        max-height: 60px;
        max-width: 100%;
        filter: grayscale(100%);
        opacity: 0.7;
        transition: var(--transition);
    }
    
    .partner-logo:hover img {
        filter: grayscale(0);
        opacity: 1;
    }
    
    /* Join Us Section Styles */
    .join-us-section {
        padding: 80px 0;
        background: linear-gradient(135deg, rgba(67, 97, 238, 0.05), rgba(76, 201, 240, 0.05));
    }
    
    .join-us-card {
        background: white;
        border-radius: var(--card-border-radius);
        padding: 50px;
        box-shadow: var(--box-shadow);
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