<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Ajouter une dépense</h1>
      <router-link :to="{ name: 'user.expenses.index' }" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
      </router-link>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Détails de la dépense</h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="saveExpense">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="mb-3">
            <label for="budget_id" class="form-label">Budget</label>
            <select id="budget_id" v-model="expense.budget_id" class="form-select" required @change="fetchBudgetCategories">
              <option value="" disabled>Sélectionnez un budget</option>
              <option v-for="budget in budgets" :key="budget.id" :value="budget.id">
                {{ budget.nom }} ({{ formatDate(budget.date_debut) }} - {{ formatDate(budget.date_fin) }})
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label for="categorie_budget_id" class="form-label">Catégorie</label>
            <select id="categorie_budget_id" v-model="expense.categorie_budget_id" class="form-select" required :disabled="!expense.budget_id">
              <option value="" disabled>Sélectionnez une catégorie</option>
              <option v-for="category in budgetCategories" :key="category.id" :value="category.id">
                {{ category.nom }} - Restant: {{ formatAmount(category.montant_restant) }}
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <input 
              type="text" 
              id="description" 
              v-model="expense.description"
              class="form-control" 
              required
              placeholder="Ex: Courses au supermarché"
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

          <div class="mb-3">
            <label for="methode_paiement" class="form-label">Méthode de paiement</label>
            <select id="methode_paiement" v-model="expense.methode_paiement" class="form-select">
              <option value="carte">Carte bancaire</option>
              <option value="especes">Espèces</option>
              <option value="virement">Virement</option>
              <option value="cheque">Chèque</option>
              <option value="autre">Autre</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="compte_id" class="form-label">Compte</label>
            <select id="compte_id" v-model="expense.compte_id" class="form-select" required>
              <option value="" disabled>Sélectionnez un compte</option>
              <option v-for="account in accounts" :key="account.id" :value="account.id">
                {{ account.nom }} ({{ formatAmount(account.solde, account.devise) }})
              </option>
            </select>
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
            <router-link :to="{ name: 'user.expenses.index' }" class="btn btn-secondary">
              Annuler
            </router-link>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Enregistrer
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserExpenseCreate',
  data() {
    return {
      expense: {
        budget_id: '',
        categorie_budget_id: '',
        description: '',
        montant: '',
        date_depense: this.formatDateForInput(new Date()),
        methode_paiement: 'carte',
        compte_id: '',
        statut: 'validée',
        note: '',
        recurrente: false,
        frequence_recurrence: 'mensuelle'
      },
      budgets: [],
      budgetCategories: [],
      accounts: [],
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
    fetchBudgets() {
      axios.get('/api/user/budgets/active')
        .then(response => {
          this.budgets = response.data.data;
          
          // Si un budget_id est passé en query parameter, le sélectionner
          const queryBudgetId = this.$route.query.budget_id;
          if (queryBudgetId && this.budgets.some(b => b.id === parseInt(queryBudgetId))) {
            this.expense.budget_id = parseInt(queryBudgetId);
            this.fetchBudgetCategories();
          }
        })
        .catch(error => {
          console.error('Erreur lors du chargement des budgets:', error);
          this.errors.push('Impossible de charger les budgets.');
        });
    },
    fetchAccounts() {
      axios.get('/api/user/accounts/active')
        .then(response => {
          this.accounts = response.data.data;
          
          // Si un compte_id est passé en query parameter, le sélectionner
          const queryAccountId = this.$route.query.account_id;
          if (queryAccountId && this.accounts.some(a => a.id === parseInt(queryAccountId))) {
            this.expense.compte_id = parseInt(queryAccountId);
          } else if (this.accounts.length === 1) {
            // S'il n'y a qu'un seul compte, le sélectionner automatiquement
            this.expense.compte_id = this.accounts[0].id;
          }
        })
        .catch(error => {
          console.error('Erreur lors du chargement des comptes:', error);
          this.errors.push('Impossible de charger les comptes.');
        });
    },
    fetchBudgetCategories() {
      if (!this.expense.budget_id) {
        this.budgetCategories = [];
        this.expense.categorie_budget_id = '';
        return;
      }
      
      axios.get(`/api/user/budgets/${this.expense.budget_id}/categories`)
        .then(response => {
          this.budgetCategories = response.data;
          
          // Réinitialiser la catégorie sélectionnée
          this.expense.categorie_budget_id = '';
        })
        .catch(error => {
          console.error('Erreur lors du chargement des catégories du budget:', error);
          this.errors.push('Impossible de charger les catégories du budget.');
        });
    },
    validateForm() {
      this.errors = [];

      if (!this.expense.budget_id) {
        this.errors.push('Veuillez sélectionner un budget.');
      }

      if (!this.expense.categorie_budget_id) {
        this.errors.push('Veuillez sélectionner une catégorie.');
      }

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

      if (!this.expense.compte_id) {
        this.errors.push('Veuillez sélectionner un compte.');
      }

      return this.errors.length === 0;
    },
    saveExpense() {
      if (!this.validateForm()) {
        return;
      }

      axios.post('/api/user/expenses', this.expense)
        .then(response => {
          this.$toasted.success('Dépense enregistrée avec succès!');
          
          if (this.$route.query.redirect) {
            // Si une redirection est spécifiée dans l'URL
            this.$router.push(this.$route.query.redirect);
          } else {
            // Sinon, rediriger vers la liste des dépenses ou le détail de la dépense
            this.$router.push({ 
              name: 'user.expenses.show', 
              params: { id: response.data.data.id } 
            });
          }
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.errors = Object.values(serverErrors).flat();
          } else {
            this.errors = ['Une erreur est survenue lors de l\'enregistrement de la dépense.'];
          }
        });
    }
  },
  created() {
    this.fetchBudgets();
    this.fetchAccounts();
  },
  metaInfo() {
    return {
      title: 'Ajouter une dépense - Budget Manager'
    };
  }
};
</script>