<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Mes alertes</h1>
      <router-link :to="{ name: 'user.alerts.create' }" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Créer une alerte
      </router-link>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Liste des alertes</h6>
        <div class="dropdown no-arrow">
          <button 
            class="btn btn-link btn-sm dropdown-toggle" 
            type="button"
            id="alertsFilterDropdown" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
          >
            {{ activeFilter === 'all' ? 'Toutes les alertes' : activeFilter === 'triggered' ? 'Alertes déclenchées' : 'Alertes non déclenchées' }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="alertsFilterDropdown">
            <li><a class="dropdown-item" href="#" @click.prevent="activeFilter = 'all'">Toutes les alertes</a></li>
            <li><a class="dropdown-item" href="#" @click.prevent="activeFilter = 'triggered'">Alertes déclenchées</a></li>
            <li><a class="dropdown-item" href="#" @click.prevent="activeFilter = 'not_triggered'">Alertes non déclenchées</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#" @click.prevent="statusFilter = 'all'">Tous les statuts</a></li>
            <li><a class="dropdown-item" href="#" @click.prevent="statusFilter = 'active'">Alertes actives</a></li>
            <li><a class="dropdown-item" href="#" @click.prevent="statusFilter = 'inactive'">Alertes inactives</a></li>
          </ul>
        </div>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des alertes...</p>
        </div>

        <div v-else-if="filteredAlerts.length === 0" class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Aucune alerte trouvée{{ activeFilter !== 'all' || statusFilter !== 'all' ? ' pour ce filtre.' : '.' }} 
          <router-link :to="{ name: 'user.alerts.create' }" class="btn btn-sm btn-primary mt-2">
            <i class="fas fa-plus me-1"></i> Créer une alerte
          </router-link>
        </div>

        <div v-else>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Cible</th>
                  <th>Condition</th>
                  <th>Statut</th>
                  <th>Dernière activation</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="alert in filteredAlerts" :key="alert.id" :class="{ 'table-danger': alert.triggered && !alert.acknowledged }">
                  <td>
                    <i :class="getAlertTypeIcon(alert.type)" class="me-2"></i>
                    {{ getAlertTypeName(alert.type) }}
                  </td>
                  <td>{{ getAlertTarget(alert) }}</td>
                  <td>
                    {{ getAlertCondition(alert) }}
                  </td>
                  <td>
                    <span 
                      class="badge" 
                      :class="alert.active ? 'bg-success' : 'bg-secondary'"
                    >
                      {{ alert.active ? 'Active' : 'Inactive' }}
                    </span>
                    <span 
                      v-if="alert.triggered" 
                      class="badge bg-danger ms-1"
                      :class="{ 'blink': alert.triggered && !alert.acknowledged }"
                    >
                      Déclenchée
                    </span>
                  </td>
                  <td>{{ alert.last_triggered_at ? formatDate(alert.last_triggered_at) : 'Jamais' }}</td>
                  <td>
                    <div class="btn-group">
                      <router-link 
                        :to="{ name: 'user.alerts.edit', params: { id: alert.id } }" 
                        class="btn btn-sm btn-primary"
                      >
                        <i class="fas fa-edit"></i>
                      </router-link>
                      <button 
                        class="btn btn-sm btn-warning" 
                        @click="acknowledgeAlert(alert)"
                        v-if="alert.triggered && !alert.acknowledged"
                        title="Marquer comme lue"
                      >
                        <i class="fas fa-check"></i>
                      </button>
                      <button 
                        class="btn btn-sm" 
                        :class="alert.active ? 'btn-secondary' : 'btn-success'"
                        @click="toggleAlertStatus(alert)"
                        :title="alert.active ? 'Désactiver' : 'Activer'"
                      >
                        <i :class="alert.active ? 'fas fa-pause' : 'fas fa-play'"></i>
                      </button>
                      <button 
                        class="btn btn-sm btn-danger" 
                        @click="deleteAlert(alert)"
                        title="Supprimer"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Notifications récentes</h6>
      </div>
      <div class="card-body">
        <div v-if="loadingNotifications" class="text-center py-3">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </div>
        <div v-else-if="notifications.length === 0" class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Aucune notification récente.
        </div>
        <div v-else>
          <div class="list-group">
            <div 
              v-for="notification in notifications" 
              :key="notification.id"
              class="list-group-item list-group-item-action"
              :class="{ 'list-group-item-danger': !notification.read }"
            >
              <div class="d-flex w-100 justify-content-between">
                <h6 class="mb-1">
                  <i :class="getAlertTypeIcon(notification.alert_type)" class="me-2"></i>
                  {{ notification.title }}
                </h6>
                <small>{{ formatDate(notification.created_at) }}</small>
              </div>
              <p class="mb-1">{{ notification.message }}</p>
              <div class="d-flex justify-content-between align-items-center">
                <small>{{ getAlertTarget(notification) }}</small>
                <button 
                  v-if="!notification.read" 
                  class="btn btn-sm btn-outline-primary"
                  @click="markNotificationAsRead(notification)"
                >
                  Marquer comme lue
                </button>
              </div>
            </div>
          </div>
          <div class="text-center mt-3" v-if="hasMoreNotifications">
            <button class="btn btn-outline-primary" @click="loadMoreNotifications">
              Voir plus de notifications
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserAlertsIndex',
  data() {
    return {
      alerts: [],
      notifications: [],
      loading: true,
      loadingNotifications: true,
      activeFilter: 'all',
      statusFilter: 'all',
      notificationPage: 1,
      hasMoreNotifications: false
    };
  },
  computed: {
    filteredAlerts() {
      let result = [...this.alerts];
      
      // Filtre par statut de déclenchement
      if (this.activeFilter === 'triggered') {
        result = result.filter(alert => alert.triggered);
      } else if (this.activeFilter === 'not_triggered') {
        result = result.filter(alert => !alert.triggered);
      }
      
      // Filtre par statut d'activation
      if (this.statusFilter === 'active') {
        result = result.filter(alert => alert.active);
      } else if (this.statusFilter === 'inactive') {
        result = result.filter(alert => !alert.active);
      }
      
      return result;
    }
  },
  methods: {
    fetchAlerts() {
      this.loading = true;
      axios.get('/api/user/alerts')
        .then(response => {
          this.alerts = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des alertes:', error);
          this.$toasted.error('Impossible de charger les alertes.');
          this.loading = false;
        });
    },
    fetchNotifications() {
      this.loadingNotifications = true;
      
      axios.get('/api/user/notifications', {
        params: { 
          page: this.notificationPage,
          type: 'alert'
        }
      })
        .then(response => {
          this.notifications = this.notificationPage === 1 
            ? response.data.data 
            : [...this.notifications, ...response.data.data];
          
          this.hasMoreNotifications = response.data.meta.current_page < response.data.meta.last_page;
          this.loadingNotifications = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des notifications:', error);
          this.loadingNotifications = false;
        });
    },
    loadMoreNotifications() {
      this.notificationPage++;
      this.fetchNotifications();
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const date = new Date(dateString);
      const now = new Date();
      const diffMs = now - date;
      const diffSec = Math.round(diffMs / 1000);
      const diffMin = Math.round(diffSec / 60);
      const diffHour = Math.round(diffMin / 60);
      const diffDay = Math.round(diffHour / 24);
      
      // Si moins d'une minute
      if (diffSec < 60) {
        return 'Il y a quelques secondes';
      }
      // Si moins d'une heure
      if (diffMin < 60) {
        return `Il y a ${diffMin} minute${diffMin > 1 ? 's' : ''}`;
      }
      // Si moins d'un jour
      if (diffHour < 24) {
        return `Il y a ${diffHour} heure${diffHour > 1 ? 's' : ''}`;
      }
      // Si moins d'une semaine
      if (diffDay < 7) {
        return `Il y a ${diffDay} jour${diffDay > 1 ? 's' : ''}`;
      }
      
      // Sinon, date formatée
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return date.toLocaleDateString('fr-FR', options);
    },
    getAlertTypeIcon(type) {
      switch (type) {
        case 'budget_category':
          return 'fas fa-tags';
        case 'budget_total':
          return 'fas fa-wallet';
        case 'account_balance':
          return 'fas fa-university';
        default:
          return 'fas fa-bell';
      }
    },
    getAlertTypeName(type) {
      switch (type) {
        case 'budget_category':
          return 'Catégorie de budget';
        case 'budget_total':
          return 'Budget total';
        case 'account_balance':
          return 'Solde de compte';
        default:
          return 'Inconnu';
      }
    },
    getAlertTarget(alert) {
      switch (alert.type) {
        case 'budget_category':
          return `${alert.budget_name} - ${alert.category_name}`;
        case 'budget_total':
          return alert.budget_name;
        case 'account_balance':
          return alert.account_name;
        default:
          return '';
      }
    },
    getAlertCondition(alert) {
      if (alert.condition === 'percentage') {
        return `${alert.threshold}% atteint`;
      } else if (alert.condition === 'amount') {
        return `${this.formatAmount(alert.threshold)} atteint`;
      } else if (alert.condition === 'below') {
        return `Inférieur à ${this.formatAmount(alert.threshold)}`;
      }
      
      return '';
    },
    formatAmount(amount, currency = 'MAD') {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: currency
      }).format(amount || 0);
    },
    acknowledgeAlert(alert) {
      axios.post(`/api/user/alerts/${alert.id}/acknowledge`)
        .then(response => {
          // Mettre à jour l'alerte dans la liste
          const index = this.alerts.findIndex(a => a.id === alert.id);
          if (index !== -1) {
            this.alerts[index].acknowledged = true;
          }
          
          this.$toasted.success('Alerte marquée comme lue.');
        })
        .catch(error => {
          console.error('Erreur lors de la confirmation de l\'alerte:', error);
          this.$toasted.error('Impossible de marquer l\'alerte comme lue.');
        });
    },
    toggleAlertStatus(alert) {
      axios.put(`/api/user/alerts/${alert.id}/toggle`)
        .then(response => {
          // Mettre à jour l'alerte dans la liste
          const index = this.alerts.findIndex(a => a.id === alert.id);
          if (index !== -1) {
            this.alerts[index].active = !this.alerts[index].active;
          }
          
          this.$toasted.success(`Alerte ${alert.active ? 'désactivée' : 'activée'} avec succès.`);
        })
        .catch(error => {
          console.error('Erreur lors du changement de statut de l\'alerte:', error);
          this.$toasted.error('Impossible de modifier le statut de l\'alerte.');
        });
    },
    deleteAlert(alert) {
      if (confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')) {
        axios.delete(`/api/user/alerts/${alert.id}`)
          .then(response => {
            // Supprimer l'alerte de la liste
            this.alerts = this.alerts.filter(a => a.id !== alert.id);
            
            this.$toasted.success('Alerte supprimée avec succès.');
          })
          .catch(error => {
            console.error('Erreur lors de la suppression de l\'alerte:', error);
            this.$toasted.error('Impossible de supprimer l\'alerte.');
          });
      }
    },
    markNotificationAsRead(notification) {
      axios.post(`/api/user/notifications/${notification.id}/read`)
        .then(response => {
          // Mettre à jour la notification dans la liste
          const index = this.notifications.findIndex(n => n.id === notification.id);
          if (index !== -1) {
            this.notifications[index].read = true;
          }
          
          this.$toasted.success('Notification marquée comme lue.');
        })
        .catch(error => {
          console.error('Erreur lors du marquage de la notification:', error);
          this.$toasted.error('Impossible de marquer la notification comme lue.');
        });
    }
  },
  mounted() {
    this.fetchAlerts();
    this.fetchNotifications();
  },
  metaInfo() {
    return {
      title: 'Mes alertes - Budget Manager'
    };
  }
};
</script>

<style scoped>
.blink {
  animation: blinker 1s linear infinite;
}

@keyframes blinker {
  50% {
    opacity: 0.5;
  }
}
</style>