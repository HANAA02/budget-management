@extends('layouts.app')

@section('title', 'Foire Aux Questions')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="text-center mb-5">Foire Aux Questions</h1>
                    
                    <div class="accordion" id="faqAccordion">
                        <!-- Question 1 -->
                        <div class="accordion-item mb-3 border rounded">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    Comment créer mon premier budget ?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Pour créer votre premier budget, suivez ces étapes simples :</p>
                                    <ol>
                                        <li>Connectez-vous à votre compte</li>
                                        <li>Accédez à la section "Budgets" depuis le tableau de bord</li>
                                        <li>Cliquez sur le bouton "Créer un nouveau budget"</li>
                                        <li>Définissez la période de votre budget (mensuel, trimestriel, etc.)</li>
                                        <li>Ajoutez vos sources de revenus</li>
                                        <li>Personnalisez la répartition des catégories selon vos besoins</li>
                                        <li>Cliquez sur "Enregistrer"</li>
                                    </ol>
                                    <p>Votre budget sera automatiquement généré et vous pourrez commencer à suivre vos dépenses.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Question 2 -->
                        <div class="accordion-item mb-3 border rounded">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Comment ajouter une nouvelle dépense ?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Pour ajouter une nouvelle dépense :</p>
                                    <ol>
                                        <li>Depuis votre tableau de bord, cliquez sur "Dépenses"</li>
                                        <li>Cliquez sur le bouton "Ajouter une dépense"</li>
                                        <li>Sélectionnez la catégorie concernée</li>
                                        <li>Entrez le montant, la date et une description</li>
                                        <li>Cliquez sur "Enregistrer"</li>
                                    </ol>
                                    <p>La dépense sera automatiquement comptabilisée dans votre budget et les graphiques seront mis à jour.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Question 3 -->
                        <div class="accordion-item mb-3 border rounded">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Comment personnaliser les catégories de budget ?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Pour personnaliser vos catégories de budget :</p>
                                    <ol>
                                        <li>Accédez à la page "Budget" et sélectionnez le budget à modifier</li>
                                        <li>Cliquez sur "Modifier les catégories"</li>
                                        <li>Ajustez les pourcentages alloués à chaque catégorie</li>
                                        <li>Pour ajouter une nouvelle catégorie, cliquez sur "Ajouter une catégorie"</li>
                                        <li>Pour supprimer une catégorie, cliquez sur l'icône de corbeille</li>
                                        <li>Cliquez sur "Enregistrer" pour confirmer vos modifications</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Question 4 -->
                        <div class="accordion-item mb-3 border rounded">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    Comment configurer des alertes pour mon budget ?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Pour configurer des alertes :</p>
                                    <ol>
                                        <li>Accédez à la section "Alertes" depuis le menu principal</li>
                                        <li>Cliquez sur "Nouvelle alerte"</li>
                                        <li>Sélectionnez la catégorie de budget concernée</li>
                                        <li>Définissez le seuil d'alerte (ex: 75% du budget utilisé)</li>
                                        <li>Choisissez le type de notification (email, application)</li>
                                        <li>Cliquez sur "Enregistrer"</li>
                                    </ol>
                                    <p>Vous recevrez désormais des notifications lorsque vos dépenses dans cette catégorie atteindront le seuil défini.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Question 5 -->
                        <div class="accordion-item mb-3 border rounded">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    Comment exporter mes rapports financiers ?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <p>Pour exporter vos rapports financiers :</p>
                                    <ol>
                                        <li>Accédez à la section "Rapports" depuis votre tableau de bord</li>
                                        <li>Sélectionnez la période souhaitée</li>
                                        <li>Cliquez sur "Générer un rapport"</li>
                                        <li>Une fois le rapport généré, cliquez sur le bouton "Exporter"</li>
                                        <li>Choisissez le format souhaité (PDF, Excel, CSV)</li>
                                        <li>Le téléchargement de votre rapport commencera automatiquement</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h3>Vous ne trouvez pas la réponse à votre question ?</h3>
                        <p>N'hésitez pas à nous contacter via le <a href="{{ route('pages.contact') }}">formulaire de contact</a> ou par email à <a href="mailto:support@budgetapp.com">support@budgetapp.com</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection