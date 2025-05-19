@extends('layouts.user')

@section('header-title', 'Mes alertes')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Alertes et notifications</h2>
        <p class="mt-1 text-sm text-gray-500">Gérez vos alertes pour rester informé de votre situation financière</p>
    </div>
    <div class="mt-4 md:mt-0">
        <a href="{{ route('alerts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            <i class="fas fa-plus mr-2"></i> Créer une alerte
        </a>
    </div>
</div>

<!-- Centre de notifications -->
<div class="bg-white shadow rounded-lg mb-6">
    <div class="px-6 py-5 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Centre de notifications</h3>
            <div class="flex space-x-4">
                <button class="text-sm text-primary-600 hover:text-primary-900" id="markAllReadBtn">
                    <i class="fas fa-check mr-1"></i> Tout marquer comme lu
                </button>
                <button class="text-sm text-gray-600 hover:text-gray-900">
                    <i class="fas fa-cog mr-1"></i> Paramètres
                </button>
            </div>
        </div>
    </div>
    
    <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
        @forelse($notifications as $notification)
            <div class="p-4 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }} hover:bg-gray-50">
                <div class="flex items-start">
                    <div class="flex-shrink-0 pt-0.5">
                        <div class="h-10 w-10 rounded-full bg-{{ $notification->type_color }}-100 flex items-center justify-center">
                            <i class="fas fa-{{ $notification->icon }} text-{{ $notification->type_color }}-600"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900">{{ $notification->title }}</p>
                            <p class="text-sm text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <p class="text-sm text-gray-500">{{ $notification->message }}</p>
                        @if(!$notification->read_at)
                            <button class="mt-2 text-xs text-primary-600 hover:text-primary-900 mark-read-btn" data-id="{{ $notification->id }}">
                                Marquer comme lu
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    <i class="fas fa-bell-slash text-3xl"></i>
                </div>
                <h3 class="text-sm font-medium text-gray-900">Aucune notification</h3>
                <p class="mt-1 text-sm text-gray-500">Vous n'avez aucune notification non lue pour le moment.</p>
            </div>
        @endforelse
    </div>

    @if(count($notifications) > 0)
        <div class="bg-gray-50 px-6 py-3 text-right">
            <span class="text-sm text-gray-500">{{ $notifications->count() }} notification(s)</span>
        </div>
    @endif
</div>

<!-- Alertes actives -->
<div>
    <h3 class="text-lg font-medium text-gray-900 mb-4">Alertes actives</h3>

    @if(count($alerts) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($alerts as $alert)
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-{{ $alert->type_color }}-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-{{ $alert->icon }} text-{{ $alert->type_color }}-600"></i>
                                </div>
                                <h4 class="text-lg font-medium text-gray-900">{{ $alert->title }}</h4>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-gray-400 hover:text-gray-500">
                                    <i class="fas fa-bell{{ $alert->active ? '' : '-slash' }}"></i>
                                </button>
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="text-gray-400 hover:text-gray-500">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                        <div class="py-1">
                                            <a href="{{ route('alerts.edit', $alert->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Modifier</a>
                                            <form action="{{ route('alerts.destroy', $alert->id) }}" method="POST" class="block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100" role="menuitem">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-gray-500 mb-4">{{ $alert->description }}</p>
                        
                        <div class="space-y-3">
                            <!-- Condition de l'alerte -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Condition</h5>
                                <div class="flex items-center">
                                    @if($alert->type === 'budget_limit')
                                        <span class="text-sm text-gray-900">
                                            <span class="font-medium">Budget {{ $alert->category->nom }}</span> dépasse {{ $alert->threshold }}%
                                        </span>
                                    @elseif($alert->type === 'expense_amount')
                                        <span class="text-sm text-gray-900">
                                            <span class="font-medium">Dépense</span> supérieure à {{ number_format($alert->threshold, 2) }} {{ $alert->currency }}
                                        </span>
                                    @elseif($alert->type === 'account_balance')
                                        <span class="text-sm text-gray-900">
                                            <span class="font-medium">{{ $alert->account->nom }}</span> solde inférieur à {{ number_format($alert->threshold, 2) }} {{ $alert->currency }}
                                        </span>
                                    @elseif($alert->type === 'goal_reminder')
                                        <span class="text-sm text-gray-900">
                                            <span class="font-medium">Objectif {{ $alert->goal->title }}</span> - {{ $alert->threshold }} jours avant échéance
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Fréquence -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Fréquence</h5>
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-900">
                                        @if($alert->frequency === 'once')
                                            Une seule fois
                                        @elseif($alert->frequency === 'daily')
                                            Quotidienne
                                        @elseif($alert->frequency === 'weekly')
                                            Hebdomadaire
                                        @elseif($alert->frequency === 'monthly')
                                            Mensuelle
                                        @else
                                            Lorsque la condition est remplie
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Méthodes de notification -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Notifications</h5>
                                <div class="flex items-center space-x-3">
                                    @if($alert->notify_by_email)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-envelope mr-1"></i> Email
                                        </span>
                                    @endif
                                    
                                    @if($alert->notify_in_app)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-bell mr-1"></i> Application
                                        </span>
                                    @endif
                                    
                                    @if($alert->notify_by_sms)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-sms mr-1"></i> SMS
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Statut -->
                            <div>
                                <h5 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Statut</h5>
                                <div class="flex items-center">
                                    @if($alert->active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Actif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Inactif
                                        </span>
                                    @endif

                                    @if($alert->last_triggered_at)
                                        <span class="ml-3 text-xs text-gray-500">
                                            Dernière alerte : {{ $alert->last_triggered_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Aucune alerte -->
        <div class="bg-white shadow rounded-lg p-6 text-center">
            <div class="mb-4">
                <div class="mx-auto h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-bell-slash text-gray-400 text-3xl"></i>
                </div>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Vous n'avez pas encore d'alertes</h3>
            <p class="text-gray-500 mb-6">Les alertes vous aident à suivre votre situation financière et à éviter les surprises.</p>
            
            <a href="{{ route('alerts.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-plus mr-2"></i> Créer ma première alerte
            </a>
        </div>
    @endif
</div>

<!-- Suggestions d'alertes -->
@if(count($suggestedAlerts) > 0)
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Suggestions d'alertes</h3>
        
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <p class="text-sm text-gray-500">
                    Voici quelques suggestions d'alertes basées sur vos habitudes financières.
                </p>
            </div>
            
            <div class="divide-y divide-gray-200">
                @foreach($suggestedAlerts as $suggestion)
                    <div class="p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-{{ $suggestion['type_color'] }}-100 flex items-center justify-center">
                                    <i class="fas fa-{{ $suggestion['icon'] }} text-{{ $suggestion['type_color'] }}-600"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-base font-medium text-gray-900">{{ $suggestion['title'] }}</h4>
                                <p class="mt-1 text-sm text-gray-500">{{ $suggestion['description'] }}</p>
                                <div class="mt-4">
                                    <a href="{{ route('alerts.create', ['suggestion' => $suggestion['id']]) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        Créer cette alerte
                                    </a>
                                    <button type="button" class="ml-3 inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dismiss-suggestion" data-id="{{ $suggestion['id'] }}">
                                        Ignorer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- Préférences de notification -->
<div class="mt-8 bg-white shadow rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Préférences de notification</h3>
    </div>
    
    <div class="p-6">
        <form action="{{ route('notifications.preferences.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Notifications par email -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="email_notifications" name="email_notifications" type="checkbox" value="1" {{ $preferences->email_notifications ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="email_notifications" class="font-medium text-gray-700">Notifications par email</label>
                        <p class="text-gray-500">Recevez des notifications par email lorsque des alertes sont déclenchées.</p>
                    </div>
                </div>
                
                <!-- Notifications dans l'application -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="in_app_notifications" name="in_app_notifications" type="checkbox" value="1" {{ $preferences->in_app_notifications ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="in_app_notifications" class="font-medium text-gray-700">Notifications dans l'application</label>
                        <p class="text-gray-500">Recevez des notifications dans l'application lorsque des alertes sont déclenchées.</p>
                    </div>
                </div>
                
                <!-- Notifications par SMS -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="sms_notifications" name="sms_notifications" type="checkbox" value="1" {{ $preferences->sms_notifications ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="sms_notifications" class="font-medium text-gray-700">Notifications par SMS</label>
                        <p class="text-gray-500">Recevez des notifications par SMS lorsque des alertes sont déclenchées (des frais peuvent s'appliquer).</p>
                        
                        @if(!$preferences->phone_verified)
                            <div class="mt-2">
                                <a href="{{ route('settings.phone') }}" class="text-xs text-primary-600 hover:text-primary-900">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Vous devez vérifier votre numéro de téléphone pour recevoir des SMS
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Résumé hebdomadaire -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="weekly_summary" name="weekly_summary" type="checkbox" value="1" {{ $preferences->weekly_summary ? 'checked' : '' }} class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="weekly_summary" class="font-medium text-gray-700">Résumé hebdomadaire</label>
                        <p class="text-gray-500">Recevez un email récapitulatif de vos finances chaque semaine.</p>
                    </div>
                </div>
                
                <!-- Bouton de sauvegarde -->
                <div class="text-right">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Enregistrer les préférences
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Script pour marquer les notifications comme lues
    document.addEventListener('DOMContentLoaded', function() {
        // Marquer une notification individuelle comme lue
        document.querySelectorAll('.mark-read-btn').forEach(button => {
            button.addEventListener('click', function() {
                const notificationId = this.dataset.id;
                markAsRead(notificationId, this.closest('div.p-4'));
            });
        });
        
        // Marquer toutes les notifications comme lues
        document.getElementById('markAllReadBtn').addEventListener('click', function() {
            markAllAsRead();
        });
        
        // Ignorer une suggestion d'alerte
        document.querySelectorAll('.dismiss-suggestion').forEach(button => {
            button.addEventListener('click', function() {
                const suggestionId = this.dataset.id;
                dismissSuggestion(suggestionId, this.closest('div.p-6'));
            });
        });
        
        function markAsRead(id, element) {
            fetch(`/api/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Changer l'apparence de la notification
                    element.classList.remove('bg-blue-50');
                    element.classList.add('bg-white');
                    
                    // Supprimer le bouton "Marquer comme lu"
                    const button = element.querySelector('.mark-read-btn');
                    if (button) button.remove();
                    
                    // Mettre à jour le compteur de notifications
                    updateNotificationCounter();
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        function markAllAsRead() {
            fetch('/api/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Changer l'apparence de toutes les notifications
                    document.querySelectorAll('.bg-blue-50').forEach(el => {
                        el.classList.remove('bg-blue-50');
                        el.classList.add('bg-white');
                    });
                    
                    // Supprimer tous les boutons "Marquer comme lu"
                    document.querySelectorAll('.mark-read-btn').forEach(btn => btn.remove());
                    
                    // Mettre à jour le compteur de notifications
                    updateNotificationCounter();
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        function dismissSuggestion(id, element) {
            fetch(`/api/alerts/suggestions/${id}/dismiss`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Masquer l'élément avec une animation
                    element.style.opacity = '0';
                    setTimeout(() => {
                        element.style.display = 'none';
                        
                        // Vérifier s'il reste des suggestions
                        const suggestions = document.querySelectorAll('.divide-y > div.p-6[style="display: block;"], .divide-y > div.p-6:not([style])');
                        if (suggestions.length === 0) {
                            // Masquer la section de suggestions
                            const suggestionsSection = document.querySelector('h3:contains("Suggestions d\'alertes")').closest('div');
                            if (suggestionsSection) {
                                suggestionsSection.style.display = 'none';
                            }
                        }
                    }, 300);
                }
            })
            .catch(error => console.error('Error:', error));
        }
        
        function updateNotificationCounter() {
            // Cette fonction mettrait à jour le compteur de notifications dans la navigation
            // Ceci est une implémentation simplifiée
            const unreadNotifications = document.querySelectorAll('.bg-blue-50').length;
            
            // Mettre à jour le compteur dans l'interface
            const counter = document.querySelector('.notification-counter');
            if (counter) {
                if (unreadNotifications > 0) {
                    counter.textContent = unreadNotifications;
                    counter.classList.remove('hidden');
                } else {
                    counter.classList.add('hidden');
                }
            }
        }
    });
</script>
@endpush