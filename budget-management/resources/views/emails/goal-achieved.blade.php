@component('mail::message')
# Félicitations ! Objectif atteint

Bonjour {{ $user->prenom }},

Nous avons le plaisir de vous annoncer que vous avez atteint l'un de vos objectifs financiers !

@component('mail::panel')
## Objectif : {{ $goal->titre }}

**Type** : {{ $goal->type == 'epargne' ? 'Épargne' : 'Réduction de dépenses' }}  
**Catégorie** : {{ $goal->categorie->nom }}  
**Montant cible** : {{ number_format($goal->montant_cible, 2) }} MAD  
**Date de début** : {{ $goal->date_debut->format('d/m/Y') }}  
**Date de fin prévue** : {{ $goal->date_fin->format('d/m/Y') }}  
**Date de réalisation** : {{ now()->format('d/m/Y') }}
@endcomponent

@if($goal->type == 'epargne')
Bravo pour votre discipline financière ! Cette réussite vous rapproche de vos objectifs à long terme.
@else
Félicitations pour votre maîtrise budgétaire ! Cette réduction de dépenses contribue à améliorer votre santé financière.
@endif

@component('mail::button', ['url' => route('user.goals.show', $goal->id), 'color' => 'success'])
Voir les détails de l'objectif
@endcomponent

Continuez sur cette lancée !

Cordialement,<br>
L'équipe {{ config('app.name') }}

@component('mail::subcopy')
Si vous ne souhaitez plus recevoir ce type de notifications, vous pouvez modifier vos préférences dans votre [profil]({{ route('user.profile.edit') }}).
@endcomponent
@endcomponent
