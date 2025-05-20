@extends('layouts.app')

@section('title', 'Conditions d\'Utilisation')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="text-center mb-5">Conditions d'Utilisation</h1>
                    
                    <div class="mb-5">
                        <p class="text-muted">Dernière mise à jour : {{ date('d/m/Y') }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>1. Acceptation des conditions</h2>
                        <p>En utilisant notre application de gestion de budget, vous acceptez d'être lié par les présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser notre service.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>2. Description du service</h2>
                        <p>Notre application vous permet de :</p>
                        <ul>
                            <li>Gérer vos revenus et dépenses personnels</li>
                            <li>Créer et suivre des budgets personnalisés</li>
                            <li>Visualiser vos finances à travers des graphiques et tableaux</li>
                            <li>Recevoir des alertes concernant votre budget</li>
                            <li>Définir et suivre des objectifs financiers</li>
                        </ul>
                        <p>Nous nous réservons le droit de modifier, suspendre ou interrompre tout aspect du service à tout moment, y compris la disponibilité de toute fonctionnalité, base de données ou contenu.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>3. Inscription et sécurité du compte</h2>
                        <p>Pour utiliser notre service, vous devez créer un compte en fournissant des informations précises et complètes. Vous êtes responsable du maintien de la confidentialité de votre mot de passe et de toutes les activités qui se produisent sous votre compte.</p>
                        <p>Vous vous engagez à :</p>
                        <ul>
                            <li>Créer un mot de passe sécurisé et le garder confidentiel</li>
                            <li>Nous informer immédiatement de toute utilisation non autorisée de votre compte</li>
                            <li>Vous assurer de vous déconnecter de votre compte à la fin de chaque session</li>
                            <li>Ne pas partager votre compte avec des tiers</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2>4. Utilisation du service</h2>
                        <p>Vous acceptez d'utiliser le service uniquement à des fins légales et conformément aux présentes conditions. Vous vous engagez à ne pas :</p>
                        <ul>
                            <li>Utiliser le service pour des activités illégales ou frauduleuses</li>
                            <li>Tenter d'accéder à des comptes, systèmes ou réseaux sans autorisation</li>
                            <li>Introduire des virus, chevaux de Troie, ou tout autre code malveillant</li>
                            <li>Perturber ou interférer avec l'intégrité ou la performance du service</li>
                            <li>Collecter ou stocker des informations personnelles sur d'autres utilisateurs</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2>5. Contenu de l'utilisateur</h2>
                        <p>Notre service vous permet de stocker et de gérer vos données financières personnelles. Vous conservez tous les droits sur vos données et êtes entièrement responsable de leur exactitude et de leur légalité.</p>
                        <p>En utilisant notre service, vous nous accordez une licence limitée pour héberger, stocker et traiter vos données uniquement dans le but de vous fournir le service.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>6. Limitation de responsabilité</h2>
                        <p>Notre application est fournie "telle quelle" et "selon disponibilité", sans garantie d'aucune sorte. Nous ne garantissons pas que :</p>
                        <ul>
                            <li>Le service répondra à vos exigences spécifiques</li>
                            <li>Le service sera ininterrompu, rapide, sécurisé ou sans erreur</li>
                            <li>Les résultats obtenus seront exacts ou fiables</li>
                            <li>Les conseils financiers générés par l'application sont adaptés à votre situation personnelle</li>
                        </ul>
                        <p>Vous utilisez l'application à vos propres risques. Nous ne serons pas responsables des dommages directs, indirects, accessoires, spéciaux ou consécutifs résultant de votre utilisation du service.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>7. Conseils financiers</h2>
                        <p>Notre application fournit des outils pour vous aider à gérer votre budget, mais ne constitue pas un conseil financier professionnel. Pour des conseils financiers personnalisés, veuillez consulter un professionnel qualifié.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>8. Modifications des conditions</h2>
                        <p>Nous nous réservons le droit de modifier ces conditions d'utilisation à tout moment. Les modifications prendront effet dès leur publication sur cette page. Nous vous informerons des changements importants par email ou par notification dans l'application. Votre utilisation continue du service après ces modifications constitue votre acceptation des nouvelles conditions.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>9. Résiliation</h2>
                        <p>Vous pouvez mettre fin à votre utilisation du service à tout moment en supprimant votre compte. Nous nous réservons le droit de suspendre ou de résilier votre accès au service si vous violez ces conditions d'utilisation ou si nous soupçonnons une utilisation frauduleuse ou abusive.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>10. Loi applicable</h2>
                        <p>Ces conditions sont régies par les lois en vigueur et seront interprétées conformément à celles-ci, sans égard aux conflits de principes juridiques.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>11. Contact</h2>
                        <p>Si vous avez des questions concernant ces conditions d'utilisation, veuillez nous contacter à :</p>
                        <p>Email : <a href="mailto:legal@budgetapp.com">legal@budgetapp.com</a></p>
                        <p>Adresse : [Votre adresse]</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection