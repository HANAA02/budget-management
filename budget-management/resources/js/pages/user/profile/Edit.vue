<template>
  <div>
    <h1 class="mb-4">Mon profil</h1>

    <div class="row">
      <div class="col-lg-8">
        <!-- Informations personnelles -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations personnelles</h6>
          </div>
          <div class="card-body">
            <form @submit.prevent="updateProfile">
              <div v-if="profileErrors.length" class="alert alert-danger">
                <ul class="mb-0">
                  <li v-for="(error, index) in profileErrors" :key="index">{{ error }}</li>
                </ul>
              </div>

              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="prenom" class="form-label">Prénom</label>
                  <input 
                    type="text" 
                    id="prenom" 
                    v-model="profile.prenom"
                    class="form-control" 
                    required
                  />
                </div>
                <div class="col-md-6">
                  <label for="nom" class="form-label">Nom</label>
                  <input 
                    type="text" 
                    id="nom" 
                    v-model="profile.nom"
                    class="form-control" 
                    required
                  />
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                  type="email" 
                  id="email" 
                  v-model="profile.email"
                  class="form-control" 
                  required
                />
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-1"></i> Enregistrer les modifications
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Changement de mot de passe -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Modifier le mot de passe</h6>
          </div>
          <div class="card-body">
            <form @submit.prevent="updatePassword">
              <div v-if="passwordErrors.length" class="alert alert-danger">
                <ul class="mb-0">
                  <li v-for="(error, index) in passwordErrors" :key="index">{{ error }}</li>
                </ul>
              </div>

              <div v-if="passwordSuccess" class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i> Votre mot de passe a été modifié avec succès.
              </div>

              <div class="mb-3">
                <label for="current_password" class="form-label">Mot de passe actuel</label>
                <div class="input-group">
                  <input 
                    :type="showCurrentPassword ? 'text' : 'password'" 
                    id="current_password" 
                    v-model="password.current_password"
                    class="form-control" 
                    required
                  />
                  <button 
                    class="btn btn-outline-secondary" 
                    type="button"
                    @click="showCurrentPassword = !showCurrentPassword"
                  >
                    <i :class="showCurrentPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                </div>
              </div>

              <div class="mb-3">
                <label for="new_password" class="form-label">Nouveau mot de passe</label>
                <div class="input-group">
                  <input 
                    :type="showNewPassword ? 'text' : 'password'" 
                    id="new_password" 
                    v-model="password.new_password"
                    class="form-control" 
                    required
                    minlength="8"
                  />
                  <button 
                    class="btn btn-outline-secondary" 
                    type="button"
                    @click="showNewPassword = !showNewPassword"
                  >
                    <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                </div>
                <small class="form-text text-muted">
                  Le mot de passe doit contenir au moins 8 caractères.
                </small>
              </div>

              <div class="mb-3">
                <label for="new_password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                <div class="input-group">
                  <input 
                    :type="showNewPassword ? 'text' : 'password'" 
                    id="new_password_confirmation" 
                    v-model="password.new_password_confirmation"
                    class="form-control" 
                    required
                  />
                </div>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-key me-1"></i> Modifier le mot de passe
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <!-- Préférences -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Préférences</h6>
          </div>
          <div class="card-body">
            <form @submit.prevent="updatePreferences">
              <div v-if="preferencesErrors.length" class="alert alert-danger">
                <ul class="mb-0">
                  <li v-for="(error, index) in preferencesErrors" :key="index">{{ error }}</li>
                </ul>
              </div>

              <div class="mb-3">
                <label for="devise" class="form-label">Devise</label>
                <select id="devise" v-model="preferences.devise" class="form-select">
                  <option value="MAD">Dirham Marocain (MAD)</option>
                  <option value="USD">Dollar Américain (USD)</option>
                  <option value="EUR">Euro (EUR)</option>
                  <option value="GBP">Livre Sterling (GBP)</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="date_format" class="form-label">Format de date</label>
                <select id="date_format" v-model="preferences.date_format" class="form-select">
                  <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                  <option value="MM/DD/YYYY">MM/DD/YYYY</option>
                  <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                </select>
              </div>

              <div class="mb-3 form-check">
                <input 
                  type="checkbox" 
                  id="email_notifications" 
                  v-model="preferences.email_notifications"
                  class="form-check-input" 
                />
                <label for="email_notifications" class="form-check-label">
                  Recevoir des notifications par email
                </label>
              </div>

              <div class="mb-3 form-check">
                <input 
                  type="checkbox" 
                  id="budget_alerts" 
                  v-model="preferences.budget_alerts"
                  class="form-check-input" 
                />
                <label for="budget_alerts" class="form-check-label">
                  Alertes de dépassement de budget
                </label>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-cog me-1"></i> Enregistrer les préférences
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Informations du compte -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informations du compte</h6>
          </div>
          <div class="card-body">
            <div class="user-info">
              <div class="mb-3 text-center">
                <div class="avatar-placeholder mx-auto">
                  <span>{{ profile.prenom ? profile.prenom.charAt(0) : '' }}{{ profile.nom ? profile.nom.charAt(0) : '' }}</span>
                </div>
              </div>

              <div class="mb-3">
                <h6 class="text-muted mb-1">Inscrit depuis</h6>
                <p>{{ formatDate(profile.date_creation) }}</p>
              </div>

              <div class="mb-3">
                <h6 class="text-muted mb-1">Dernière connexion</h6>
                <p>{{ profile.dernier_login ? formatDateTime(profile.dernier_login) : 'N/A' }}</p>
              </div>

              <div class="border-top pt-3 mt-3">
                <button 
                  type="button" 
                  class="btn btn-outline-danger btn-sm"
                  @click="showDeleteAccountModal"
                >
                  <i class="fas fa-trash me-1"></i> Supprimer mon compte
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de suppression de compte -->
    <div 
      class="modal fade" 
      id="deleteAccountModal" 
      tabindex="-1" 
      aria-labelledby="deleteAccountModalLabel" 
      aria-hidden="true"
      ref="deleteAccountModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteAccountModalLabel">Supprimer mon compte</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Attention :</strong> Cette action est irréversible. Toutes vos données seront définitivement supprimées.
            </div>
            <p>Pour confirmer la suppression de votre compte, veuillez saisir votre mot de passe :</p>
            <div class="mb-3">
              <label for="delete_password" class="form-label">Mot de passe</label>
              <input 
                type="password" 
                id="delete_password" 
                v-model="deleteAccount.password"
                class="form-control" 
                required
              />
            </div>
            <div v-if="deleteAccountErrors.length" class="alert alert-danger">
              <ul class="mb-0">
                <li v-for="(error, index) in deleteAccountErrors" :key="index">{{ error }}</li>
              </ul>
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
              @click="confirmDeleteAccount"
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
  name: 'UserProfileEdit',
  data() {
    return {
      profile: {
        prenom: '',
        nom: '',
        email: '',
        date_creation: null,
        dernier_login: null
      },
      password: {
        current_password: '',
        new_password: '',
        new_password_confirmation: ''
      },
      preferences: {
        devise: 'MAD',
        date_format: 'DD/MM/YYYY',
        email_notifications: true,
        budget_alerts: true
      },
      deleteAccount: {
        password: ''
      },
      showCurrentPassword: false,
      showNewPassword: false,
      profileErrors: [],
      passwordErrors: [],
      preferencesErrors: [],
      deleteAccountErrors: [],
      passwordSuccess: false,
      deleteAccountModal: null
    };
  },
  methods: {
    fetchProfile() {
      axios.get('/api/user/profile')
        .then(response => {
          this.profile = response.data.profile;
          this.preferences = response.data.preferences;
        })
        .catch(error => {
          console.error('Erreur lors du chargement du profil:', error);
          this.$toasted.error('Impossible de charger les informations du profil.');
        });
    },
    updateProfile() {
      this.profileErrors = [];
      
      if (!this.validateEmail(this.profile.email)) {
        this.profileErrors.push('Veuillez entrer une adresse email valide.');
        return;
      }
      
      axios.put('/api/user/profile', this.profile)
        .then(response => {
          this.$toasted.success('Profil mis à jour avec succès!');
        })
        .catch(error => {
          console.error('Erreur lors de la mise à jour du profil:', error);
          
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.profileErrors = Object.values(serverErrors).flat();
          } else {
            this.profileErrors = ['Une erreur est survenue lors de la mise à jour du profil.'];
          }
        });
    },
    updatePassword() {
      this.passwordErrors = [];
      this.passwordSuccess = false;
      
      if (!this.password.current_password) {
        this.passwordErrors.push('Veuillez entrer votre mot de passe actuel.');
        return;
      }
      
      if (!this.password.new_password) {
        this.passwordErrors.push('Veuillez entrer un nouveau mot de passe.');
        return;
      }
      
      if (this.password.new_password.length < 8) {
        this.passwordErrors.push('Le nouveau mot de passe doit contenir au moins 8 caractères.');
        return;
      }
      
      if (this.password.new_password !== this.password.new_password_confirmation) {
        this.passwordErrors.push('Les mots de passe ne correspondent pas.');
        return;
      }
      
      axios.put('/api/user/password', this.password)
        .then(response => {
          this.passwordSuccess = true;
          this.password = {
            current_password: '',
            new_password: '',
            new_password_confirmation: ''
          };
          this.$toasted.success('Mot de passe modifié avec succès!');
        })
        .catch(error => {
          console.error('Erreur lors de la mise à jour du mot de passe:', error);
          
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.passwordErrors = Object.values(serverErrors).flat();
          } else if (error.response && error.response.status === 422) {
            this.passwordErrors = ['Le mot de passe actuel est incorrect.'];
          } else {
            this.passwordErrors = ['Une erreur est survenue lors de la mise à jour du mot de passe.'];
          }
        });
    },
    updatePreferences() {
      this.preferencesErrors = [];
      
      axios.put('/api/user/preferences', this.preferences)
        .then(response => {
          this.$toasted.success('Préférences mises à jour avec succès!');
        })
        .catch(error => {
          console.error('Erreur lors de la mise à jour des préférences:', error);
          
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.preferencesErrors = Object.values(serverErrors).flat();
          } else {
            this.preferencesErrors = ['Une erreur est survenue lors de la mise à jour des préférences.'];
          }
        });
    },
    showDeleteAccountModal() {
      this.deleteAccount.password = '';
      this.deleteAccountErrors = [];
      this.deleteAccountModal.show();
    },
    confirmDeleteAccount() {
      this.deleteAccountErrors = [];
      
      if (!this.deleteAccount.password) {
        this.deleteAccountErrors.push('Veuillez entrer votre mot de passe pour confirmer la suppression.');
        return;
      }
      
      axios.post('/api/user/delete-account', this.deleteAccount)
        .then(response => {
          this.deleteAccountModal.hide();
          this.$toasted.success('Votre compte a été supprimé avec succès.');
          
          // Déconnexion et redirection
          setTimeout(() => {
            window.location.href = '/login';
          }, 2000);
        })
        .catch(error => {
          console.error('Erreur lors de la suppression du compte:', error);
          
          if (error.response && error.response.status === 422) {
            this.deleteAccountErrors = ['Le mot de passe est incorrect.'];
          } else if (error.response && error.response.data && error.response.data.message) {
            this.deleteAccountErrors = [error.response.data.message];
          } else {
            this.deleteAccountErrors = ['Une erreur est survenue lors de la suppression du compte.'];
          }
        });
    },
    validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
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
    }
  },
  mounted() {
    this.fetchProfile();
    this.deleteAccountModal = new Modal(this.$refs.deleteAccountModal);
  },
  metaInfo() {
    return {
      title: 'Mon profil - Budget Manager'
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
}
</style>