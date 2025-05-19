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