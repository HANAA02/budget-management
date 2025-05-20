@extends('layouts.app')

@section('title', 'Politique de Confidentialité')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="text-center mb-5">Politique de Confidentialité</h1>
                    
                    <div class="mb-5">
                        <p class="text-muted">Dernière mise à jour : {{ date('d/m/Y') }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Introduction</h2>
                        <p>Nous prenons la protection de vos données personnelles très au sérieux. Cette politique de confidentialité vous informe sur la manière dont nous collectons, utilisons et protégeons vos informations lorsque vous utilisez notre application de gestion de budget.</p>
                        <p>En utilisant notre service, vous acceptez les pratiques décrites dans cette politique.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Informations que nous collectons</h2>
                        <p>Nous collectons les informations suivantes :</p>
                        <ul>
                            <li><strong>Informations personnelles</strong> : nom, prénom, adresse email lors de la création de votre compte.</li>
                            <li><strong>Informations financières</strong> : revenus, dépenses, budgets et catégories de budget que vous créez dans l'application.</li>
                            <li><strong>Données d'utilisation</strong> : informations sur la façon dont vous utilisez l'application, fréquence de connexion, fonctionnalités utilisées.</li>
                            <li><strong>Informations techniques</strong> : adresse IP, type de navigateur, appareil utilisé, système d'exploitation.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Utilisation des informations</h2>
                        <p>Nous utilisons vos informations pour :</p>
                        <ul>
                            <li>Fournir, maintenir et améliorer notre service</li>
                            <li>Créer et gérer votre compte utilisateur</li>
                            <li>Vous permettre de gérer vos finances personnelles</li>
                            <li>Générer des statistiques et des rapports financiers personnalisés</li>
                            <li>Vous envoyer des notifications et alertes concernant votre budget</li>
                            <li>Résoudre les problèmes techniques et améliorer la sécurité</li>
                            <li>Vous communiquer des informations importantes concernant l'application</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Protection des données</h2>
                        <p>Nous mettons en œuvre des mesures de sécurité appropriées pour protéger vos informations personnelles contre tout accès non autorisé, altération, divulgation ou destruction. Ces mesures comprennent :</p>
                        <ul>
                            <li>Le chiffrement des données sensibles</li>
                            <li>L'authentification à deux facteurs (optionnelle)</li>
                            <li>Des sauvegardes régulières et sécurisées</li>
                            <li>Des audits de sécurité réguliers</li>
                            <li>La limitation de l'accès aux données personnelles au personnel autorisé uniquement</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Partage des informations</h2>
                        <p>Nous ne vendons pas vos données personnelles à des tiers. Nous pouvons partager vos informations dans les circonstances suivantes :</p>
                        <ul>
                            <li>Avec d'autres utilisateurs uniquement si vous activez la fonctionnalité de partage de budget</li>
                            <li>Avec nos fournisseurs de services qui nous aident à exploiter l'application (hébergement, services de paiement, etc.)</li>
                            <li>Si la loi l'exige ou pour protéger nos droits légaux</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Vos droits</h2>
                        <p>Vous disposez des droits suivants concernant vos données personnelles :</p>
                        <ul>
                            <li>Droit d'accès à vos données</li>
                            <li>Droit de rectification des données inexactes</li>
                            <li>Droit à l'effacement de vos données (droit à l'oubli)</li>
                            <li>Droit à la limitation du traitement</li>
                            <li>Droit à la portabilité des données</li>
                            <li>Droit d'opposition au traitement de vos données</li>
                        </ul>
                        <p>Pour exercer ces droits, contactez-nous à <a href="mailto:privacy@budgetapp.com">privacy@budgetapp.com</a>.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Conservation des données</h2>
                        <p>Nous conservons vos informations personnelles aussi longtemps que votre compte est actif ou que cela est nécessaire pour vous fournir nos services. Vous pouvez demander la suppression de votre compte à tout moment, auquel cas vos données seront supprimées ou anonymisées après une période de rétention de 30 jours (pour permettre une récupération en cas de suppression accidentelle).</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Cookies et technologies similaires</h2>
                        <p>Nous utilisons des cookies et des technologies similaires pour améliorer votre expérience, analyser l'utilisation de notre service et personnaliser le contenu. Vous pouvez contrôler l'utilisation des cookies via les paramètres de votre navigateur.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Modifications de cette politique</h2>
                        <p>Nous pouvons mettre à jour cette politique de confidentialité de temps à autre. Nous vous informerons de tout changement important par email ou par une notification dans l'application. Nous vous encourageons à consulter régulièrement cette page pour rester informé des mises à jour.</p>
                    </div>
                    
                    <div class="mb-4">
                        <h2>Contact</h2>
                        <p>Si vous avez des questions concernant cette politique de confidentialité, veuillez nous contacter à :</p>
                        <p>Email : <a href="mailto:privacy@budgetapp.com">privacy@budgetapp.com</a></p>
                        <p>Adresse : [Votre adresse]</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection