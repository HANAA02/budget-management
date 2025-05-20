<!-- pages/contact.blade.php -->
@extends('layouts.app')

@section('content')
<!-- Header Section -->
<section class="page-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Contactez-nous</h1>
                <p class="lead mb-5">Des questions ? Des suggestions ? Nous sommes là pour vous aider.</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info Section -->
<section class="section-padding">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-5 mb-lg-0 fadeIn">
                <div class="contact-info">
                    <h2 class="section-title mb-4">Informations de contact</h2>
                    <p class="lead mb-4">N'hésitez pas à nous contacter par l'un des moyens suivants :</p>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h5>Adresse</h5>
                            <p>123 Boulevard Mohammed V<br>Casablanca, 20250<br>Maroc</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="contact-text">
                            <h5>Téléphone</h5>
                            <p>+212 522 123 456</p>
                            <p>Lun - Ven, 9h - 18h</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-text">
                            <h5>Email</h5>
                            <p>contact@budgetapp.com</p>
                            <p>support@budgetapp.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <div class="contact-text">
                            <h5>Support</h5>
                            <p>Support en ligne 24/7</p>
                            <p>Chat en direct disponible</p>
                        </div>
                    </div>
                    
                    <div class="contact-social mt-4">
                        <h5>Suivez-nous</h5>
                        <div class="social-links">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8 fadeIn">
                <div class="contact-form-card">
                    <h3 class="mb-4">Envoyez-nous un message</h3>
                    
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    <form action="{{ route('contact.submit') }}" method="POST" id="contactForm">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="name" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-4">
                                <div class="form-group">
                                    <label for="subject" class="form-label">Sujet <span class="text-danger">*</span></label>
                                    <select class="form-select @error('subject') is-invalid @enderror" id="subject" name="subject" required>
                                        <option value="" selected disabled>Sélectionnez un sujet</option>
                                        <option value="Support technique" {{ old('subject') == 'Support technique' ? 'selected' : '' }}>Support technique</option>
                                        <option value="Question sur le produit" {{ old('subject') == 'Question sur le produit' ? 'selected' : '' }}>Question sur le produit</option>
                                        <option value="Facturation" {{ old('subject') == 'Facturation' ? 'selected' : '' }}>Facturation</option>
                                        <option value="Partenariat" {{ old('subject') == 'Partenariat' ? 'selected' : '' }}>Partenariat</option>
                                        <option value="Autre" {{ old('subject') == 'Autre' ? 'selected' : '' }}>Autre</option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-check mb-4">
                            <input class="form-check-input @error('privacy') is-invalid @enderror" type="checkbox" id="privacy" name="privacy" required {{ old('privacy') ? 'checked' : '' }}>
                            <label class="form-check-label" for="privacy">
                                J'ai lu et j'accepte la <a href="{{ route('privacy') }}">politique de confidentialité</a> <span class="text-danger">*</span>
                            </label>
                            @error('privacy')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i> Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container-fluid p-0">
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3323.8854991023786!2d-7.6192650846104905!3d33.592945880731695!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xda7d282e67e0c51%3A0xc4c071b4623de02c!2sBoulevard%20Mohammed%20V%2C%20Casablanca!5e0!3m2!1sfr!2sma!4v1620835842432!5m2!1sfr!2sma" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5 fadeIn">
                <h2 class="section-title">Questions fréquentes</h2>
                <p class="section-subtitle">Trouvez rapidement des réponses à vos questions.</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-10 mx-auto fadeIn">
                <div class="accordion contact-faq" id="contactFaq">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Comment puis-je obtenir une assistance technique ?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                <p>Pour obtenir une assistance technique, vous pouvez :</p>
                                <ul>
                                    <li>Utiliser le chat en direct disponible dans l'application</li>
                                    <li>Envoyer un email à support@budgetapp.com</li>
                                    <li>Remplir le formulaire de contact sur cette page</li>
                                    <li>Consulter notre <a href="{{ route('faq') }}">centre d'aide</a> pour les questions courantes</li>
                                </ul>
                                <p>Notre équipe de support est disponible 24/7 et s'engage à répondre à toutes les demandes dans un délai de 24 heures.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Comment puis-je signaler un problème avec l'application ?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                <p>Si vous rencontrez un problème avec l'application, vous pouvez :</p>
                                <ol>
                                    <li>Utiliser l'option "Signaler un problème" dans les paramètres de l'application</li>
                                    <li>Envoyer un email à support@budgetapp.com avec le mot "BUG" dans l'objet</li>
                                    <li>Remplir le formulaire de contact sur cette page en sélectionnant "Support technique" comme sujet</li>
                                </ol>
                                <p>Pour nous aider à résoudre le problème plus rapidement, veuillez inclure le plus de détails possible : la version de l'application, votre appareil, les étapes pour reproduire le problème et, si possible, des captures d'écran.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                J'ai une suggestion pour améliorer l'application. Comment puis-je la partager ?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                <p>Nous apprécions vos suggestions ! Vous pouvez partager vos idées de plusieurs façons :</p>
                                <ul>
                                    <li>Utilisez l'option "Suggestions" dans le menu de l'application</li>
                                    <li>Envoyez un email à suggestions@budgetapp.com</li>
                                    <li>Remplissez le formulaire de contact sur cette page en sélectionnant "Autre" comme sujet et en mentionnant qu'il s'agit d'une suggestion</li>
                                </ul>
                                <p>Toutes les suggestions sont examinées par notre équipe produit. Bien que nous ne puissions pas répondre individuellement à chaque suggestion, nous prenons en compte tous les retours pour améliorer continuellement notre application.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Comment puis-je annuler mon abonnement ou demander un remboursement ?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#contactFaq">
                            <div class="accordion-body">
                                <p>Pour annuler votre abonnement :</p>
                                <ol>
                                    <li>Connectez-vous à votre compte</li>
                                    <li>Accédez à "Paramètres" > "Abonnement"</li>
                                    <li>Cliquez sur "Gérer l'abonnement" puis "Annuler l'abonnement"</li>
                                </ol>
                                <p>Pour demander un remboursement, veuillez nous contacter à billing@budgetapp.com ou utiliser le formulaire de contact en sélectionnant "Facturation" comme sujet. Conformément à notre politique de remboursement, nous offrons un remboursement intégral dans les 30 jours suivant l'achat si vous n'êtes pas satisfait de notre service.</p>
                            </div>
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
                <h2 class="mb-4">Vous n'avez pas trouvé votre réponse ?</h2>
                <p class="lead mb-5">Consultez notre centre d'aide complet pour plus d'informations sur notre application et ses fonctionnalités.</p>
                <a href="{{ route('faq') }}" class="btn btn-lg">Consulter le centre d'aide</a>
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
    
    /* Contact Info Styles */
    .contact-item {
        display: flex;
        margin-bottom: 30px;
    }
    
    .contact-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary-light), var(--primary));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 20px;
        flex-shrink: 0;
        box-shadow: 0 8px 15px rgba(67, 97, 238, 0.3);
    }
    
    .contact-text h5 {
        margin-bottom: 5px;
        color: var(--gray-800);
    }
    
    .contact-text p {
        margin-bottom: 5px;
        color: var(--gray-600);
    }
    
    .contact-social h5 {
        margin-bottom: 15px;
        color: var(--gray-800);
    }
    
    .social-links {
        display: flex;
        gap: 10px;
    }
    
    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--gray-100);
        color: var(--gray-700);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
    }
    
    .social-link:hover {
        background: var(--primary);
        color: white;
        transform: translateY(-3px);
    }
    
    /* Contact Form Styles */
    .contact-form-card {
        background: white;
        border-radius: var(--card-border-radius);
        padding: 40px;
        box-shadow: var(--box-shadow);
    }
    
    .form-label {
        font-weight: 500;
        color: var(--gray-700);
        margin-bottom: 8px;
    }
    
    .form-control, .form-select {
        padding: 12px 15px;
        border-radius: 8px;
        border: 1px solid var(--gray-300);
        transition: var(--transition);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
    }
    
    /* Map Section Styles */
    .map-section {
        height: 450px;
    }
    
    .map-container {
        width: 100%;
        height: 100%;
    }
    
    /* FAQ Styles */
    .contact-faq {
        border-radius: var(--card-border-radius);
        overflow: hidden;
        box-shadow: var(--box-shadow);
    }
    
    .accordion-item {
        border: none;
        border-bottom: 1px solid var(--gray-200);
    }
    
    .accordion-item:last-child {
        border-bottom: none;
    }
    
    .accordion-button {
        padding: 20px;
        font-weight: 600;
        color: var(--gray-800);
        background: white;
    }
    
    .accordion-button:not(.collapsed) {
        color: var(--primary);
        background: rgba(67, 97, 238, 0.05);
        box-shadow: none;
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: var(--gray-200);
    }
    
    .accordion-button::after {
        background-size: 16px;
        width: 16px;
        height: 16px;
    }
    
    .accordion-body {
        padding: 0 20px 20px;
    }
    
    .accordion-body ul, .accordion-body ol {
        padding-left: 20px;
        margin-bottom: 15px;
    }
    
    .accordion-body li {
        margin-bottom: 8px;
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
        
        // Validation du formulaire de contact
        const contactForm = document.getElementById('contactForm');
        
        if (contactForm) {
            contactForm.addEventListener('submit', function(event) {
                let isValid = true;
                
                // Validation du nom
                const nameInput = document.getElementById('name');
                if (!nameInput.value.trim()) {
                    nameInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    nameInput.classList.remove('is-invalid');
                }
                
                // Validation de l'email
                const emailInput = document.getElementById('email');
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailInput.value.trim() || !emailPattern.test(emailInput.value)) {
                    emailInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    emailInput.classList.remove('is-invalid');
                }
                
                // Validation du sujet
                const subjectInput = document.getElementById('subject');
                if (!subjectInput.value) {
                    subjectInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    subjectInput.classList.remove('is-invalid');
                }
                
                // Validation du message
                const messageInput = document.getElementById('message');
                if (!messageInput.value.trim()) {
                    messageInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    messageInput.classList.remove('is-invalid');
                }
                
                // Validation de la case à cocher
                const privacyInput = document.getElementById('privacy');
                if (!privacyInput.checked) {
                    privacyInput.classList.add('is-invalid');
                    isValid = false;
                } else {
                    privacyInput.classList.remove('is-invalid');
                }
                
                if (!isValid) {
                    event.preventDefault();
                }
            });
        }
    });
</script>
@endsection