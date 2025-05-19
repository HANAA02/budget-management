# Suite des Pages Utilisateur

Je vais continuer à fournir les pages restantes. Commençons par compléter les pages d'alertes, puis nous poursuivrons avec les autres sections.

## Suite des Pages Alertes

### Edit.vue (Alertes)
```vue
<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Modifier l'alerte</h1>
      <router-link :to="{ name: 'user.alerts.index' }" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
      </router-link>
    </div>

    <div v-if="loading" class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <div v-else class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Détails de l'alerte</h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="updateAlert">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Note:</strong> Pour des raisons de cohérence, le type d'alerte, le budget, la catégorie et le compte ne peuvent pas être modifiés. 
            Si vous souhaitez changer ces paramètres, veuillez créer une nouvelle alerte.
          </div>

          <div class="mb-3">
            <label class="form-label">Type d'alerte</label>
            <input type="text" class="form-control" :value="getAlertTypeName(alert.type)" disabled />
          </div>

          <div v-if="alert.type === 'budget_category'" class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Budget</label>
              <input type="text" class="form-control" :value="getBudgetName(alert.budget_id)" disabled />
            </div>
            <div class="col-md-6">
              <label class="form-label">Catégorie</label>
              <input type="text" class="form-control" :value="getCategoryName(alert.categorie_budget_id)" disabled />
            </div>
          </div>

          <div v-if="alert.type === 'budget_total'" class="mb-3">
            <label class="form-label">Budget</label>
            <input type="text" class="form-control" :value="getBudgetName(alert.budget_id)" disabled />
          </div>

          <div v-if="alert.type === 'account_balance'" class="mb-3">
            <label class="form-label">Compte</label>
            <input type="text" class="form-control" :value="getAccountName(alert.account_id)" disabled />
          </div>

          <div class="mb-3">
            <label for="condition" class="form-label">Condition</label>
            <select id="condition" v-model="alert.condition" class="form-select" required>
              <option value="" disabled>Sélectionnez une condition</option>
              <option value="percentage">Pourcentage atteint</option>
              <option value="amount">Montant atteint</option>
              <option v-if="alert.type === 'account_balance'" value="below">Solde inférieur à</option>
            </select>
          </div>

          <div class="mb-3">
            <label :for="alert.condition === 'percentage' ? 'percentage_threshold' : 'amount_threshold'" class="form-label">
              {{ alert.condition === 'percentage' ? 'Seuil de pourcentage' : 'Seuil de montant' }}
            </label>
            <div class="input-group">
              <input 
                :type="alert.condition === 'percentage' ? 'number' : 'text'" 
                :id="alert.condition === 'percentage' ? 'percentage_threshold' : 'amount_threshold'" 
                v-model="alert.condition === 'percentage' ? alert.percentage_threshold : alert.amount_threshold"
                class="form-control" 
                :min="alert.condition === 'percentage' ? 0 : null"
                :max="alert.condition === 'percentage' ? 100 : null"
                :step="alert.condition === 'percentage' ? 1 : 0.01"
                required
              />
              <span class="input-group-text">{{ alert.condition === 'percentage' ? '%' : 'MAD' }}</span>
            </div>
            <small class="form-text text-muted" v-if="alert.condition === 'percentage'">
              L'alerte sera déclenchée lorsque le pourcentage d'utilisation atteindra cette valeur.
            </small>
            <small class="form-text text-muted" v-else>
              L'alerte sera déclenchée lorsque le montant atteindra cette valeur.
            </small>
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Message personnalisé (optionnel)</label>
            <textarea 
              id="message" 
              v-model="alert.message"
              class="form-control" 
              rows="2"
              placeholder="Message qui sera affiché avec l'alerte"
            ></textarea>
          </div>

          <div class="mb-3">
            <label for="frequency" class="form-label">Fréquence de notification</label>
            <select id="frequency" v-model="alert.frequency" class="form-select" required>
              <option value="once">Une fois seulement</option>
              <option value="daily">Quotidienne</option>
              <option value="weekly">Hebdomadaire</option>
            </select>
            <small class="form-text text-muted">
              Définissez à quelle fréquence vous souhaitez recevoir cette alerte une fois qu'elle a été déclenchée.
            </small>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <div class="form-check">
                <input 
                  type="checkbox" 
                  id="notification_app" 
                  v-model="alert.notification_app"
                  class="form-check-input" 
                />
                <label for="notification_app" class="form-check-label">
                  Notification dans l'application
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check">
                <input 
                  type="checkbox" 
                  id="notification_email" 
                  v-model="alert.notification_email"
                  class="form-check-input" 
                />
                <label for="notification_email" class="form-check-label">
                  Notification par email
                </label>
              </div>
            </div>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="active" 
              v-model="alert.active"
              class="form-check-input" 
            />
            <label for="active" class="form-check-label">
              Alerte active
            </label>
            <small class="form-text text-muted d-block">
              Décochez cette option pour désactiver temporairement cette alerte.
            </small>
          </div>

          <div class="d-flex justify-content-between">
            <router-link :to="{ name: 'user.alerts.index' }" class="btn btn-secondary">
              Annuler
            </router-link>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Mettre à jour
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserAlertEdit',
  data() {
    return {
      alert: {
        id: null,
        type: '',
        budget_id: '',
        categorie_budget_id: '',
        account_id: '',
        condition: '',
        percentage_threshold: 0,
        amount_threshold: '',
        message: '',
        frequency: 'once',
        notification_app: true,
        notification_email: false,
        active: true
      },
      budgets: [],
      accounts: [],
      categories: [],
      loading: true,
      errors: []
    };
  },
  methods: {
    fetchAlert() {
      this.loading = true;
      
      // Récupérer les données de l'alerte
      axios.get(`/api/user/alerts/${this.$route.params.id}`)
        .then(response => {
          const alertData = response.data.data;
          
          // Mapper les données reçues à notre modèle
          this.alert = {
            id: alertData.id,
            type: alertData.type,
            budget_id: alertData.budget_id || '',
            categorie_budget_id: alertData.categorie_budget_id || '',
            account_id: alertData.account_id || '',
            condition: alertData.condition,
            percentage_threshold: alertData.condition === 'percentage' ? alertData.threshold : 0,
            amount_threshold: alertData.condition !== 'percentage' ? alertData.threshold : '',
            message: alertData.message || '',
            frequency: alertData.frequency,
            notification_app: alertData.notification_app,
            notification_email: alertData.notification_email,
            active: alertData.active
          };
          
          // Charger les données associées
          return axios.get('/api/user/budgets/active');
        })
        .then(response => {
          this.budgets = response.data.data;
          return axios.get('/api/user/accounts/active');
        })
        .then(response => {
          this.accounts = response.data.data;
          
          // Si l'alerte est liée à un budget et une catégorie, charger les catégories
          if (this.alert.type === 'budget_category' && this.alert.budget_id) {
            return axios.get(`/api/user/budgets/${this.alert.budget_id}/categories`);
          }
          
          return Promise.resolve({ data: [] });
        })
        .then(response => {
          this.categories = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement de l\'alerte:', error);
          this.$toasted.error('Impossible de charger les détails de l\'alerte.');
          this.loading = false;
          this.$router.push({ name: 'user.alerts.index' });
        });
    },
    getAlertTypeName(type) {
      switch (type) {
        case 'budget_category':
          return 'Alerte sur catégorie de budget';
        case 'budget_total':
          return 'Alerte sur budget total';
        case 'account_balance':
          return 'Alerte sur solde de compte';
        default:
          return 'Inconnu';
      }
    },
    getBudgetName(budgetId) {
      const budget = this.budgets.find(b => b.id === budgetId);
      return budget ? budget.nom : 'Inconnu';
    },
    getCategoryName(categoryId) {
      const category = this.categories.find(c => c.id === categoryId);
      return category ? category.nom : 'Inconnu';
    },
    getAccountName(accountId) {
      const account = this.accounts.find(a => a.id === accountId);
      return account ? account.nom : 'Inconnu';
    },
    validateForm() {
      this.errors = [];

      if (!this.alert.condition) {
        this.errors.push('La condition est requise.');
        return false;
      }

      if (this.alert.condition === 'percentage') {
        if (!this.alert.percentage_threshold) {
          this.errors.push('Le seuil de pourcentage est requis.');
          return false;
        }

        if (this.alert.percentage_threshold < 0 || this.alert.percentage_threshold > 100) {
          this.errors.push('Le seuil de pourcentage doit être compris entre 0 et 100.');
          return false;
        }
      } else {
        if (!this.alert.amount_threshold) {
          this.errors.push('Le seuil de montant est requis.');
          return false;
        }
      }

      if (!this.alert.notification_app && !this.alert.notification_email) {
        this.errors.push('Veuillez sélectionner au moins un type de notification.');
        return false;
      }

      return true;
    },
    updateAlert() {
      if (!this.validateForm()) {
        return;
      }

      // Préparer les données à envoyer
      const alertData = {
        condition: this.alert.condition,
        threshold: this.alert.condition === 'percentage' ? this.alert.percentage_threshold : this.alert.amount_threshold,
        message: this.alert.message,
        frequency: this.alert.frequency,
        notification_app: this.alert.notification_app,
        notification_email: this.alert.notification_email,
        active: this.alert.active
      };

      axios.put(`/api/user/alerts/${this.alert.id}`, alertData)
        .then(response => {
          this.$toasted.success('Alerte mise à jour avec succès!');
          this.$router.push({ name: 'user.alerts.index' });
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.errors = Object.values(serverErrors).flat();
          } else {
            this.errors = ['Une erreur est survenue lors de la mise à jour de l\'alerte.'];
          }
        });
    }
  },
  created() {
    this.fetchAlert();
  },
  metaInfo() {
    return {
      title: 'Modifier l\'alerte - Budget Manager'
    };
  }
};
</script>
```

### Index.vue (Alertes)
```vue
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
```

## Pages de dépenses

### Create.vue (Dépenses)
```vue
<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Ajouter une dépense</h1>
      <router-link :to="{ name: 'user.expenses.index' }" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
      </router-link>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Détails de la dépense</h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="saveExpense">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="mb-3">
            <label for="budget_id" class="form-label">Budget</label>
            <select id="budget_id" v-model="expense.budget_id" class="form-select" required @change="fetchBudgetCategories">
              <option value="" disabled>Sélectionnez un budget</option>
              <option v-for="budget in budgets" :key="budget.id" :value="budget.id">
                {{ budget.nom }} ({{ formatDate(budget.date_debut) }} - {{ formatDate(budget.date_fin) }})
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label for="categorie_budget_id" class="form-label">Catégorie</label>
            <select id="categorie_budget_id" v-model="expense.categorie_budget_id" class="form-select" required :disabled="!expense.budget_id">
              <option value="" disabled>Sélectionnez une catégorie</option>
              <option v-for="category in budgetCategories" :key="category.id" :value="category.id">
                {{ category.nom }} - Restant: {{ formatAmount(category.montant_restant) }}
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input 
              type="text" 
              id="description" 
              v-model="expense.description"
              class="form-control" 
              required
              placeholder="Ex: Courses au supermarché"
            />
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="montant" class="form-label">Montant</label>
              <div class="input-group">
                <span class="input-group-text">MAD</span>
                <input 
                  type="number" 
                  id="montant" 
                  v-model="expense.montant"
                  class="form-control" 
                  step="0.01" 
                  min="0.01" 
                  required
                />
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="date_depense" class="form-label">Date de la dépense</label>
              <input 
                type="date" 
                id="date_depense" 
                v-model="expense.date_depense"
                class="form-control" 
                :max="today"
                required
              />
            </div>
          </div>

          <div class="mb-3">
            <label for="methode_paiement" class="form-label">Méthode de paiement</label>
            <select id="methode_paiement" v-model="expense.methode_paiement" class="form-select">
              <option value="carte">Carte bancaire</option>
              <option value="especes">Espèces</option>
              <option value="virement">Virement</option>
              <option value="cheque">Chèque</option>
              <option value="autre">Autre</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="compte_id" class="form-label">Compte</label>
            <select id="compte_id" v-model="expense.compte_id" class="form-select" required>
              <option value="" disabled>Sélectionnez un compte</option>
              <option v-for="account in accounts" :key="account.id" :value="account.id">
                {{ account.nom }} ({{ formatAmount(account.solde, account.devise) }})
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label for="statut" class="form-label">Statut</label>
            <select id="statut" v-model="expense.statut" class="form-select">
              <option value="validée">Validée</option>
              <option value="en attente">En attente</option>
              <option value="annulée">Annulée</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="note" class="form-label">Note (optionnelle)</label>
            <textarea 
              id="note" 
              v-model="expense.note"
              class="form-control" 
              rows="2"
              placeholder="Note ou commentaire sur cette dépense"
            ></textarea>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="recurrente" 
              v-model="expense.recurrente"
              class="form-check-input" 
            />
            <label for="recurrente" class="form-check-label">
              Dépense récurrente
            </label>
          </div>

          <div v-if="expense.recurrente" class="mb-3">
            <label for="frequence_recurrence" class="form-label">Fréquence de récurrence</label>
            <select id="frequence_recurrence" v-model="expense.frequence_recurrence" class="form-select">
              <option value="hebdomadaire">Hebdomadaire</option>
              <option value="mensuelle">Mensuelle</option>
              <option value="trimestrielle">Trimestrielle</option>
              <option value="annuelle">Annuelle</option>
            </select>
          </div>

          <div class="d-flex justify-content-between">
            <router-link :to="{ name: 'user.expenses.index' }" class="btn btn-secondary">
              Annuler
            </router-link>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Enregistrer
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserExpenseCreate',
  data() {
    return {
      expense: {
        budget_id: '',
        categorie_budget_id: '',
        description: '',
        montant: '',
        date_depense: this.formatDateForInput(new Date()),
        methode_paiement: 'carte',
        compte_id: '',
        statut: 'validée',
        note: '',
        recurrente: false,
        frequence_recurrence: 'mensuelle'
      },
      budgets: [],
      budgetCategories: [],
      accounts: [],
      errors: []
    };
  },
  computed: {
    today() {
      return this.formatDateForInput(new Date());
    }
  },
  methods: {
    formatDateForInput(date) {
      const d = new Date(date);
      const year = d.getFullYear();
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatAmount(amount, currency = 'MAD') {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: currency
      }).format(amount || 0);
    },
    fetchBudgets() {
      axios.get('/api/user/budgets/active')
        .then(response => {
          this.budgets = response.data.data;
          
          // Si un budget_id est passé en query parameter, le sélectionner
          const queryBudgetId = this.$route.query.budget_id;
          if (queryBudgetId && this.budgets.some(b => b.id === parseInt(queryBudgetId))) {
            this.expense.budget_id = parseInt(queryBudgetId);
            this.fetchBudgetCategories();
          }
        })
        .catch(error => {
          console.error('Erreur lors du chargement des budgets:', error);
          this.errors.push('Impossible de charger les budgets.');
        });
    },
    fetchAccounts() {
      axios.get('/api/user/accounts/active')
        .then(response => {
          this.accounts = response.data.data;
          
          // Si un compte_id est passé en query parameter, le sélectionner
          const queryAccountId = this.$route.query.account_id;
          if (queryAccountId && this.accounts.some(a => a.id === parseInt(queryAccountId))) {
            this.expense.compte_id = parseInt(queryAccountId);
          } else if (this.accounts.length === 1) {
            // S'il n'y a qu'un seul compte, le sélectionner automatiquement
            this.expense.compte_id = this.accounts[0].id;
          }
        })
        .catch(error => {
          console.error('Erreur lors du chargement des comptes:', error);
          this.errors.push('Impossible de charger les comptes.');
        });
    },
    fetchBudgetCategories() {
      if (!this.expense.budget_id) {
        this.budgetCategories = [];
        this.expense.categorie_budget_id = '';
        return;
      }
      
      axios.get(`/api/user/budgets/${this.expense.budget_id}/categories`)
        .then(response => {
          this.budgetCategories = response.data;
          
          // Réinitialiser la catégorie sélectionnée
          this.expense.categorie_budget_id = '';
        })
        .catch(error => {
          console.error('Erreur lors du chargement des catégories du budget:', error);
          this.errors.push('Impossible de charger les catégories du budget.');
        });
    },
    validateForm() {
      this.errors = [];

      if (!this.expense.budget_id) {
        this.errors.push('Veuillez sélectionner un budget.');
      }

      if (!this.expense.categorie_budget_id) {
        this.errors.push('Veuillez sélectionner une catégorie.');
      }

      if (!this.expense.description) {
        this.errors.push('La description est requise.');
      }

      if (!this.expense.montant) {
        this.errors.push('Le montant est requis.');
      } else if (parseFloat(this.expense.montant) <= 0) {
        this.errors.push('Le montant doit être supérieur à 0.');
      }

      if (!this.expense.date_depense) {
        this.errors.push('La date est requise.');
      }

      if (!this.expense.compte_id) {
        this.errors.push('Veuillez sélectionner un compte.');
      }

      return this.errors.length === 0;
    },
    saveExpense() {
      if (!this.validateForm()) {
        return;
      }

      axios.post('/api/user/expenses', this.expense)
        .then(response => {
          this.$toasted.success('Dépense enregistrée avec succès!');
          
          if (this.$route.query.redirect) {
            // Si une redirection est spécifiée dans l'URL
            this.$router.push(this.$route.query.redirect);
          } else {
            // Sinon, rediriger vers la liste des dépenses ou le détail de la dépense
            this.$router.push({ 
              name: 'user.expenses.show', 
              params: { id: response.data.data.id } 
            });
          }
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.errors = Object.values(serverErrors).flat();
          } else {
            this.errors = ['Une erreur est survenue lors de l\'enregistrement de la dépense.'];
          }
        });
    }
  },
  created() {
    this.fetchBudgets();
    this.fetchAccounts();
  },
  metaInfo() {
    return {
      title: 'Ajouter une dépense - Budget Manager'
    };
  }
};
</script>
```

### Edit.vue (Dépenses)
```vue
<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Modifier la dépense</h1>
      <div>
        <router-link :to="{ name: 'user.expenses.show', params: { id: $route.params.id } }" class="btn btn-info me-2">
          <i class="fas fa-eye me-1"></i> Voir les détails
        </router-link>
        <router-link :to="{ name: 'user.expenses.index' }" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <div v-else class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Détails de la dépense</h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="updateExpense">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Note:</strong> Pour des raisons de cohérence, le budget, la catégorie et le compte ne peuvent pas être modifiés. 
            Si vous souhaitez changer ces paramètres, veuillez supprimer cette dépense et en créer une nouvelle.
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Budget</label>
              <input type="text" class="form-control" :value="getBudgetName(expense.budget_id)" disabled />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Catégorie</label>
              <input type="text" class="form-control" :value="getCategoryName(expense.categorie_budget_id)" disabled />
            </div>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input 
              type="text" 
              id="description" 
              v-model="expense.description"
              class="form-control" 
              required
            />
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="montant" class="form-label">Montant</label>
              <div class="input-group">
                <span class="input-group-text">MAD</span>
                <input 
                  type="number" 
                  id="montant" 
                  v-model="expense.montant"
                  class="form-control" 
                  step="0.01" 
                  min="0.01" 
                  required
                />
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="date_depense" class="form-label">Date de la dépense</label>
              <input 
                type="date" 
                id="date_depense" 
                v-model="expense.date_depense"
                class="form-control" 
                :max="today"
                required
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="methode_paiement" class="form-label">Méthode de paiement</label>
              <select id="methode_paiement" v-model="expense.methode_paiement" class="form-select">
                <option value="carte">Carte bancaire</option>
                <option value="especes">Espèces</option>
                <option value="virement">Virement</option>
                <option value="cheque">Chèque</option>
                <option value="autre">Autre</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Compte</label>
              <input type="text" class="form-control" :value="getAccountName(expense.compte_id)" disabled />
            </div>
          </div>

          <div class="mb-3">
            <label for="statut" class="form-label">Statut</label>
            <select id="statut" v-model="expense.statut" class="form-select">
              <option value="validée">Validée</option>
              <option value="en attente">En attente</option>
              <option value="annulée">Annulée</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="note" class="form-label">Note (optionnelle)</label>
            <textarea 
              id="note" 
              v-model="expense.note"
              class="form-control" 
              rows="2"
              placeholder="Note ou commentaire sur cette dépense"
            ></textarea>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="recurrente" 
              v-model="expense.recurrente"
              class="form-check-input" 
            />
            <label for="recurrente" class="form-check-label">
              Dépense récurrente
            </label>
          </div>

          <div v-if="expense.recurrente" class="mb-3">
            <label for="frequence_recurrence" class="form-label">Fréquence de récurrence</label>
            <select id="frequence_recurrence" v-model="expense.frequence_recurrence" class="form-select">
              <option value="hebdomadaire">Hebdomadaire</option>
              <option value="mensuelle">Mensuelle</option>
              <option value="trimestrielle">Trimestrielle</option>
              <option value="annuelle">Annuelle</option>
            </select>
          </div>

          <div class="d-flex justify-content-between">
            <router-link :to="{ name: 'user.expenses.show', params: { id: expense.id } }" class="btn btn-secondary">
              Annuler
            </router-link>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Mettre à jour
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserExpenseEdit',
  data() {
    return {
      expense: {
        id: null,
        budget_id: '',
        categorie_budget_id: '',
        description: '',
        montant: '',
        date_depense: '',
        methode_paiement: 'carte',
        compte_id: '',
        statut: 'validée',
        note: '',
        recurrente: false,
        frequence_recurrence: 'mensuelle'
      },
      budgets: [],
      categories: [],
      accounts: [],
      loading: true,
      errors: []
    };
  },
  computed: {
    today() {
      return this.formatDateForInput(new Date());
    }
  },
  methods: {
    formatDateForInput(date) {
      const d = new Date(date);
      const year = d.getFullYear();
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatAmount(amount, currency = 'MAD') {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: currency
      }).format(amount || 0);
    },
    fetchExpense() {
      this.loading = true;
      
      axios.get(`/api/user/expenses/${this.$route.params.id}`)
        .then(response => {
          this.expense = response.data.data;
          
          // Charger les informations associées
          return axios.get('/api/user/budgets');
        })
        .then(response => {
          this.budgets = response.data.data;
          return axios.get(`/api/user/budgets/${this.expense.budget_id}/categories`);
        })
        .then(response => {
          this.categories = response.data;
          return axios.get('/api/user/accounts');
        })
        .then(response => {
          this.accounts = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement de la dépense:', error);
          this.$toasted.error('Impossible de charger les détails de la dépense.');
          this.loading = false;
          this.$router.push({ name: 'user.expenses.index' });
        });
    },
    getBudgetName(budgetId) {
      const budget = this.budgets.find(b => b.id === budgetId);
      return budget ? budget.nom : 'Inconnu';
    },
    getCategoryName(categoryId) {
      const category = this.categories.find(c => c.id === categoryId);
      return category ? category.nom : 'Inconnu';
    },
    getAccountName(accountId) {
      const account = this.accounts.find(a => a.id === accountId);
      return account ? account.nom : 'Inconnu';
    },
    validateForm() {
      this.errors = [];

      if (!this.expense.description) {
        this.errors.push('La description est requise.');
      }

      if (!this.expense.montant) {
        this.errors.push('Le montant est requis.');
      } else if (parseFloat(this.expense.montant) <= 0) {
        this.errors.push('Le montant doit être supérieur à 0.');
      }

      if (!this.expense.date_depense) {
        this.errors.push('La date est requise.');
      }

      return this.errors.length === 0;
    },
    updateExpense() {
      if (!this.validateForm()) {
        return;
      }

      // Préparer les données à envoyer
      const expenseData = {
        description: this.expense.description,
        montant: this.expense.montant,
        date_depense: this.expense.date_depense,
        methode_paiement: this.expense.methode_paiement,
        statut: this.expense.statut,
        note: this.expense.note,
        recurrente: this.expense.recurrente,
        frequence_recurrence: this.expense.frequence_recurrence
      };

      axios.put(`/api/user/expenses/${this.expense.id}`, expenseData)
        .then(response => {
          this.$toasted.success('Dépense mise à jour avec succès!');
          this.$router.push({ name: 'user.expenses.show', params: { id: this.expense.id } });
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.errors = Object.values(serverErrors).flat();
          } else {
            this.errors = ['Une erreur est survenue lors de la mise à jour de la dépense.'];
          }
        });
    }
  },
  created() {
    this.fetchExpense();
  },
  metaInfo() {
    return {
      title: 'Modifier la dépense - Budget Manager'
    };
  }
};
</script>
```

### Index.vue (Dépenses)
```vue
<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Mes dépenses</h1>
      <router-link :to="{ name: 'user.expenses.create' }" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Ajouter une dépense
      </router-link>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        <button class="btn btn-sm btn-outline-secondary" @click="resetFilters">
          <i class="fas fa-undo me-1"></i> Réinitialiser
        </button>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="budget_filter" class="form-label">Budget</label>
            <select id="budget_filter" v-model="filters.budget_id" class="form-select" @change="fetchData">
              <option value="">Tous les budgets</option>
              <option v-for="budget in budgets" :key="budget.id" :value="budget.id">
                {{ budget.nom }}
              </option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label for="category_filter" class="form-label">Catégorie</label>
            <select id="category_filter" v-model="filters.category_id" class="form-select" @change="fetchData">
              <option value="">Toutes les catégories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.nom }}
              </option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label for="account_filter" class="form-label">Compte</label>
            <select id="account_filter" v-model="filters.account_id" class="form-select" @change="fetchData">
              <option value="">Tous les comptes</option>
              <option v-for="account in accounts" :key="account.id" :value="account.id">
                {{ account.nom }}
              </option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label for="date_start" class="form-label">Date de début</label>
            <input type="date" id="date_start" v-model="filters.date_start" class="form-control" @change="fetchData" />
          </div>
          <div class="col-md-4 mb-3">
            <label for="date_end" class="form-label">Date de fin</label>
            <input type="date" id="date_end" v-model="filters.date_end" class="form-control" @change="fetchData" />
          </div>
          <div class="col-md-4 mb-3">
            <label for="status_filter" class="form-label">Statut</label>
            <select id="status_filter" v-model="filters.status" class="form-select" @change="fetchData">
              <option value="">Tous les statuts</option>
              <option value="validée">Validée</option>
              <option value="en attente">En attente</option>
              <option value="annulée">Annulée</option>
            </select>
          </div>
          <div class="col-md-8 mb-3">
            <label for="search" class="form-label">Recherche</label>
            <div class="input-group">
              <input 
                type="text" 
                id="search" 
                v-model="filters.search" 
                class="form-control" 
                placeholder="Rechercher une dépense..."
                @input="handleSearchInput"
              />
              <button 
                class="btn btn-outline-secondary" 
                type="button"
                @click="filters.search = ''; fetchData()"
                v-if="filters.search"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <label for="sort_by" class="form-label">Trier par</label>
            <select id="sort_by" v-model="filters.sort_by" class="form-select" @change="fetchData">
              <option value="date_desc">Date (récent → ancien)</option>
              <option value="date_asc">Date (ancien → récent)</option>
              <option value="amount_desc">Montant (décroissant)</option>
              <option value="amount_asc">Montant (croissant)</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Liste des dépenses -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Liste des dépenses</h6>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des dépenses...</p>
        </div>

        <div v-else-if="expenses.length === 0" class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Aucune dépense trouvée{{ hasFilters ? ' pour ces critères.' : '.' }} 
          <router-link :to="{ name: 'user.expenses.create' }" class="btn btn-sm btn-primary mt-2">
            <i class="fas fa-plus me-1"></i> Ajouter une dépense
          </router-link>
        </div>

        <div v-else>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Description</th>
                  <th>Catégorie</th>
                  <th>Montant</th>
                  <th>Compte</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="expense in expenses" :key="expense.id">
                  <td>{{ formatDate(expense.date_depense) }}</td>
                  <td>{{ expense.description }}</td>
                  <td>
                    <span 
                      class="badge rounded-pill" 
                      :style="{ 
                        backgroundColor: expense.categorie.couleur, 
                        color: getContrastColor(expense.categorie.couleur) 
                      }"
                    >
                      {{ expense.categorie.nom }}
                    </span>
                  </td>
                  <td>{{ formatAmount(expense.montant) }}</td>
                  <td>{{ expense.compte.nom }}</td>
                  <td>
                    <span 
                      class="badge" 
                      :class="getStatusBadgeClass(expense.statut)"
                    >
                      {{ expense.statut }}
                    </span>
                  </td>
                  <td>
                    <div class="btn-group">
                      <router-link 
                        :to="{ name: 'user.expenses.show', params: { id: expense.id } }" 
                        class="btn btn-sm btn-info"
                        title="Voir les détails"
                      >
                        <i class="fas fa-eye"></i>
                      </router-link>
                      <router-link 
                        :to="{ name: 'user.expenses.edit', params: { id: expense.id } }" 
                        class="btn btn-sm btn-primary"
                        title="Modifier"
                      >
                        <i class="fas fa-edit"></i>
                      </router-link>
                      <button 
                        class="btn btn-sm btn-danger" 
                        @click="confirmDelete(expense)"
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

          <!-- Pagination -->
          <nav aria-label="Pagination des dépenses">
            <ul class="pagination justify-content-center mt-4">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="goToPage(1)">
                  <i class="fas fa-angle-double-left"></i>
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">
                  <i class="fas fa-angle-left"></i>
                </a>
              </li>
              <li 
                v-for="page in pageNumbers" 
                :key="page" 
                class="page-item"
                :class="{ active: currentPage === page }"
              >
                <a class="page-link" href="#" @click.prevent="goToPage(page)">
                  {{ page }}
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">
                  <i class="fas fa-angle-right"></i>
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                <a class="page-link" href="#" @click.prevent="goToPage(lastPage)">
                  <i class="fas fa-angle-double-right"></i>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Résumé des dépenses -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Résumé des dépenses</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-6">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Répartition par catégorie</h6>
              </div>
              <div class="card-body">
                <div v-if="loading" class="text-center py-3">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                  </div>
                </div>
                <div v-else-if="expenses.length === 0" class="text-center py-3">
                  <p class="text-muted mb-0">Aucune donnée disponible</p>
                </div>
                <div v-else class="chart-container" style="height: 300px;">
                  <canvas ref="categoryChart"></canvas>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dépenses par mois</h6>
              </div>
              <div class="card-body">
                <div v-if="loading" class="text-center py-3">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                  </div>
                </div>
                <div v-else-if="expenses.length === 0" class="text-center py-3">
                  <p class="text-muted mb-0">Aucune donnée disponible</p>
                </div>
                <div v-else class="chart-container" style="height: 300px;">
                  <canvas ref="monthlyChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div 
      class="modal fade" 
      id="deleteExpenseModal" 
      tabindex="-1" 
      aria-labelledby="deleteExpenseModalLabel" 
      aria-hidden="true"
      ref="deleteModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteExpenseModalLabel">Confirmer la suppression</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body" v-if="expenseToDelete">
            <p>Êtes-vous sûr de vouloir supprimer cette dépense ?</p>
            <div class="alert alert-info">
              <strong>Description:</strong> {{ expenseToDelete.description }}<br>
              <strong>Montant:</strong> {{ formatAmount(expenseToDelete.montant) }}<br>
              <strong>Date:</strong> {{ formatDate(expenseToDelete.date_depense) }}
            </div>
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Attention:</strong> Cette action est irréversible.
            </div>
          </div>
          <div class="modal-footer">
            <button 
              type="button" 
              class="btn btn-secondary" 
              data-bs-dismiss="modal"
            >
              Annuler
            </button>
            <button 
              type="button" 
              class="btn btn-danger"
              @click="deleteExpense"
            >
              Supprimer définitivement
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';
import Chart from 'chart.js/auto';
import debounce from 'lodash/debounce';

export default {
  name: 'UserExpensesIndex',
  data() {
    return {
      expenses: [],
      budgets: [],
      categories: [],
      accounts: [],
      loading: true,
      deleteModal: null,
      expenseToDelete: null,
      filters: {
        budget_id: '',
        category_id: '',
        account_id: '',
        date_start: '',
        date_end: '',
        status: '',
        search: '',
        sort_by: 'date_desc',
        page: 1
      },
      currentPage: 1,
      lastPage: 1,
      totalExpenses: 0,
      categoryChart: null,
      monthlyChart: null
    };
  },
  computed: {
    hasFilters() {
      return this.filters.budget_id || 
             this.filters.category_id || 
             this.filters.account_id || 
             this.filters.date_start || 
             this.filters.date_end || 
             this.filters.status || 
             this.filters.search;
    },
    pageNumbers() {
      const displayedPages = 5;
      const pages = [];
      
      if (this.lastPage <= displayedPages) {
        // Afficher toutes les pages si leur nombre est inférieur au max
        for (let i = 1; i <= this.lastPage; i++) {
          pages.push(i);
        }
      } else {
        // Afficher un sous-ensemble de pages
        let startPage = Math.max(1, this.currentPage - Math.floor(displayedPages / 2));
        let endPage = Math.min(this.lastPage, startPage + displayedPages - 1);
        
        // Ajuster si on est proche de la fin
        if (endPage - startPage + 1 < displayedPages) {
          startPage = Math.max(1, endPage - displayedPages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
          pages.push(i);
        }
      }
      
      return pages;
    }
  },
  methods: {
    fetchData() {
      this.loading = true;
      
      // Préparer les paramètres de recherche
      const params = {
        budget_id: this.filters.budget_id,
        category_id: this.filters.category_id,
        account_id: this.filters.account_id,
        date_start: this.filters.date_start,
        date_end: this.filters.date_end,
        status: this.filters.status,
        search: this.filters.search,
        sort_by: this.filters.sort_by,
        page: this.currentPage
      };
      
      // Nettoyer les paramètres vides
      for (const key in params) {
        if (!params[key]) {
          delete params[key];
        }
      }
      
      axios.get('/api/user/expenses', { params })
        .then(response => {
          this.expenses = response.data.data;
          this.currentPage = response.data.meta.current_page;
          this.lastPage = response.data.meta.last_page;
          this.totalExpenses = response.data.meta.total;
          this.loading = false;
          
          // Mettre à jour les graphiques
          this.$nextTick(() => {
            this.renderCharts();
          });
        })
        .catch(error => {
          console.error('Erreur lors du chargement des dépenses:', error);
          this.$toasted.error('Impossible de charger les dépenses.');
          this.loading = false;
        });
    },
    fetchFilterOptions() {
      // Charger les budgets
      axios.get('/api/user/budgets')
        .then(response => {
          this.budgets = response.data.data;
          
          // Charger les catégories
          return axios.get('/api/categories');
        })
        .then(response => {
          this.categories = response.data.data;
          
          // Charger les comptes
          return axios.get('/api/user/accounts');
        })
        .then(response => {
          this.accounts = response.data.data;
          
          // Vérifier si des filtres sont passés en query parameters
          this.applyQueryParams();
          
          // Charger les dépenses
          this.fetchData();
        })
        .catch(error => {
          console.error('Erreur lors du chargement des options de filtrage:', error);
          this.$toasted.error('Impossible de charger les options de filtrage.');
          
          // Charger quand même les dépenses
          this.fetchData();
        });
    },
    applyQueryParams() {
      const query = this.$route.query;
      
      if (query.budget_id) {
        this.filters.budget_id = query.budget_id;
      }
      
      if (query.category_id) {
        this.filters.category_id = query.category_id;
      }
      
      if (query.account_id) {
        this.filters.account_id = query.account_id;
      }
      
      if (query.date_start) {
        this.filters.date_start = query.date_start;
      }
      
      if (query.date_end) {
        this.filters.date_end = query.date_end;
      }
      
      if (query.status) {
        this.filters.status = query.status;
      }
      
      if (query.search) {
        this.filters.search = query.search;
      }
      
      if (query.sort_by) {
        this.filters.sort_by = query.sort_by;
      }
      
      if (query.page) {
        this.currentPage = parseInt(query.page);
      }
    },
    updateQueryParams() {
      // Préparer les paramètres à ajouter dans l'URL
      const query = {};
      
      if (this.filters.budget_id) {
        query.budget_id = this.filters.budget_id;
      }
      
      if (this.filters.category_id) {
        query.category_id = this.filters.category_id;
      }
      
      if (this.filters.account_id) {
        query.account_id = this.filters.account_id;
      }
      
      if (this.filters.date_start) {
        query.date_start = this.filters.date_start;
      }
      
      if (this.filters.date_end) {
        query.date_end = this.filters.date_end;
      }
      
      if (this.filters.status) {
        query.status = this.filters.status;
      }
      
      if (this.filters.search) {
        query.search = this.filters.search;
      }
      
      if (this.filters.sort_by) {
        query.sort_by = this.filters.sort_by;
      }
      
      if (this.currentPage > 1) {
        query.page = this.currentPage;
      }
      
      // Mettre à jour l'URL sans recharger la page
      this.$router.replace({ query }).catch(() => {});
    },
    resetFilters() {
      this.filters = {
        budget_id: '',
        category_id: '',
        account_id: '',
        date_start: '',
        date_end: '',
        status: '',
        search: '',
        sort_by: 'date_desc'
      };
      this.currentPage = 1;
      this.fetchData();
    },
    goToPage(page) {
      if (page < 1 || page > this.lastPage) {
        return;
      }
      
      this.currentPage = page;
      this.fetchData();
    },
    handleSearchInput: debounce(function() {
      this.currentPage = 1;
      this.fetchData();
    }, 500),
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatAmount(amount, currency = 'MAD') {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: currency
      }).format(amount || 0);
    },
    getStatusBadgeClass(status) {
      switch (status) {
        case 'validée':
          return 'bg-success';
        case 'en attente':
          return 'bg-warning text-dark';
        case 'annulée':
          return 'bg-danger';
        default:
          return 'bg-secondary';
      }
    },
    getContrastColor(hexColor) {
      if (!hexColor) return '#000000';
      
      // Convertir la couleur hex en RGB
      const r = parseInt(hexColor.substr(1, 2), 16);
      const g = parseInt(hexColor.substr(3, 2), 16);
      const b = parseInt(hexColor.substr(5, 2), 16);
      
      // Calculer la luminosité (formule de perception)
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
      
      // Retourner du texte blanc ou noir en fonction de la luminosité
      return luminance > 0.5 ? '#000000' : '#FFFFFF';
    },
    confirmDelete(expense) {
      this.expenseToDelete = expense;
      this.deleteModal.show();
    },
    deleteExpense() {
      if (!this.expenseToDelete) {
        return;
      }
      
      axios.delete(`/api/user/expenses/${this.expenseToDelete.id}`)
        .then(response => {
          this.$toasted.success('Dépense supprimée avec succès!');
          this.deleteModal.hide();
          this.expenseToDelete = null;
          this.fetchData();
        })
        .catch(error => {
          console.error('Erreur lors de la suppression de la dépense:', error);
          this.$toasted.error('Impossible de supprimer la dépense.');
          this.deleteModal.hide();
        });
    },
    renderCharts() {
      this.renderCategoryChart();
      this.renderMonthlyChart();
    },
    renderCategoryChart() {
      if (!this.$refs.categoryChart || this.expenses.length === 0) return;
      
      const ctx = this.$refs.categoryChart.getContext('2d');
      
      // Préparer les données
      const categoryCounts = {};
      const categoryColors = {};
      
      this.expenses.forEach(expense => {
        const categoryName = expense.categorie.nom;
        const categoryColor = expense.categorie.couleur || '#4e73df';
        
        if (!categoryCounts[categoryName]) {
          categoryCounts[categoryName] = 0;
          categoryColors[categoryName] = categoryColor;
        }
        
        categoryCounts[categoryName] += parseFloat(expense.montant);
      });
      
      const labels = Object.keys(categoryCounts);
      const data = Object.values(categoryCounts);
      const colors = labels.map(label => categoryColors[label]);
      
      // Détruire le graphique existant s'il y en a un
      if (this.categoryChart) {
        this.categoryChart.destroy();
      }
      
      this.categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: colors,
            hoverOffset: 4,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
              labels: {
                boxWidth: 12,
                font: {
                  size: 10
                }
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.label || '';
                  if (label) {
                    label += ': ';
                  }
                  label += new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD'
                  }).format(context.raw);
                  return label;
                }
              }
            }
          }
        }
      });
    },
    renderMonthlyChart() {
      if (!this.$refs.monthlyChart || this.expenses.length === 0) return;
      
      const ctx = this.$refs.monthlyChart.getContext('2d');
      
      // Préparer les données
      const monthlyExpenses = {};
      
      this.expenses.forEach(expense => {
        const date = new Date(expense.date_depense);
        const monthYear = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        
        if (!monthlyExpenses[monthYear]) {
          monthlyExpenses[monthYear] = 0;
        }
        
        monthlyExpenses[monthYear] += parseFloat(expense.montant);
      });
      
      // Trier les mois par ordre chronologique
      const sortedMonths = Object.keys(monthlyExpenses).sort();
      
      // Formater les labels pour l'affichage
      const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
      const labels = sortedMonths.map(monthYear => {
        const [year, month] = monthYear.split('-');
        return `${monthNames[parseInt(month) - 1]} ${year}`;
      });
      
      const data = sortedMonths.map(monthYear => monthlyExpenses[monthYear]);
      
      // Détruire le graphique existant s'il y en a un
      if (this.monthlyChart) {
        this.monthlyChart.destroy();
      }
      
      this.monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Dépenses mensuelles',
            data: data,
            backgroundColor: '#4e73df',
            borderColor: '#2e59d9',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD',
                    maximumFractionDigits: 0
                  }).format(value);
                }
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.dataset.label || '';
                  if (label) {
                    label += ': ';
                  }
                  label += new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD'
                  }).format(context.raw);
                  return label;
                }
              }
            }
          }
        }
      });
    }
  },
  mounted() {
    this.fetchFilterOptions();
    this.deleteModal = new Modal(this.$refs.deleteModal);
  },
  beforeUnmount() {
    if (this.categoryChart) {
      this.categoryChart.destroy();
    }
    
    if (this.monthlyChart) {
      this.monthlyChart.destroy();
    }
  },
  watch: {
    currentPage() {
      this.updateQueryParams();
    }
  },
  metaInfo() {
    return {
      title: 'Mes dépenses - Budget Manager'
    };
  }
};
</script>

<style scoped>
/* Ajoutez vos styles personnalisés ici */
</style>
```

### Show.vue (Dépenses)
```vue
<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Détails de la dépense</h1>
      <div>
        <router-link :to="{ name: 'user.expenses.edit', params: { id: $route.params.id } }" class="btn btn-primary me-2">
          <i class="fas fa-edit me-1"></i> Modifier
        </router-link>
        <router-link :to="{ name: 'user.expenses.index' }" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <div v-else>
      <div class="row">
        <!-- Détails de la dépense -->
        <div class="col-lg-8">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h6 class="m-0 font-weight-bold text-primary">Informations sur la dépense</h6>
              <span 
                class="badge" 
                :class="getStatusBadgeClass(expense.statut)"
              >
                {{ expense.statut }}
              </span>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h5 class="mb-3">{{ expense.description }}</h5>
                  <div class="mb-4">
                    <span 
                      class="badge rounded-pill py-2 px-3" 
                      :style="{ 
                        backgroundColor: expense.categorie.couleur, 
                        color: getContrastColor(expense.categorie.couleur) 
                      }"
                    >
                      {{ expense.categorie.nom }}
                    </span>
                  </div>

                  <table class="table table-borderless">
                    <tbody>
                      <tr>
                        <th style="width: 40%">Montant</th>
                        <td class="h5 text-danger">{{ formatAmount(expense.montant) }}</td>
                      </tr>
                      <tr>
                        <th>Date</th>
                        <td>{{ formatDate(expense.date_depense) }}</td>
                      </tr>
                      <tr>
                        <th>Compte</th>
                        <td>{{ expense.compte.nom }}</td>
                      </tr>
                      <tr>
                        <th>Méthode de paiement</th>
                        <td>
                          <i :class="getPaymentMethodIcon(expense.methode_paiement)" class="me-2"></i>
                          {{ getPaymentMethodName(expense.methode_paiement) }}
                        </td>
                      </tr>
                      <tr v-if="expense.recurrente">
                        <th>Récurrence</th>
                        <td>
                          <i class="fas fa-sync-alt me-2"></i>
                          {{ getRecurrenceText(expense.frequence_recurrence) }}
                        </td>
                      </tr>
                      <tr v-if="expense.note">
                        <th>Note</th>
                        <td>{{ expense.note }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="col-md-6">
                  <div class="card mb-4">
                    <div class="card-header py-3">
                      <h6 class="m-0 font-weight-bold text-primary">Budget associé</h6>
                    </div>
                    <div class="card-body">
                      <h5>{{ expense.budget.nom }}</h5>
                      <p class="text-muted mb-3">
                        {{ formatDate(expense.budget.date_debut) }} - {{ formatDate(expense.budget.date_fin) }}
                      </p>
                      <div class="d-grid">
                        <router-link 
                          :to="{ name: 'user.budgets.show', params: { id: expense.budget.id } }" 
                          class="btn btn-outline-primary btn-sm"
                        >
                          <i class="fas fa-eye me-1"></i> Voir le budget
                        </router-link>
                      </div>
                    </div>
                  </div>

                  <div class="card">
                    <div class="card-header py-3">
                      <h6 class="m-0 font-weight-bold text-primary">Impact sur la catégorie</h6>
                    </div>
                    <div class="card-body">
                      <div class="d-flex justify-content-between mb-2">
                        <span>Budget alloué :</span>
                        <span>{{ formatAmount(expense.categorie_budget.montant_alloue) }}</span>
                      </div>
                      <div class="d-flex justify-content-between mb-2">
                        <span>Dépenses totales :</span>
                        <span>{{ formatAmount(expense.categorie_budget.montant_depense) }}</span>
                      </div>
                      <div class="d-flex justify-content-between mb-2">
                        <span>Solde restant :</span>
                        <span 
                          :class="expense.categorie_budget.montant_restant < 0 ? 'text-danger' : 'text-success'"
                        >
                          {{ formatAmount(expense.categorie_budget.montant_restant) }}
                        </span>
                      </div>
                      <div class="progress mb-1">
                        <div 
                          class="progress-bar" 
                          role="progressbar" 
                          :style="{ width: `${expense.categorie_budget.pourcentage_utilisation}%` }" 
                          :class="getBudgetProgressClass(expense.categorie_budget.pourcentage_utilisation)"
                          :aria-valuenow="expense.categorie_budget.pourcentage_utilisation" 
                          aria-valuemin="0" 
                          aria-valuemax="100"
                        >
                          {{ expense.categorie_budget.pourcentage_utilisation }}%
                        </div>
                      </div>
                      <small class="text-muted">
                        Cette dépense représente {{ getExpensePercentage() }}% du budget alloué à cette catégorie.
                      </small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Informations complémentaires -->
        <div class="col-lg-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Historique</h6>
            </div>
            <div class="card-body">
              <div class="timeline">
                <div class="timeline-item">
                  <div class="timeline-badge bg-primary">
                    <i class="fas fa-plus"></i>
                  </div>
                  <div class="timeline-content">
                    <h6 class="timeline-title">Création</h6>
                    <p class="timeline-text text-muted">
                      <small>{{ formatDateTime(expense.created_at) }}</small>
                    </p>
                  </div>
                </div>
                <div v-if="expense.updated_at !== expense.created_at" class="timeline-item">
                  <div class="timeline-badge bg-info">
                    <i class="fas fa-edit"></i>
                  </div>
                  <div class="timeline-content">
                    <h6 class="timeline-title">Dernière modification</h6>
                    <p class="timeline-text text-muted">
                      <small>{{ formatDateTime(expense.updated_at) }}</small>
                    </p>
                  </div>
                </div>
              </div>

              <div class="d-grid gap-2 mt-4">
                <button 
                  v-if="expense.statut !== 'annulée'" 
                  class="btn btn-danger" 
                  @click="cancelExpense"
                >
                  <i class="fas fa-ban me-1"></i> Annuler cette dépense
                </button>
                <button 
                  class="btn btn-outline-danger" 
                  @click="confirmDelete"
                >
                  <i class="fas fa-trash me-1"></i> Supprimer définitivement
                </button>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Dépenses similaires</h6>
            </div>
            <div class="card-body">
              <div v-if="loadingSimilar" class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Chargement...</span>
                </div>
              </div>
              <div v-else-if="similarExpenses.length === 0" class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Aucune dépense similaire trouvée.
              </div>
              <div v-else class="list-group">
                <router-link 
                  v-for="similar in similarExpenses" 
                  :key="similar.id"
                  :to="{ name: 'user.expenses.show', params: { id: similar.id } }"
                  class="list-group-item list-group-item-action"
                >
                  <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">{{ similar.description }}</h6>
                    <small>{{ formatDate(similar.date_depense) }}</small>
                  </div>
                  <div class="d-flex w-100 justify-content-between">
                    <p class="mb-1 text-muted">{{ similar.categorie.nom }}</p>
                    <strong>{{ formatAmount(similar.montant) }}</strong>
                  </div>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div 
      class="modal fade" 
      id="deleteExpenseModal" 
      tabindex="-1" 
      aria-labelledby="deleteExpenseModalLabel" 
      aria-hidden="true"
      ref="deleteModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteExpenseModalLabel">Confirmer la suppression</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body">
            <p>Êtes-vous sûr de vouloir supprimer définitivement cette dépense ?</p>
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Attention:</strong> Cette action est irréversible.
            </div>
          </div>
          <div class="modal-footer">
            <button 
              type="button" 
              class="btn btn-secondary" 
              data-bs-dismiss="modal"
            >
              Annuler
            </button>
            <button 
              type="button" 
              class="btn btn-danger"
              @click="deleteExpense"
            >
              Supprimer définitivement
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  name: 'UserExpenseShow',
  data() {
    return {
      expense: {
        id: null,
        description: '',
        montant: 0,
        date_depense: null,
        methode_paiement: '',
        statut: '',
        note: '',
        recurrente: false,
        frequence_recurrence: '',
        created_at: null,
        updated_at: null,
        budget: {
          id: null,
          nom: '',
          date_debut: null,
          date_fin: null
        },
        categorie: {
          id: null,
          nom: '',
          couleur: '#4e73df'
        },
        compte: {
          id: null,
          nom: ''
        },
        categorie_budget: {
          montant_alloue: 0,
          montant_depense: 0,
          montant_restant: 0,
          pourcentage_utilisation: 0
        }
      },
      similarExpenses: [],
      loading: true,
      loadingSimilar: true,
      deleteModal: null
    };
  },
  methods: {
    fetchExpense() {
      this.loading = true;
      
      axios.get(`/api/user/expenses/${this.$route.params.id}`)
        .then(response => {
          this.expense = response.data.data;
          this.loading = false;
          
          // Charger les dépenses similaires
          this.fetchSimilarExpenses();
        })
        .catch(error => {
          console.error('Erreur lors du chargement de la dépense:', error);
          this.$toasted.error('Impossible de charger les détails de la dépense.');
          this.loading = false;
          this.$router.push({ name: 'user.expenses.index' });
        });
    },
    fetchSimilarExpenses() {
      this.loadingSimilar = true;
      
      axios.get(`/api/user/expenses/${this.expense.id}/similar`)
        .then(response => {
          this.similarExpenses = response.data.data;
          this.loadingSimilar = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des dépenses similaires:', error);
          this.loadingSimilar = false;
        });
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatDateTime(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
      };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatAmount(amount, currency = 'MAD') {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: currency
      }).format(amount || 0);
    },
    getStatusBadgeClass(status) {
      switch (status) {
        case 'validée':
          return 'bg-success';
        case 'en attente':
          return 'bg-warning text-dark';
        case 'annulée':
          return 'bg-danger';
        default:
          return 'bg-secondary';
      }
    },
    getBudgetProgressClass(percentage) {
      if (percentage < 50) {
        return 'bg-success';
      } else if (percentage < 75) {
        return 'bg-info';
      } else if (percentage < 90) {
        return 'bg-warning';
      } else {
        return 'bg-danger';
      }
    },
    getContrastColor(hexColor) {
      if (!hexColor) return '#000000';
      
      // Convertir la couleur hex en RGB
      const r = parseInt(hexColor.substr(1, 2), 16);
      const g = parseInt(hexColor.substr(3, 2), 16);
      const b = parseInt(hexColor.substr(5, 2), 16);
      
      // Calculer la luminosité (formule de perception)
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
      
      // Retourner du texte blanc ou noir en fonction de la luminosité
      return luminance > 0.5 ? '#000000' : '#FFFFFF';
    },
    getPaymentMethodIcon(method) {
      switch (method) {
        case 'carte':
          return 'fas fa-credit-card';
        case 'especes':
          return 'fas fa-money-bill-wave';
        case 'virement':
          return 'fas fa-exchange-alt';
        case 'cheque':
          return 'fas fa-money-check';
        default:
          return 'fas fa-wallet';
      }
    },
    getPaymentMethodName(method) {
      switch (method) {
        case 'carte':
          return 'Carte bancaire';
        case 'especes':
          return 'Espèces';
        case 'virement':
          return 'Virement';
        case 'cheque':
          return 'Chèque';
        default:
          return 'Autre';
      }
    },
    getRecurrenceText(frequence) {
      switch (frequence) {
        case 'hebdomadaire':
          return 'Hebdomadaire';
        case 'mensuelle':
          return 'Mensuelle';
        case 'trimestrielle':
          return 'Trimestrielle';
        case 'annuelle':
          return 'Annuelle';
        default:
          return 'Inconnue';
      }
    },
    getExpensePercentage() {
      if (!this.expense.categorie_budget.montant_alloue) return 0;
      
      const percentage = (this.expense.montant / this.expense.categorie_budget.montant_alloue) * 100;
      return percentage.toFixed(1);
    },
    confirmDelete() {
      this.deleteModal.show();
    },
    deleteExpense() {
      axios.delete(`/api/user/expenses/${this.expense.id}`)
        .then(response => {
          this.$toasted.success('Dépense supprimée avec succès!');
          this.deleteModal.hide();
          this.$router.push({ name: 'user.expenses.index' });
        })
        .catch(error => {
          console.error('Erreur lors de la suppression de la dépense:', error);
          this.$toasted.error('Impossible de supprimer la dépense.');
          this.deleteModal.hide();
        });
    },
    cancelExpense() {
      if (confirm('Êtes-vous sûr de vouloir annuler cette dépense ?')) {
        axios.put(`/api/user/expenses/${this.expense.id}/cancel`)
          .then(response => {
            this.expense.statut = 'annulée';
            this.$toasted.success('Dépense annulée avec succès!');
          })
          .catch(error => {
            console.error('Erreur lors de l\'annulation de la dépense:', error);
            this.$toasted.error('Impossible d\'annuler la dépense.');
          });
      }
    }
  },
  mounted() {
    this.fetchExpense();
    this.deleteModal = new Modal(this.$refs.deleteModal);
  },
  metaInfo() {
    return {
      title: 'Détails de la dépense - Budget Manager'
    };
  }
};
</script>

<style scoped>
.timeline {
  position: relative;
  padding-left: 40px;
  margin-bottom: 20px;
  list-style-type: none;
}

.timeline:before {
  content: '';
  position: absolute;
  left: 15px;
  top: 0;
  height: 100%;
  width: 2px;
  background: #e9ecef;
}

.timeline-item {
  position: relative;
  margin-bottom: 30px;
}

.timeline-badge {
  position: absolute;
  left: -40px;
  width: 30px;
  height: 30px;
  text-align: center;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.timeline-content {
  padding: 0 15px;
}

.timeline-title {
  margin-bottom: 5px;
}

.timeline-text {
  margin-bottom: 0;
}
</style>