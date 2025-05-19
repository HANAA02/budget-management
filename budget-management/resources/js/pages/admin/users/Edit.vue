<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Modifier l'utilisateur</h1>
      <div>
        <router-link :to="{ name: 'admin.users.show', params: { id: $route.params.id } }" class="btn btn-info me-2">
          <i class="fas fa-eye me-1"></i> Voir les détails
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

    <div v-else class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
          <i class="fas fa-user me-2"></i>
          {{ user.prenom }} {{ user.nom }}
        </h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="updateUser">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="prenom" class="form-label">Prénom</label>
              <input 
                type="text" 
                id="prenom" 
                v-model="user.prenom"
                class="form-control" 
                required
              />
            </div>
            <div class="col-md-6 mb-3">
              <label for="nom" class="form-label">Nom</label>
              <input 
                type="text" 
                id="nom" 
                v-model="user.nom"
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
              v-model="user.email"
              class="form-control" 
              required
            />
          </div>

          <div class="mb-3">
            <label for="role" class="form-label">Rôle</label>
            <select id="role" v-model="user.role" class="form-select" required>
              <option value="user">Utilisateur</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="active" 
              v-model="user.active"
              class="form-check-input" 
            />
            <label for="active" class="form-check-label">Compte actif</label>
            <small class="form-text text-muted d-block">
              Désactiver ce compte empêchera l'utilisateur de se connecter à l'application.
            </small>
          </div>

          <h5 class="mt-4 mb-3">Modifier le mot de passe</h5>
          <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            Laissez ces champs vides si vous ne souhaitez pas modifier le mot de passe.
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="mot_de_passe" class="form-label">Nouveau mot de passe</label>
              <div class="input-group">
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  id="mot_de_passe" 
                  v-model="passwordData.mot_de_passe"
                  class="form-control" 
                  minlength="8"
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
            <div class="col-md-6 mb-3">
              <label for="confirmation_mot_de_passe" class="form-label">Confirmer le nouveau mot de passe</label>
              <div class="input-group">
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  id="confirmation_mot_de_passe" 
                  v-model="passwordData.confirmation_mot_de_passe"
                  class="form-control" 
                />
              </div>
            </div>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="send_password_notification" 
              v-model="passwordData.send_notification"
              class="form-check-input" 
              :disabled="!passwordData.mot_de_passe"
            />
            <label for="send_password_notification" class="form-check-label">
              Notifier l'utilisateur du changement de mot de passe
            </label>
          </div>

          <hr class="my-4">

          <div class="d-flex justify-content-between">
            <button 
              type="button" 
              class="btn btn-secondary"
              @click="$router.push({ name: 'admin.users.show', params: { id: user.id } })"
            >
              Annuler
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Enregistrer les modifications
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminUserEdit',
  data() {
    return {
      user: {
        id: null,
        prenom: '',
        nom: '',
        email: '',
        role: 'user',
        active: true
      },
      passwordData: {
        mot_de_passe: '',
        confirmation_mot_de_passe: '',
        send_notification: true
      },
      showPassword: false,
      loading: true,
      errors: []
    };
  },
  methods: {
    fetchUser() {
      this.loading = true;
      axios.get(`/api/admin/users/${this.$route.params.id}`)
        .then(response => {
          this.user = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement de l\'utilisateur:', error);
          this.$toasted.error('Impossible de charger les détails de l\'utilisateur.');
          this.loading = false;
          this.$router.push({ name: 'admin.users.index' });
        });
    },
    validateForm() {
      this.errors = [];

      if (!this.user.prenom.trim()) {
        this.errors.push('Le prénom est requis.');
      }

      if (!this.user.nom.trim()) {
        this.errors.push('Le nom est requis.');
      }

      if (!this.user.email.trim()) {
        this.errors.push('L\'email est requis.');
      } else if (!this.validateEmail(this.user.email)) {
        this.errors.push('Veuillez entrer une adresse email valide.');
      }

      // Validation du mot de passe seulement si l'utilisateur a entré quelque chose
      if (this.passwordData.mot_de_passe) {
        if (this.passwordData.mot_de_passe.length < 8) {
          this.errors.push('Le mot de passe doit contenir au moins 8 caractères.');
        }

        if (this.passwordData.mot_de_passe !== this.passwordData.confirmation_mot_de_passe) {
          this.errors.push('Les mots de passe ne correspondent pas.');
        }
      }

      return this.errors.length === 0;
    },
    validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    },
    updateUser() {
      if (!this.validateForm()) {
        return;
      }

      // Préparer les données à envoyer
      const userData = {
        prenom: this.user.prenom,
        nom: this.user.nom,
        email: this.user.email,
        role: this.user.role,
        active: this.user.active
      };

      // Ajouter les données de mot de passe si elles sont fournies
      if (this.passwordData.mot_de_passe) {
        userData.mot_de_passe = this.passwordData.mot_de_passe;
        userData.confirmation_mot_de_passe = this.passwordData.confirmation_mot_de_passe;
        userData.send_password_notification = this.passwordData.send_notification;
      }

      axios.put(`/api/admin/users/${this.user.id}`, userData)
        .then(response => {
          this.$toasted.success('Utilisateur mis à jour avec succès!');
          this.$router.push({ name: 'admin.users.show', params: { id: this.user.id } });
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            Object.keys(serverErrors).forEach(key => {
              this.errors.push(serverErrors[key][0]);
            });
          } else {
            this.errors.push('Une erreur est survenue lors de la mise à jour de l\'utilisateur.');
          }
        });
    }
  },
  created() {
    this.fetchUser();
  },
  metaInfo() {
    return {
      title: `Modifier l'utilisateur - Administration`
    };
  }
};
</script>