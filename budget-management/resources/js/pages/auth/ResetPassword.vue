<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-10 col-lg-12 col-md-9">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-6 d-none d-lg-block bg-password-image"></div>
              <div class="col-lg-6">
                <div class="p-5">
                  <div class="text-center">
                    <h1 class="h4 text-gray-900 mb-2">Mot de passe oublié ?</h1>
                    <p class="mb-4">Entrez votre adresse email ci-dessous et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
                  </div>
                  
                  <form class="user" @submit.prevent="resetPassword" v-if="!emailSent">
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
                    
                    <button 
                      type="submit" 
                      class="btn btn-primary btn-user btn-block"
                      :disabled="loading"
                    >
                      <span v-if="loading">
                        <i class="fas fa-spinner fa-spin me-2"></i> Envoi en cours...
                      </span>
                      <span v-else>Réinitialiser le mot de passe</span>
                    </button>
                  </form>
                  
                  <div v-if="emailSent" class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Un email contenant les instructions pour réinitialiser votre mot de passe a été envoyé à l'adresse indiquée.
                  </div>
                  
                  <hr>
                  
                  <div class="text-center">
                    <router-link class="small" :to="{ name: 'auth.register' }">
                      Créer un compte !
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
  name: 'ResetPassword',
  data() {
    return {
      form: {
        email: '',
        token: this.$route.params.token || '',
        password: '',
        password_confirmation: ''
      },
      errors: [],
      loading: false,
      emailSent: false
    };
  },
  methods: {
    resetPassword() {
      this.errors = [];
      this.loading = true;
      
      axios.post('/api/auth/forgot-password', { email: this.form.email })
        .then(response => {
          this.loading = false;
          this.emailSent = true;
        })
        .catch(error => {
          this.loading = false;
          
          if (error.response && error.response.data) {
            if (error.response.data.errors) {
              this.errors = Object.values(error.response.data.errors).flat();
            } else if (error.response.data.message) {
              this.errors = [error.response.data.message];
            } else {
              this.errors = ['Une erreur est survenue lors de la demande de réinitialisation.'];
            }
          } else {
            this.errors = ['Impossible de se connecter au serveur.'];
          }
        });
    }
  },
  metaInfo() {
    return {
      title: 'Réinitialisation du mot de passe - Budget Manager'
    };
  }
};
</script>

<style scoped>
.bg-password-image {
  background: url('/img/password-bg.jpg');
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