<template>
  <div class="income-form">
    <div class="card">
      <div class="card-header">
        <h3>{{ isEditing ? 'Modifier un revenu' : 'Ajouter un nouveau revenu' }}</h3>
      </div>
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="alert alert-danger" v-if="errors.length">
            <ul>
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="form-group mb-3">
            <label for="source">Source du revenu:</label>
            <input 
              type="text" 
              id="source" 
              v-model="income.source" 
              class="form-control" 
              required
              placeholder="Ex: Salaire principal, Freelance, Allocation..."
            />
          </div>

          <div class="form-group mb-3">
            <label for="compte_id">Compte:</label>
            <select 
              id="compte_id" 
              v-model="income.compte_id" 
              class="form-control" 
              required
            >
              <option value="" disabled>Sélectionnez un compte</option>
              <option 
                v-for="account in accounts" 
                :key="account.id" 
                :value="account.id"
              >
                {{ account.nom }} ({{ formatMontant(account.solde) }})
              </option>
            </select>
          </div>

          <div class="form-group mb-3">
            <label for="montant">Montant:</label>
            <div class="input-group">
              <span class="input-group-text">MAD</span>
              <input 
                type="number" 
                id="montant" 
                v-model="income.montant" 
                class="form-control" 
                step="0.01" 
                min="0.01" 
                required
              />
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="date_perception">Date de perception:</label>
            <input 
              type="date" 
              id="date_perception" 
              v-model="income.date_perception" 
              class="form-control" 
              required
            />
          </div>

          <div class="form-group mb-3">
            <label for="periodicite">Périodicité:</label>
            <select id="periodicite" v-model="income.periodicite" class="form-control">
              <option value="mensuel">Mensuel</option>
              <option value="bimensuel">Bimensuel</option>
              <option value="hebdomadaire">Hebdomadaire</option>
              <option value="trimestriel">Trimestriel</option>
              <option value="annuel">Annuel</option>
              <option value="ponctuel">Ponctuel (une seule fois)</option>
            </select>
          </div>

          <div class="form-check mb-3">
            <input 
              type="checkbox" 
              id="add_to_budget" 
              v-model="addToBudget" 
              class="form-check-input"
            />
            <label class="form-check-label" for="add_to_budget">
              Ajouter ce revenu à mon budget actuel
            </label>
            <small class="form-text text-muted d-block">
              Si activé, ce revenu sera automatiquement inclus dans votre budget actif.
            </small>
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
  name: 'IncomeForm',
  props: {
    editingIncome: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      income: {
        source: '',
        compte_id: '',
        montant: '',
        date_perception: this.formatDateForInput(new Date()),
        periodicite: 'mensuel'
      },
      accounts: [],
      errors: [],
      isEditing: false,
      addToBudget: true
    };
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
      this.income = {
        source: '',
        compte_id: '',
        montant: '',
        date_perception: this.formatDateForInput(new Date()),
        periodicite: 'mensuel'
      };
      this.addToBudget = true;
      this.errors = [];
      this.isEditing = false;
      this.$emit('cancel');
    },
    loadAccounts() {
      axios.get('/api/accounts')
        .then(response => {
          this.accounts = response.data.data;
          // Si l'utilisateur n'a qu'un seul compte, le sélectionner automatiquement
          if (this.accounts.length === 1 && !this.income.compte_id) {
            this.income.compte_id = this.accounts[0].id;
          }
        })
        .catch(error => {
          console.error('Erreur lors du chargement des comptes:', error);
          this.errors.push('Impossible de charger les comptes. Veuillez réessayer.');
        });
    },
    validateForm() {
      this.errors = [];
      
      // Validation basique
      if (!this.income.source) {
        this.errors.push('Veuillez indiquer la source du revenu.');
      }
      if (!this.income.compte_id) {
        this.errors.push('Veuillez sélectionner un compte.');
      }
      if (!this.income.montant || this.income.montant <= 0) {
        this.errors.push('Veuillez entrer un montant valide.');
      }
      if (!this.income.date_perception) {
        this.errors.push('Veuillez sélectionner une date de perception.');
      }
      
      return this.errors.length === 0;
    },
    submitForm() {
      if (!this.validateForm()) {
        return;
      }
      
      // Préparer les données
      const formData = {
        ...this.income,
        add_to_budget: this.addToBudget
      };
      
      const url = this.isEditing 
        ? `/api/incomes/${this.income.id}` 
        : '/api/incomes';
      const method = this.isEditing ? 'put' : 'post';
      
      axios[method](url, formData)
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
      if (this.editingIncome) {
        this.income = { ...this.editingIncome };
        this.isEditing = true;
        this.addToBudget = false; // Désactiver pour l'édition car le revenu est déjà budgétisé
      } else {
        this.resetForm();
      }
    }
  },
  created() {
    this.loadAccounts();
    this.initForm();
  },
  watch: {
    editingIncome: {
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
.income-form {
  max-width: 600px;
  margin: 0 auto;
}
</style>