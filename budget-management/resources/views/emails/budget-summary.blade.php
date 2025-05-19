@component('mail::message')
# Récapitulatif de votre budget

Bonjour {{ $user->prenom }},

Voici un récapitulatif de votre budget **{{ $budget->nom }}** pour la période du **{{ $budget->date_debut->format('d/m/Y') }}** au **{{ $budget->date_fin->format('d/m/Y') }}**.

@component('mail::panel')
## Résumé

**Budget total** : {{ number_format($budget->montant_total, 2) }} MAD  
**Total dépensé** : {{ number_format($total_depense, 2) }} MAD  
**Reste disponible** : {{ number_format($budget->montant_total - $total_depense, 2) }} MAD  
**Progression globale** : {{ $progression }}%
@endcomponent

## Détail par catégorie

@component('mail::table')
| Catégorie | Budget alloué | Dépensé | Reste | Progression |
|:--------- |:-------------:| -------:| -----:| -----------:|
@foreach($categories as $categorie)
| {{ $categorie->categorie->nom }} | {{ number_format($categorie->montant_alloue, 2) }} | {{ number_format($categorie->depensesTotal ?? 0, 2) }} | {{ number_format($categorie->montant_alloue - ($categorie->depensesTotal ?? 0), 2) }} | {{ min(100, round((($categorie->depensesTotal ?? 0) / $categorie->montant_alloue) * 100)) }}% |
@endforeach
@endcomponent

@if(count($depenses_recentes) > 0)
## Dépenses récentes

@component('mail::table')
| Date | Description | Catégorie | Montant |
|:---- |:----------- |:--------- | -------:|
@foreach($depenses_recentes as $depense)
| {{ $depense->date_depense->format('d/m/Y') }} | {{ $depense->description }} | {{ $depense->categorieBudget->categorie->nom }} | {{ number_format($depense->montant, 2) }} |
@endforeach
@endcomponent
@endif

@component('mail::button', ['url' => route('user.budgets.show', $budget->id), 'color' => 'primary'])
Voir le détail complet
@endcomponent

Cordialement,<br>
L'équipe {{ config('app.name') }}

@component('mail::subcopy')
Ce récapitulatif vous est envoyé conformément à vos [préférences de notification]({{ route('user.profile.edit') }}).
@endcomponent
@endcomponent