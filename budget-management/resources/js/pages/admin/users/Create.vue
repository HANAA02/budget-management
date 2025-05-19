<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Créer un nouvel utilisateur</h1>
      <router-link :to="{ name: 'admin.users.index' }" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
      </router-link>
    </div>

    <div class="card">
      <div class="card-body">
        <form @submit.prevent="saveUser">
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

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="mot_de_passe" class="form-label">Mot de passe</label>
              <div class="input-group">
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  id="mot_de_passe" 
                  v-model="user.mot_de_passe"
                  class="form-control" 
                  required
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
              <label for="confirmation_mot_de_passe" class="form-label">Confirmer le mot de passe</label>
              <div class="input-group">
                <input 
                  :type="showPassword ? 'text' : 'password'" 
                  id="confirmation_mot_de_passe" 
                  v-model="user.confirmation_mot_de_passe"
                  class="form-control" 
                  required
                />
              </div>
            </div>
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
              id="send_welcome_email" 
              v-model="sendWelcomeEmail"
              class="form-check-input" 
            />
            <label for="send_welcome_email" class="form-check-label">
              Envoyer un email de bienvenue
            </label>
            <small class="form-text text-muted d-block">
              Si activé, un email de bienvenue sera envoyé à l'utilisateur avec ses informations de connexion.
            </small>
          </div>

          <hr class="my-4">

          <div class="d-flex justify-content-between">
            <button 
              type="button" 
              class="btn btn-secondary"
              @click="$router.push({ name: 'admin.users.index' })"
            >
              Annuler
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Créer l'utilisateur
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminUserCreate',
  data() {
    return {
      user: {
        prenom: '',
        nom: '',
        email: '',
        mot_de_passe: '',
        confirmation_mot_de_passe: '',
        role: 'user'
      },
      showPassword: false,
      sendWelcomeEmail: true,
      errors: []
    };
  },
  methods: {
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

      if (!this.user.mot_de_passe) {
        this.errors.push('Le mot de passe est requis.');
      } else if (this.user.mot_de_passe.length < 8) {
        this.errors.push('Le mot de passe doit contenir au moins 8 caractères.');
      }

      if (this.user.mot_de_passe !== this.user.confirmation_mot_de_passe) {
        this.errors.push('Les mots de passe ne correspondent pas.');
      }

      return this.errors.length === 0;
    },
    validateEmail(email) {
      const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return re.test(email);
    },
    saveUser() {
      if (!this.validateForm()) {
        return;
      }

      // Préparer les données à envoyer
      const userData = {
        prenom: this.user.prenom,
        nom: this.user.nom,
        email: this.user.email,
        mot_de_passe: this.user.mot_de_passe,
        confirmation_mot_de_passe: this.user.confirmation_mot_de_passe,
        role: this.user.role,
        send_welcome_email: this.sendWelcomeEmail
      };

      axios.post('/api/admin/users', userData)
        .then(response => {
          this.$toasted.success('Utilisateur créé avec succès!');
          this.$router.push({ name: 'admin.users.show', params: { id: response.data.data.id } });
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            Object.keys(serverErrors).forEach(key => {
              this.errors.push(serverErrors[key][0]);
            });
          } else {
            this.errors.push('Une erreur est survenue lors de la création de l\'utilisateur.');
          }
        });
    }
  },
  metaInfo() {
    return {
      title: 'Créer un utilisateur - Administration'
    };
  }
};
</script>