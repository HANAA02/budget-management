<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
              <div class="col-lg-7">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Créer un compte</h1>
                  </div>
                  
                  <form class="user" @submit.prevent="register">
                    <div v-if="errors.length" class="alert alert-danger">
                      <ul class="mb-0">
                        <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                      </ul>
                    </div>
                    
                    <div class="form-group row mb-3">
                      <div class="col-sm-6 mb-3 mb-sm-0">
                        <input 
                          type="text" 
                          class="form-control form-control-user" 
                          id="prenom" 
                          v-model="form.prenom"
                          placeholder="Prénom"
                          required
                        />
                      </div>
                      <div class="col-sm-6">
                        <input 
                          type="text" 
                          class="form-control form-control-user" 
                          id="nom" 
                          v-model="form.nom"
                          placeholder="Nom"
                          required
                        />
                      </div>
                    </div>
                    
                    <div class="form-group mb-3">
                      <input 
                        type="email" 
                        class="form-control form-control-user" 
                        id="email" 
                        v-model="form.email"
                        placeholder="Adresse email"
                        required
                      />
                    </div>
                    
                    <div class="form-group row mb-3">
                      <div class="col-sm-6 mb-3 mb-sm-0">
                        <input 
                          type="password" 
                          class="form-control form-control-user" 
                          id="password" 
                          v-model="form.password"
                          placeholder="Mot de passe"
                          minlength="8"
                          required
                        />
                      </div>
                      <div class="col-sm-6">
                        <input 
                          type="password" 
                          class="form-control form-control-user" 
                          id="password_confirmation" 
                          v-model="form.password_confirmation"
                          placeholder="Confirmer le mot de passe"
                          required
                        />
                      </div>
                    </div>
                    
                    <button 
                      type="submit" 
                      class="btn btn-primary btn-user btn-block"
                      :disabled="loading"
                    >
                      <span v-if="loading">
                        <i class="fas fa-spinner fa-spin me-2"></i> Inscription en cours...
                      </span>
                      <span v-else>S'inscrire</span>
                    </button>
                  </form>
                  
                  <hr>
                  
                  <div class="text-center">
                    <router-link class="small" :to="{ name: 'auth.reset-password' }">
                      Mot de passe oublié ?
                    </router-link>
                  </div>
                  
                  <div class="text-center">
                    <router-link class="small" :to="{ name: 'auth.login' }">
                      Déjà un compte ? Connectez-vous !
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Register',
  data() {
    return {
      form: {
        prenom: '',
        nom: '',
        email: '',
        password: '',
        password_confirmation: ''
      },
      errors: [],
      loading: false
    };
  },
  methods: {
    register() {
      this.errors = [];
      
      // Validation basique côté client
      if (this.form.password !== this.form.password_confirmation) {
        this.errors.push('Les mots de passe ne correspondent pas.');
        return;
      }
      
      if (this.form.password.length < 8) {
        this.errors.push('Le mot de passe doit contenir au moins 8 caractères.');
        return;
      }
      
      this.loading = true;
      
      axios.post('/api/auth/register', this.form)
        .then(response => {
          this.$toasted.success('Compte créé avec succès! Vous pouvez maintenant vous connecter.');
          this.$router.push({ name: 'auth.login' });
        })
        .catch(error => {
          this.loading = false;
          
          if (error.response && error.response.data) {
            if (error.response.data.errors) {
              this.errors = Object.values(error.response.data.errors).flat();
            } else if (error.response.data.message) {
              this.errors = [error.response.data.message];
            } else {
              this.errors = ['Une erreur est survenue lors de l\'inscription.'];
            }
          } else {
            this.errors = ['Impossible de se connecter au serveur.'];
          }
        });
    }
  },
  metaInfo() {
    return {
      title: 'Inscription - Budget Manager'
    };
  }
};
</script>

<style scoped>
.bg-register-image {
  background: url('/img/register-bg.jpg');
  background-position: center;
  background-size: cover;
}

.form-control-user {
  border-radius: 10rem;
  padding: 1.5rem 1rem;
}

.btn-user {
  border-radius: 10rem;
  padding: 0.75rem 1rem;
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.1rem;
}
</style>