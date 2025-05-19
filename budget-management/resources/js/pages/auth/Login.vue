<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-4">Bienvenue sur Budget Manager</h1>
                  </div>
                  
                  <form class="user" @submit.prevent="login">
                    <div v-if="errors.length" class="alert alert-danger">
                      <ul class="mb-0">
                        <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
                      </ul>
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
                    
                    <div class="form-group mb-3">
                      <input 
                        type="password" 
                        class="form-control form-control-user" 
                        id="password" 
                        v-model="form.password"
                        placeholder="Mot de passe"
                        required
                      />
                    </div>
                    
                    <div class="form-group mb-3">
                      <div class="custom-control custom-checkbox small">
                        <input 
                          type="checkbox" 
                          class="custom-control-input" 
                          id="remember" 
                          v-model="form.remember"
                        />
                        <label class="custom-control-label" for="remember">
                          Se souvenir de moi
                        </label>
                      </div>
                    </div>
                    
                    <button 
                      type="submit" 
                      class="btn btn-primary btn-user btn-block"
                      :disabled="loading"
                    >
                      <span v-if="loading">
                        <i class="fas fa-spinner fa-spin me-2"></i> Connexion en cours...
                      </span>
                      <span v-else>Connexion</span>
                    </button>
                  </form>
                  
                  <hr>
                  
                  <div class="text-center">
                    <router-link class="small" :to="{ name: 'auth.reset-password' }">
                      Mot de passe oublié ?
                    </router-link>
                  </div>
                  
                  <div class="text-center">
                    <router-link class="small" :to="{ name: 'auth.register' }">
                      Créer un compte !
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
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: '',
        remember: false
      },
      errors: [],
      loading: false
    };
  },
  methods: {
    login() {
      this.errors = [];
      this.loading = true;
      
      axios.post('/api/auth/login', this.form)
        .then(response => {
          // Stocker le token d'authentification si disponible
          if (response.data.token) {
            localStorage.setItem('auth_token', response.data.token);
            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
          }
          
          // Rediriger vers le tableau de bord
          window.location.href = '/dashboard';
        })
        .catch(error => {
          this.loading = false;
          
          if (error.response && error.response.data) {
            if (error.response.data.errors) {
              this.errors = Object.values(error.response.data.errors).flat();
            } else if (error.response.data.message) {
              this.errors = [error.response.data.message];
            } else {
              this.errors = ['Une erreur est survenue lors de la connexion.'];
            }
          } else {
            this.errors = ['Impossible de se connecter au serveur.'];
          }
        });
    }
  },
  metaInfo() {
    return {
      title: 'Connexion - Budget Manager'
    };
  }
};
</script>

<style scoped>
.bg-login-image {
  background: url('/img/login-bg.jpg');
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