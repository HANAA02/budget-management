<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Détails de l'utilisateur</h1>
      <div>
        <router-link :to="{ name: 'admin.users.edit', params: { id: $route.params.id } }" class="btn btn-primary me-2">
          <i class="fas fa-edit me-1"></i> Modifier
        </router-link>
        <router-link :to="{ name: 'admin.users.index' }" class="btn btn-secondary">
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
        <!-- Informations utilisateur -->
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fas fa-user me-2"></i> Informations personnelles
              </h5>
            </div>
            <div class="card-body">
              <div class="user-avatar text-center mb-4">
                <div class="avatar-placeholder">
                  <span>{{ user.prenom.charAt(0) }}{{ user.nom.charAt(0) }}</span>
                </div>
                <h4 class="mt-2">{{ user.prenom }} {{ user.nom }}</h4>
                <span 
                  class="badge" 
                  :class="user.role === 'admin' ? 'bg-danger' : 'bg-primary'"
                >
                  {{ user.role === 'admin' ? 'Administrateur' : 'Utilisateur' }}
                </span>
                <span 
                  class="badge ms-2" 
                  :class="user.active ? 'bg-success' : 'bg-danger'"
                >
                  {{ user.active ? 'Actif' : 'Inactif' }}
                </span>
              </div>

              <table class="table table-borderless">
                <tbody>
                  <tr>
                    <th style="width: 35%">ID</th>
                    <td>{{ user.id }}</td>
                  </tr>
                  <tr>
                    <th>Email</th>
                    <td>{{ user.email }}</td>
                  </tr>
                  <tr>
                    <th>Date d'inscription</th>
                    <td>{{ formatDate(user.date_creation) }}</td>
                  </tr>
                  <tr>
                    <th>Dernière connexion</th>
                    <td>{{ user.dernier_login ? formatDateTime(user.dernier_login) : 'Jamais' }}</td>
                  </tr>
                </tbody>
              </table>

              <div class="mt-3">
                <button 
                  class="btn btn-sm me-2" 
                  :class="user.active ? 'btn-warning' : 'btn-success'"
                  @click="toggleUserStatus"
                >
                  <i :class="user.active ? 'fas fa-ban me-1' : 'fas fa-check me-1'"></i>
                  {{ user.active ? 'Désactiver le compte' : 'Activer le compte' }}
                </button>
                <button 
                  class="btn btn-sm btn-secondary me-2" 
                  @click="showResetPasswordModal"
                >
                  <i class="fas fa-key me-1"></i> Réinitialiser le mot de passe
                </button>
                <button 
                  class="btn btn-sm btn-info" 
                  @click="impersonateUser"
                >
                  <i class="fas fa-user-secret me-1"></i> Se connecter en tant que
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Statistiques utilisateur -->
        <div class="col-md-6 mb-4">
          <div class="card">
            <div class="card-header">
              <h5 class="mb-0">
                <i class="fas fa-chart-pie me-2"></i> Statistiques
              </h5>
            </div>
            <div class="card-body">
              <div class="row mb-4">
                <div class="col-6">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h6 class="card-title">Budgets créés</h6>
                      <p class="display-6">{{ userStats.budgets_count }}</p>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h6 class="card-title">Dépenses enregistrées</h6>
                      <p class="display-6">{{ userStats.expenses_count }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h6 class="card-title">Comptes bancaires</h6>
                      <p class="display-6">{{ userStats.accounts_count }}</p>
                    </div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h6 class="card-title">Objectifs</h6>
                      <p class="display-6">{{ userStats.goals_count }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="card bg-light mt-4">
                <div class="card-body text-center">
                  <h6 class="card-title">Montant total géré</h6>
                  <p class="display-5">{{ formatAmount(userStats.total_amount) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Activité récente -->
      <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="fas fa-history me-2"></i> Activité récente
          </h5>
          <div>
            <select v-model="activityType" class="form-select form-select-sm" style="width: auto; display: inline-block;">
              <option value="">Toutes les activités</option>
              <option value="login">Connexions</option>
              <option value="budget">Budgets</option>
              <option value="expense">Dépenses</option>
              <option value="goal">Objectifs</option>
            </select>
          </div>
        </div>
        <div class="card-body">
          <div v-if="loadingActivity" class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Chargement...</span>
            </div>
          </div>
          <div v-else-if="userActivity.length === 0" class="alert alert-info">
            Aucune activité récente n'a été enregistrée pour cet utilisateur.
          </div>
          <div v-else>
            <div class="timeline">
              <div v-for="(activity, index) in filteredActivity" :key="index" class="timeline-item">
                <div class="timeline-badge" :class="getActivityBadgeClass(activity.type)">
                  <i :class="getActivityIcon(activity.type)"></i>
                </div>
                <div class="timeline-content">
                  <h6 class="timeline-title">{{ activity.description }}</h6>
                  <p class="timeline-text">
                    <small class="text-muted">{{ formatDateTime(activity.created_at) }}</small>
                  </p>
                </div>
              </div>
            </div>
            <div class="text-center mt-3" v-if="hasMoreActivity">
              <button class="btn btn-outline-primary" @click="loadMoreActivity">
                Charger plus d'activités
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Budgets de l'utilisateur -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">
            <i class="fas fa-wallet me-2"></i> Budgets
          </h5>
        </div>
        <div class="card-body">
          <div v-if="loadingBudgets" class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Chargement...</span>
            </div>
          </div>
          <div v-else-if="userBudgets.length === 0" class="alert alert-info">
            Cet utilisateur n'a pas encore créé de budget.
          </div>
          <div v-else class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Période</th>
                  <th>Montant total</th>
                  <th>Date de création</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="budget in userBudgets" :key="budget.id">
                  <td>{{ budget.nom }}</td>
                  <td>{{ formatDate(budget.date_debut) }} - {{ formatDate(budget.date_fin) }}</td>
                  <td>{{ formatAmount(budget.montant_total) }}</td>
                  <td>{{ formatDate(budget.date_creation) }}</td>
                  <td>
                    <a :href="`/budgets/${budget.id}`" target="_blank" class="btn btn-sm btn-info">
                      <i class="fas fa-eye"></i>
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de réinitialisation de mot de passe -->
    <div 
      class="modal fade" 
      id="resetPasswordModal" 
      tabindex="-1" 
      aria-labelledby="resetPasswordModalLabel" 
      aria-hidden="true"
      ref="resetPasswordModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="resetPasswordModalLabel">Réinitialiser le mot de passe</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body">
            <p>
              Vous êtes sur le point de réinitialiser le mot de passe de <strong>{{ user.prenom }} {{ user.nom }}</strong>.
            </p>
            
            <div class="mb-3">
              <label for="new_password" class="form-label">Nouveau mot de passe</label>
              <div class="input-group">
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  id="new_password" 
                  v-model="resetPasswordData.password"
                  class="form-control" 
                  minlength="8"
                  required
                />
                <button 
                  class="btn btn-outline-secondary" 
                  type="button"
                  @click="showPassword = !showPassword"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
              <small class="form-text text-muted">
                Le mot de passe doit contenir au moins 8 caractères.
              </small>
            </div>
            
            <div class="mb-3 form-check">
              <input 
                type="checkbox" 
                id="send_reset_notification" 
                v-model="resetPasswordData.send_notification"
                class="form-check-input" 
              />
              <label for="send_reset_notification" class="form-check-label">
                Envoyer un email de notification à l'utilisateur
              </label>
            </div>
            
            <div class="alert alert-warning">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Attention :</strong> Cette action est irréversible. L'ancien mot de passe ne pourra pas être récupéré.
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
              class="btn btn-primary"
              @click="resetPassword"
              :disabled="!resetPasswordData.password || resetPasswordData.password.length < 8"
            >
              <i class="fas fa-key me-1"></i> Réinitialiser le mot de passe
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de changement de statut -->
    <div 
      class="modal fade" 
      id="statusChangeModal" 
      tabindex="-1" 
      aria-labelledby="statusChangeModalLabel" 
      aria-hidden="true"
      ref="statusModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="statusChangeModalLabel">Confirmer le changement de statut</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body">
            <p v-if="user.active">
              Êtes-vous sûr de vouloir <strong>désactiver</strong> le compte de <strong>{{ user.prenom }} {{ user.nom }}</strong> ?
            </p>
            <p v-else>
              Êtes-vous sûr de vouloir <strong>activer</strong> le compte de <strong>{{ user.prenom }} {{ user.nom }}</strong> ?
            </p>
            <div class="alert" :class="user.active ? 'alert-warning' : 'alert-info'">
              <i :class="user.active ? 'fas fa-exclamation-triangle me-2' : 'fas fa-info-circle me-2'"></i>
              <span v-if="user.active">
                <strong>Attention :</strong> L'utilisateur ne pourra plus se connecter à l'application tant que son compte sera désactivé.
              </span>
              <span v-else>
                <strong>Information :</strong> L'utilisateur pourra à nouveau se connecter à l'application.
              </span>
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
              :class="user.active ? 'btn btn-warning' : 'btn btn-success'"
              @click="updateUserStatus"
            >
              <span v-if="user.active">
                <i class="fas fa-ban me-1"></i> Désactiver
              </span>
              <span v-else>
                <i class="fas fa-check me-1"></i> Activer
              </span>
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
  name: 'AdminUserShow',
  data() {
    return {
      user: {
        id: null,
        prenom: '',
        nom: '',
        email: '',
        role: '',
        active: true,
        date_creation: null,
        dernier_login: null
      },
      userStats: {
        budgets_count: 0,
        expenses_count: 0,
        accounts_count: 0,
        goals_count: 0,
        total_amount: 0
      },
      userActivity: [],
      userBudgets: [],
      loading: true,
      loadingActivity: true,
      loadingBudgets: true,
      activityPage: 1,
      hasMoreActivity: false,
      activityType: '',
      resetPasswordData: {
        password: '',
        send_notification: true
      },
      showPassword: false,
      statusModal: null,
      resetPasswordModal: null
    };
  },
  computed: {
    filteredActivity() {
      if (!this.activityType) {
        return this.userActivity;
      }
      
      return this.userActivity.filter(activity => activity.type === this.activityType);
    }
  },
  methods: {
    fetchUser() {
      this.loading = true;
      axios.get(`/api/admin/users/${this.$route.params.id}`)
        .then(response => {
          this.user = response.data.data;
          this.fetchUserStats();
        })
        .catch(error => {
          console.error('Erreur lors du chargement de l\'utilisateur:', error);
          this.$toasted.error('Impossible de charger les détails de l\'utilisateur.');
          this.loading = false;
          this.$router.push({ name: 'admin.users.index' });
        });
    },
    fetchUserStats() {
      axios.get(`/api/admin/users/${this.user.id}/stats`)
        .then(response => {
          this.userStats = response.data;
          this.fetchUserActivity();
        })
        .catch(error => {
          console.error('Erreur lors du chargement des statistiques:', error);
          this.fetchUserActivity();
        });
    },
    fetchUserActivity() {
      this.loadingActivity = true;
      axios.get(`/api/admin/users/${this.user.id}/activity`, {
        params: { page: this.activityPage }
      })
        .then(response => {
          this.userActivity = this.activityPage === 1 
            ? response.data.data 
            : [...this.userActivity, ...response.data.data];
          
          this.hasMoreActivity = response.data.meta.current_page < response.data.meta.last_page;
          this.loadingActivity = false;
          this.fetchUserBudgets();
        })
        .catch(error => {
          console.error('Erreur lors du chargement de l\'activité:', error);
          this.loadingActivity = false;
          this.fetchUserBudgets();
        });
    },
    fetchUserBudgets() {
      this.loadingBudgets = true;
      axios.get(`/api/admin/users/${this.user.id}/budgets`)
        .then(response => {
          this.userBudgets = response.data.data;
          this.loadingBudgets = false;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des budgets:', error);
          this.loadingBudgets = false;
          this.loading = false;
        });
    },
    loadMoreActivity() {
      this.activityPage++;
      this.fetchUserActivity();
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
    formatAmount(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount || 0);
    },
    getActivityBadgeClass(type) {
      switch (type) {
        case 'login':
          return 'bg-primary';
        case 'budget':
          return 'bg-success';
        case 'expense':
          return 'bg-info';
        case 'goal':
          return 'bg-warning';
        default:
          return 'bg-secondary';
      }
    },
    getActivityIcon(type) {
      switch (type) {
        case 'login':
          return 'fas fa-sign-in-alt';
        case 'budget':
          return 'fas fa-wallet';
        case 'expense':
          return 'fas fa-receipt';
        case 'goal':
          return 'fas fa-bullseye';
        default:
          return 'fas fa-info-circle';
      }
    },
    toggleUserStatus() {
      this.statusModal.show();
    },
    updateUserStatus() {
      const newStatus = !this.user.active;
      
      axios.patch(`/api/admin/users/${this.user.id}/status`, { active: newStatus })
        .then(() => {
          this.user.active = newStatus;
          
          const action = newStatus ? 'activé' : 'désactivé';
          this.$toasted.success(`Compte ${action} avec succès!`);
          
          this.statusModal.hide();
        })
        .catch(error => {
          console.error('Erreur lors de la mise à jour du statut:', error);
          
          let errorMessage = 'Une erreur est survenue lors de la mise à jour du statut.';
          
          if (error.response && error.response.data && error.response.data.message) {
            errorMessage = error.response.data.message;
          }
          
          this.$toasted.error(errorMessage);
          this.statusModal.hide();
        });
    },
    showResetPasswordModal() {
      this.resetPasswordData = {
        password: '',
        send_notification: true
      };
      this.resetPasswordModal.show();
    },
    resetPassword() {
      if (!this.resetPasswordData.password || this.resetPasswordData.password.length < 8) {
        return;
      }
      
      axios.post(`/api/admin/users/${this.user.id}/reset-password`, this.resetPasswordData)
        .then(() => {
          this.$toasted.success('Mot de passe réinitialisé avec succès!');
          this.resetPasswordModal.hide();
        })
        .catch(error => {
          console.error('Erreur lors de la réinitialisation du mot de passe:', error);
          
          let errorMessage = 'Une erreur est survenue lors de la réinitialisation du mot de passe.';
          
          if (error.response && error.response.data && error.response.data.message) {
            errorMessage = error.response.data.message;
          }
          
          this.$toasted.error(errorMessage);
        });
    },
    impersonateUser() {
      axios.post(`/api/admin/users/${this.user.id}/impersonate`)
        .then(response => {
          window.location.href = response.data.redirect_url;
        })
        .catch(error => {
          console.error('Erreur lors de l\'usurpation d\'identité:', error);
          
          let errorMessage = 'Une erreur est survenue.';
          
          if (error.response && error.response.data && error.response.data.message) {
            errorMessage = error.response.data.message;
          }
          
          this.$toasted.error(errorMessage);
        });
    }
  },
  created() {
    this.fetchUser();
  },
  mounted() {
    this.statusModal = new Modal(this.$refs.statusModal);
    this.resetPasswordModal = new Modal(this.$refs.resetPasswordModal);
  },
  watch: {
    activityType() {
      // Reset la pagination quand on change le type d'activité
      this.activityPage = 1;
      this.userActivity = [];
      this.fetchUserActivity();
    }
  },
  metaInfo() {
    return {
      title: `Détails de l'utilisateur - Administration`
    };
  }
};
</script>

<style scoped>
.avatar-placeholder {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background-color: #4e73df;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  font-weight: bold;
  margin: 0 auto;
}

/* Timeline styling */
.timeline {
  position: relative;
  padding-left: 40px;
  margin-bottom: 50px;
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