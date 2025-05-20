@extends('layouts.app')

@section('title', 'Tarifs - Budget Manager')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="text-center mb-5">
                <h1 class="display-4 mb-3">Plans tarifaires flexibles</h1>
                <p class="lead">Choisissez l'option qui correspond le mieux à vos besoins financiers</p>
            </div>
            
            <div class="row">
                <!-- Plan Gratuit -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow transition-300">
                        <div class="card-header bg-white text-center py-4">
                            <h2 class="h3 mb-0">Gratuit</h2>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="text-center mb-4">
                                <span class="display-4">0 MAD</span>
                                <span class="text-muted">/mois</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 1 compte utilisateur</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 2 comptes bancaires</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Gestion des revenus et dépenses</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Répartition automatique du budget</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Visualisation de base</li>
                                <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i> Catégories personnalisées</li>
                                <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i> Historique (limité à 3 mois)</li>
                                <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i> Objectifs financiers</li>
                                <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i> Système d'alertes</li>
                                <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i> Exportation des rapports</li>
                            </ul>
                            <div class="mt-auto text-center">
                                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg w-100">Commencer gratuitement</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Standard -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow border-primary hover-shadow transition-300">
                        <div class="card-header bg-primary text-white text-center py-4">
                            <h2 class="h3 mb-0">Standard</h2>
                            <span class="badge bg-white text-primary mt-2">Le plus populaire</span>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="text-center mb-4">
                                <span class="display-4">49 MAD</span>
                                <span class="text-muted">/mois</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 1 compte utilisateur</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 5 comptes bancaires</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Gestion des revenus et dépenses</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Répartition automatique du budget</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Visualisations avancées</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Catégories personnalisées</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Historique complet</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Objectifs financiers</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Système d'alertes</li>
                                <li class="mb-2 text-muted"><i class="fas fa-times text-danger me-2"></i> Exportation des rapports</li>
                            </ul>
                            <div class="mt-auto text-center">
                                <a href="{{ route('register') }}?plan=standard" class="btn btn-primary btn-lg w-100">Choisir ce plan</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Plan Premium -->
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow transition-300">
                        <div class="card-header bg-dark text-white text-center py-4">
                            <h2 class="h3 mb-0">Premium</h2>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="text-center mb-4">
                                <span class="display-4">99 MAD</span>
                                <span class="text-muted">/mois</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 5 comptes utilisateurs</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Comptes bancaires illimités</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Gestion des revenus et dépenses</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Répartition automatique du budget</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Visualisations avancées</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Catégories personnalisées</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Historique complet</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Objectifs financiers illimités</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Système d'alertes avancé</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Exportation des rapports</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Support prioritaire</li>
                            </ul>
                            <div class="mt-auto text-center">
                                <a href="{{ route('register') }}?plan=premium" class="btn btn-outline-dark btn-lg w-100">Choisir ce plan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Comparaison des fonctionnalités -->
            <div class="card mt-5 shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="mb-0">Comparaison détaillée des fonctionnalités</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">Fonctionnalité</th>
                                    <th scope="col" class="text-center">Gratuit</th>
                                    <th scope="col" class="text-center">Standard</th>
                                    <th scope="col" class="text-center">Premium</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Comptes utilisateurs</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">1</td>
                                    <td class="text-center">5</td>
                                </tr>
                                <tr>
                                    <td>Comptes bancaires</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">Illimité</td>
                                </tr>
                                <tr>
                                    <td>Catégories de dépenses</td>
                                    <td class="text-center">Prédéfinies</td>
                                    <td class="text-center">Personnalisables</td>
                                    <td class="text-center">Personnalisables</td>
                                </tr>
                                <tr>
                                    <td>Historique</td>
                                    <td class="text-center">3 mois</td>
                                    <td class="text-center">12 mois</td>
                                    <td class="text-center">Illimité</td>
                                </tr>
                                <tr>
                                    <td>Objectifs financiers</td>
                                    <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                    <td class="text-center">3 maximum</td>
                                    <td class="text-center">Illimité</td>
                                </tr>
                                <tr>
                                    <td>Alertes</td>
                                    <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                    <td class="text-center">Basiques</td>
                                    <td class="text-center">Avancées</td>
                                </tr>
                                <tr>
                                    <td>Exportation de rapports</td>
                                    <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                    <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                                    <td class="text-center"><i class="fas fa-check text-success"></i></td>
                                </tr>
                                <tr>
                                    <td>Support client</td>
                                    <td class="text-center">Email</td>
                                    <td class="text-center">Email</td>
                                    <td class="text-center">Email & Téléphone</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Section -->
            <div class="mt-5">
                <h3 class="text-center mb-4">Questions fréquentes sur les abonnements</h3>
                
                <div class="accordion" id="pricingFaq">
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-white" id="pricingHeadingOne">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left text-decoration-none" type="button" data-toggle="collapse" data-target="#pricingCollapseOne" aria-expanded="true" aria-controls="pricingCollapseOne">
                                    Puis-je changer de plan à tout moment ?
                                </button>
                            </h2>
                        </div>
                        <div id="pricingCollapseOne" class="collapse show" aria-labelledby="pricingHeadingOne" data-parent="#pricingFaq">
                            <div class="card-body">
                                Oui, vous pouvez passer à un plan supérieur ou inférieur à tout moment. Si vous passez à un plan supérieur, la différence de prix sera calculée au prorata pour le reste de votre période de facturation. Si vous passez à un plan inférieur, le changement prendra effet à la fin de votre période de facturation actuelle.
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-white" id="pricingHeadingTwo">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed text-decoration-none" type="button" data-toggle="collapse" data-target="#pricingCollapseTwo" aria-expanded="false" aria-controls="pricingCollapseTwo">
                                    Y a-t-il un engagement de durée ?
                                </button>
                            </h2>
                        </div>
                        <div id="pricingCollapseTwo" class="collapse" aria-labelledby="pricingHeadingTwo" data-parent="#pricingFaq">
                            <div class="card-body">
                                Non, tous nos plans sont sans engagement. Vous pouvez annuler votre abonnement à tout moment, et vous continuerez à avoir accès aux fonctionnalités de votre plan jusqu'à la fin de la période de facturation en cours.
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-white" id="pricingHeadingThree">
                            <h2 class="mb-0">
                                <button class="btn btn-link btn-block text-left collapsed text-decoration-none" type="button" data-toggle="collapse" data-target="#pricingCollapseThree" aria-expanded="false" aria-controls="pricingCollapseThree">
                                    Proposez-vous des réductions pour un paiement annuel ?
                                </button>
                            </h2>
                        </div>
                        <div id="pricingCollapseThree" class="collapse" aria-labelledby="pricingHeadingThree" data-parent="#pricingFaq">
                            <div class="card-body">
                                Oui, nous offrons une réduction de 20% pour les paiements annuels sur tous nos plans payants. Cette option sera disponible lors de votre inscription ou dans les paramètres de votre compte.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Section -->
            <div class="text-center mt-5">
                <h3>Prêt à commencer votre gestion de budget ?</h3>
                <p class="mb-4">Inscrivez-vous maintenant et commencez à prendre le contrôle de vos finances</p>
                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5">Créer un compte</a>
            </div>
        </div>
    </div>
</div>
@endsection