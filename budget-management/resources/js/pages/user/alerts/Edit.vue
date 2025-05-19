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