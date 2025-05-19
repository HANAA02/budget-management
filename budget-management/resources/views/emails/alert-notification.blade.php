@component('mail::message')
# Alerte de budget

Bonjour {{ $user->prenom }},

Nous vous informons que l'une de vos catégories de budget a atteint le seuil d'alerte défini.

@component('mail::panel')
**Budget concerné** : {{ $budget->nom }}  
**Catégorie** : {{ $categorie->nom }}  
**Type d'alerte** : {{ $alert->type == 'pourcentage' ? 'Pourcentage atteint' : ($alert->type == 'montant' ? 'Montant atteint' : 'Dépassement de budget') }}  
**Seuil défini** : {{ $alert->type == 'pourcentage' ? $alert->seuil . '%' : number_format($alert->seuil, 2) . ' MAD' }}  
**Statut actuel** : {{ $pourcentage }}% utilisé ({{ number_format($montant_depense, 2) }} / {{ number_format($montant_alloue, 2) }} MAD)
@endcomponent

@component('mail::button', ['url' => route('user.budgets.show', $budget->id), 'color' => 'primary'])
Voir le détail du budget
@endcomponent

Nous vous recommandons de revoir vos dépenses dans cette catégorie afin de respecter votre budget.

Cordialement,<br>
L'équipe {{ config('app.name') }}

@component('mail::subcopy')
Si vous ne souhaitez plus recevoir ce type de notifications, vous pouvez modifier vos préférences dans votre [profil]({{ route('user.profile.edit') }}).
@endcomponent
@endcomponent