@component('mail::message')
# Bienvenue sur {{ config('app.name') }} !

Bonjour {{ $user->prenom }},

Nous sommes ravis de vous compter parmi les utilisateurs de notre application de gestion de budget. Avec {{ config('app.name') }}, vous allez pouvoir :

- Créer et gérer vos budgets mensuels
- Suivre vos dépenses et revenus
- Définir des objectifs financiers personnalisés
- Recevoir des alertes et des rapports détaillés

@component('mail::panel')
## Commencez dès maintenant

1. **Créez votre premier budget** en définissant les montants alloués à chaque catégorie
2. **Enregistrez vos revenus** pour avoir une vision claire de vos finances
3. **Suivez vos dépenses** quotidiennes pour rester dans les limites que vous vous êtes fixées
4. **Définissez des objectifs** pour épargner ou réduire certaines dépenses
@endcomponent

@component('mail::button', ['url' => route('user.dashboard'), 'color' => 'primary'])
Accéder à mon tableau de bord
@endcomponent

Si vous avez des questions ou besoin d'assistance, n'hésitez pas à contacter notre équipe de support.

Cordialement,<br>
L'équipe {{ config('app.name') }}

@component('mail::subcopy')
Vous recevez cet email car vous venez de créer un compte sur {{ config('app.name') }}. Si vous n'êtes pas à l'origine de cette inscription, veuillez nous contacter immédiatement.
@endcomponent
@endcomponent