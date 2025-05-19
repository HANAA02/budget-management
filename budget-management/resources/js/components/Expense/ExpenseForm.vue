<template>
  <div class="expense-form">
    <div class="card">
      <div class="card-header">
        <h3>{{ isEditing ? 'Modifier la dépense' : 'Ajouter une nouvelle dépense' }}</h3>
      </div>
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="alert alert-danger" v-if="errors.length">
            <ul>
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="form-group mb-3">
            <label for="categorie_budget_id">Catégorie:</label>
            <select 
              id="categorie_budget_id" 
              v-model="expense.categorie_budget_id" 
              class="form-control" 
              required
            >
              <option value="" disabled>Sélectionnez une catégorie</option>
              <option 
                v-for="category in categories" 
                :key="category.id" 
                :value="category.id"
              >
                {{ category.nom }} - Budget disponible: {{ formatMontant(category.montant_restant) }}
              </option>
            </select>
          </div>

          <div class="form-group mb-3">
            <label for="description">Description:</label>
            <input 
              type="text" 
              id="description" 
              v-model="expense.description" 
              class="form-control" 
              required
            />
          </div>

          <div class="form-group mb-3">
            <label for="montant">Montant:</label>
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

          <div class="form-group mb-3">
            <label for="date_depense">Date de la dépense:</label>
            <input 
              type="date" 
              id="date_depense" 
              v-model="expense.date_depense" 
              class="form-control" 
              :max="currentDate" 
              required
            />
          </div>

          <div class="form-group mb-3">
            <label for="statut">Statut:</label>
            <select id="statut" v-model="expense.statut" class="form-control">
              <option value="validée">Validée</option>
              <option value="en attente">En attente</option>
              <option value="annulée">Annulée</option>
            </select>
          </div>

          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" @click="resetForm">Annuler</button>
            <button type="submit" class="btn btn-primary">
              {{ isEditing ? 'Mettre à jour' : 'Enregistrer' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ExpenseForm',
  props: {
    editingExpense: {
      type: Object,
      default: null
    },
    budgetId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      expense: {
        categorie_budget_id: '',
        description: '',
        montant: '',
        date_depense: this.formatDateForInput(new Date()),
        statut: 'validée'
      },
      categories: [],
      errors: [],
      isEditing: false
    };
  },
  computed: {
    currentDate() {
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
    formatMontant(montant) {
      return new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(montant);
    },
    resetForm() {
      this.expense = {
        categorie_budget_id: '',
        description: '',
        montant: '',
        date_depense: this.formatDateForInput(new Date()),
        statut: 'validée'
      };
      this.errors = [];
      this.isEditing = false;
      this.$emit('cancel');
    },
    loadCategories() {
      axios.get(`/api/budgets/${this.budgetId}/categories`)
        .then(response => {
          this.categories = response.data.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des catégories:', error);
          this.errors.push('Impossible de charger les catégories de budget.');
        });
    },
    submitForm() {
      this.errors = [];
      
      // Validation basique
      if (!this.expense.categorie_budget_id) {
        this.errors.push('Veuillez sélectionner une catégorie.');
      }
      if (!this.expense.description) {
        this.errors.push('Veuillez entrer une description.');
      }
      if (!this.expense.montant || this.expense.montant <= 0) {
        this.errors.push('Veuillez entrer un montant valide.');
      }
      if (!this.expense.date_depense) {
        this.errors.push('Veuillez sélectionner une date.');
      }
      
      if (this.errors.length) {
        return;
      }
      
      const url = this.isEditing 
        ? `/api/expenses/${this.expense.id}` 
        : '/api/expenses';
      const method = this.isEditing ? 'put' : 'post';
      
      axios[method](url, this.expense)
        .then(response => {
          this.$emit('saved', response.data.data);
          this.resetForm();
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            Object.keys(serverErrors).forEach(key => {
              this.errors.push(serverErrors[key][0]);
            });
          } else {
            this.errors.push('Une erreur est survenue. Veuillez réessayer.');
          }
        });
    },
    initForm() {
      if (this.editingExpense) {
        this.expense = { ...this.editingExpense };
        this.isEditing = true;
      } else {
        this.resetForm();
      }
    }
  },
  created() {
    this.loadCategories();
    this.initForm();
  },
  watch: {
    editingExpense: {
      handler(newVal) {
        if (newVal) {
          this.initForm();
        }
      },
      deep: true
    }
  }
};
</script>

<style scoped>
.expense-form {
  max-width: 600px;
  margin: 0 auto;
}
</style>