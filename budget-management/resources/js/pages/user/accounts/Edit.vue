<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Modifier le compte</h1>
      <div>
        <router-link :to="{ name: 'user.accounts.show', params: { id: $route.params.id } }" class="btn btn-info me-2">
          <i class="fas fa-eye me-1"></i> Voir les détails
        </router-link>
        <router-link :to="{ name: 'user.accounts.index' }" class="btn btn-secondary">
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
        <h6 class="m-0 font-weight-bold text-primary">Détails du compte</h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="updateAccount">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nom" class="form-label">Nom du compte</label>
              <input 
                type="text" 
                id="nom" 
                v-model="account.nom"
                class="form-control" 
                required
              />
            </div>
            <div class="col-md-6 mb-3">
              <label for="type" class="form-label">Type de compte</label>
              <select id="type" v-model="account.type" class="form-select" required>
                <option value="" disabled>Sélectionnez un type</option>
                <option value="courant">Compte courant</option>
                <option value="epargne">Compte d'épargne</option>
                <option value="credit">Carte de crédit</option>
                <option value="liquide">Espèces</option>
                <option value="autre">Autre</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="solde_actuel" class="form-label">Solde actuel</label>
            <div class="input-group">
              <span class="input-group-text">{{ account.devise }}</span>
              <input 
                type="text" 
                id="solde_actuel" 
                v-model="account.solde"
                class="form-control" 
                disabled
              />
            </div>
            <small class="form-text text-muted">
              Le solde est mis à jour automatiquement en fonction des revenus et dépenses. 
              Pour ajuster manuellement le solde, utilisez une opération d'ajustement.
            </small>
          </div>

          <div class="mb-3">
            <label for="devise" class="form-label">Devise</label>
            <select id="devise" v-model="account.devise" class="form-select" disabled>
              <option value="MAD">Dirham Marocain (MAD)</option>
              <option value="USD">Dollar Américain (USD)</option>
              <option value="EUR">Euro (EUR)</option>
              <option value="GBP">Livre Sterling (GBP)</option>
            </select>
            <small class="form-text text-muted">
              La devise ne peut pas être modifiée une fois le compte créé.
            </small>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description (optionnelle)</label>
            <textarea 
              id="description" 
              v-model="account.description"
              class="form-control" 
              rows="3"
            ></textarea>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="inclure_dans_total" 
              v-model="account.inclure_dans_total"
              class="form-check-input" 
            />
            <label for="inclure_dans_total" class="form-check-label">
              Inclure ce compte dans le calcul du solde total
            </label>
            <small class="form-text text-muted d-block">
              Décochez cette option si vous ne souhaitez pas que ce compte soit pris en compte dans le calcul de votre solde total.
            </small>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="est_actif" 
              v-model="account.est_actif"
              class="form-check-input" 
            />
            <label for="est_actif" class="form-check-label">
              Compte actif
            </label>
            <small class="form-text text-muted d-block">
              Décochez cette option si vous souhaitez désactiver temporairement ce compte.
            </small>
          </div>

          <div class="d-flex justify-content-between">
            <router-link :to="{ name: 'user.accounts.show', params: { id: account.id } }" class="btn btn-secondary">
              Annuler
            </router-link>
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
  name: 'UserAccountEdit',
  data() {
    return {
      account: {
        id: null,
        nom: '',
        type: '',
        solde: 0,
        devise: 'MAD',
        description: '',
        inclure_dans_total: true,
        est_actif: true
      },
      loading: true,
      errors: []
    };
  },
  methods: {
    fetchAccount() {
      this.loading = true;
      axios.get(`/api/user/accounts/${this.$route.params.id}`)
        .then(response => {
          this.account = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement du compte:', error);
          this.$toasted.error('Impossible de charger les détails du compte.');
          this.loading = false;
          this.$router.push({ name: 'user.accounts.index' });
        });
    },
    validateForm() {
      this.errors = [];

      if (!this.account.nom) {
        this.errors.push('Le nom du compte est requis.');
      }

      if (!this.account.type) {
        this.errors.push('Le type de compte est requis.');
      }

      return this.errors.length === 0;
    },
    updateAccount() {
      if (!this.validateForm()) {
        return;
      }

      const accountData = {
        nom: this.account.nom,
        type: this.account.type,
        description: this.account.description,
        inclure_dans_total: this.account.inclure_dans_total,
        est_actif: this.account.est_actif
      };

      axios.put(`/api/user/accounts/${this.account.id}`, accountData)
        .then(response => {
          this.$toasted.success('Compte mis à jour avec succès!');
          this.$router.push({ name: 'user.accounts.show', params: { id: this.account.id } });
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.errors = Object.values(serverErrors).flat();
          } else {
            this.errors = ['Une erreur est survenue lors de la mise à jour du compte.'];
          }
        });
    }
  },
  created() {
    this.fetchAccount();
  },
  metaInfo() {
    return {
      title: `Modifier le compte - Budget Manager`
    };
  }
};
</script>