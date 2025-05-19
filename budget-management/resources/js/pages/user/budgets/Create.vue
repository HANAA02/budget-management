<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Créer un nouveau budget</h1>
      <router-link :to="{ name: 'user.budgets.index' }" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
      </router-link>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Détails du budget</h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="submitStep1" v-if="currentStep === 1">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="mb-3">
            <label for="nom" class="form-label">Nom du budget</label>
            <input 
              type="text" 
              id="nom" 
              v-model="budget.nom"
              class="form-control" 
              required
              placeholder="Ex: Budget de mai 2025"
            />
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="date_debut" class="form-label">Date de début</label>
              <input 
                type="date" 
                id="date_debut" 
                v-model="budget.date_debut"
                class="form-control" 
                required
              />
            </div>
            <div class="col-md-6 mb-3">
              <label for="date_fin" class="form-label">Date de fin</label>
              <input 
                type="date" 
                id="date_fin" 
                v-model="budget.date_fin"
                class="form-control" 
                required
                :min="budget.date_debut"
              />
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Modèle</label>
            <div class="alert alert-info">
              <div class="form-check form-check-inline">
                <input 
                  class="form-check-input" 
                  type="radio" 
                  id="modelNew" 
                  v-model="budgetModel" 
                  value="new"
                />
                <label class="form-check-label" for="modelNew">Nouveau budget</label>
              </div>
              <div class="form-check form-check-inline" v-if="previousBudgets.length > 0">
                <input 
                  class="form-check-input" 
                  type="radio" 
                  id="modelPrevious" 
                  v-model="budgetModel" 
                  value="previous"
                />
                <label class="form-check-label" for="modelPrevious">Utiliser un budget précédent</label>
              </div>
            </div>
            
            <div v-if="budgetModel === 'previous' && previousBudgets.length > 0" class="mt-3">
              <select 
                v-model="selectedPreviousBudget" 
                class="form-select" 
                required
              >
                <option value="" disabled selected>Sélectionnez un budget précédent</option>
                <option 
                  v-for="prevBudget in previousBudgets" 
                  :key="prevBudget.id" 
                  :value="prevBudget.id"
                >
                  {{ prevBudget.nom }} ({{ formatDate(prevBudget.date_debut) }} - {{ formatDate(prevBudget.date_fin) }})
                </option>
              </select>
              <small class="form-text text-muted">
                Les allocations par catégorie seront copiées à partir du budget sélectionné.
              </small>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Revenus disponibles</label>
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Source</th>
                    <th>Montant</th>
                    <th>Inclure</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="income in availableIncomes" :key="income.id">
                    <td>{{ income.source }}</td>
                    <td>{{ formatAmount(income.montant) }}</td>
                    <td>
                      <div class="form-check">
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          :id="`income_${income.id}`" 
                          v-model="selectedIncomes"
                          :value="income.id"
                        />
                        <label class="form-check-label" :for="`income_${income.id}`">
                          Inclure
                        </label>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="availableIncomes.length === 0">
                    <td colspan="3" class="text-center">
                      <p class="mb-0">Aucun revenu disponible.</p>
                      <router-link :to="{ name: 'user.incomes.create' }" class="btn btn-sm btn-primary mt-2">
                        <i class="fas fa-plus me-1"></i> Ajouter un revenu
                      </router-link>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" class="text-end fw-bold">Montant total à budgétiser:</td>
                    <td>{{ formatAmount(totalSelectedIncome) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="d-flex justify-content-between">
            <router-link :to="{ name: 'user.budgets.index' }" class="btn btn-secondary">
              Annuler
            </router-link>
            <button type="submit" class="btn btn-primary" :disabled="isStep1Invalid">
              Continuer <i class="fas fa-arrow-right ms-1"></i>
            </button>
          </div>
        </form>

        <div v-else-if="currentStep === 2">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="mb-4">
            <h5>Répartition de votre budget: {{ formatAmount(totalSelectedIncome) }}</h5>
            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              Répartissez votre budget entre les différentes catégories. Vous pouvez ajuster les pourcentages manuellement ou utiliser les valeurs par défaut.
            </div>
          </div>

          <div class="mb-4">
            <div class="d-flex justify-content-between mb-3">
              <h6>Répartition par catégorie</h6>
              <button 
                type="button" 
                class="btn btn-sm btn-outline-secondary"
                @click="resetToDefaultPercentages"
              >
                <i class="fas fa-sync-alt me-1"></i> Réinitialiser aux valeurs par défaut
              </button>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Catégorie</th>
                    <th>Pourcentage (%)</th>
                    <th>Montant alloué</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(allocation, index) in categoryAllocations" :key="allocation.id">
                    <td>
                      <span 
                        class="badge rounded-pill" 
                        :style="{ backgroundColor: allocation.couleur, color: getContrastColor(allocation.couleur) }"
                      >
                        {{ allocation.nom }}
                      </span>
                    </td>
                    <td style="width: 30%;">
                      <div class="input-group">
                        <input 
                          type="number" 
                          class="form-control" 
                          v-model.number="allocation.pourcentage"
                          min="0" 
                          max="100" 
                          step="0.01"
                          @input="updateAllocations(index)"
                        />
                        <span class="input-group-text">%</span>
                      </div>
                    </td>
                    <td>
                      <div class="input-group">
                        <span class="input-group-text">MAD</span>
                        <input 
                          type="number" 
                          class="form-control" 
                          v-model.number="allocation.montant"
                          min="0" 
                          step="0.01"
                          @input="updateAllocationPercentage(index)"
                        />
                      </div>
                    </td>
                    <td>
                      <button 
                        type="button" 
                        class="btn btn-sm btn-danger"
                        @click="removeCategory(index)"
                        :disabled="categoryAllocations.length <= 1"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2" class="text-end fw-bold">Total:</td>
                    <td 
                      :class="isBalanced ? 'text-success' : 'text-danger'"
                    >
                      {{ formatAmount(totalAllocated) }} ({{ totalPercentage.toFixed(2) }}%)
                    </td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div 
              class="alert" 
              :class="isBalanced ? 'alert-success' : 'alert-danger'"
            >
              <i 
                class="me-2" 
                :class="isBalanced ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'"
              ></i>
              <span v-if="isBalanced">
                Votre budget est équilibré.
              </span>
              <span v-else>
                <strong>Attention:</strong> La somme des pourcentages doit être égale à 100%.
                <span v-if="totalPercentage < 100">
                  Il vous reste {{ (100 - totalPercentage).toFixed(2) }}% à allouer 
                  ({{ formatAmount(totalSelectedIncome - totalAllocated) }}).
                </span>
                <span v-else>
                  Vous avez dépassé 100% de {{ (totalPercentage - 100).toFixed(2) }}% 
                  ({{ formatAmount(totalAllocated - totalSelectedIncome) }}).
                </span>
              </span>
            </div>
          </div>

          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" @click="currentStep = 1">
              <i class="fas fa-arrow-left me-1"></i> Retour
            </button>
            <button 
              type="button" 
              class="btn btn-primary"
              @click="createBudget"
              :disabled="!isBalanced"
            >
              <i class="fas fa-save me-1"></i> Créer le budget
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserBudgetCreate',
  data() {
    return {
      currentStep: 1,
      budget: {
        nom: '',
        date_debut: this.formatDateForInput(new Date()),
        date_fin: this.formatDateForInput(new Date(new Date().setMonth(new Date().getMonth() + 1)))
      },
      budgetModel: 'new',
      previousBudgets: [],
      selectedPreviousBudget: '',
      availableIncomes: [],
      selectedIncomes: [],
      categories: [],
      categoryAllocations: [],
      errors: []
    };
  },
  computed: {
    totalSelectedIncome() {
      return this.availableIncomes
        .filter(income => this.selectedIncomes.includes(income.id))
        .reduce((sum, income) => sum + parseFloat(income.montant), 0);
    },
    totalAllocated() {
      return this.categoryAllocations.reduce((sum, allocation) => sum + parseFloat(allocation.montant || 0), 0);
    },
    totalPercentage() {
      return this.categoryAllocations.reduce((sum, allocation) => sum + parseFloat(allocation.pourcentage || 0), 0);
    },
    isBalanced() {
      return Math.abs(this.totalPercentage - 100) < 0.01;
    },
    isStep1Invalid() {
      return !this.budget.nom || 
             !this.budget.date_debut || 
             !this.budget.date_fin || 
             this.selectedIncomes.length === 0 ||
             (this.budgetModel === 'previous' && !this.selectedPreviousBudget);
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
    formatAmount(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount || 0);
    },
    getContrastColor(hexColor) {
      if (!hexColor) return '#000000';
      
      // Convertir la couleur hex en RGB
      const r = parseInt(hexColor.substr(1, 2), 16);
      const g = parseInt(hexColor.substr(3, 2), 16);
      const b = parseInt(hexColor.substr(5, 2), 16);
      
      // Calculer la luminosité (formule de perception)
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
      
      // Retourner du texte blanc ou noir en fonction de la luminosité
      return luminance > 0.5 ? '#000000' : '#FFFFFF';
    },
    fetchInitialData() {
      // Charger les budgets précédents
      axios.get('/api/user/budgets')
        .then(response => {
          this.previousBudgets = response.data.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des budgets précédents:', error);
        });
      
      // Charger les revenus disponibles
      axios.get('/api/user/incomes/available')
        .then(response => {
          this.availableIncomes = response.data.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des revenus disponibles:', error);
        });
      
      // Charger les catégories
      axios.get('/api/categories')
        .then(response => {
          this.categories = response.data.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des catégories:', error);
        });
    },
    submitStep1() {
      this.errors = [];
      
      // Valider les dates
      const startDate = new Date(this.budget.date_debut);
      const endDate = new Date(this.budget.date_fin);
      
      if (endDate <= startDate) {
        this.errors.push('La date de fin doit être postérieure à la date de début.');
        return;
      }
      
      // Valider les revenus sélectionnés
      if (this.selectedIncomes.length === 0) {
        this.errors.push('Veuillez sélectionner au moins une source de revenu.');
        return;
      }
      
      // Valider le modèle sélectionné
      if (this.budgetModel === 'previous' && !this.selectedPreviousBudget) {
        this.errors.push('Veuillez sélectionner un budget précédent.');
        return;
      }
      
      if (this.budgetModel === 'previous') {
        // Charger les allocations du budget précédent
        axios.get(`/api/user/budgets/${this.selectedPreviousBudget}/allocations`)
          .then(response => {
            this.initCategoryAllocations(response.data);
            this.currentStep = 2;
          })
          .catch(error => {
            console.error('Erreur lors du chargement des allocations du budget précédent:', error);
            this.errors.push('Impossible de charger les allocations du budget précédent.');
          });
      } else {
        // Utiliser les pourcentages par défaut des catégories
        this.initCategoryAllocations();
        this.currentStep = 2;
      }
    },
    initCategoryAllocations(previousAllocations = null) {
      this.categoryAllocations = [];
      
      if (previousAllocations) {
        // Utiliser les allocations du budget précédent
        this.categoryAllocations = previousAllocations.map(allocation => {
          const percentage = allocation.pourcentage;
          return {
            id: allocation.categorie_id,
            nom: allocation.nom,
            couleur: allocation.couleur,
            pourcentage: percentage,
            montant: (percentage / 100) * this.totalSelectedIncome
          };
        });
      } else {
        // Utiliser les pourcentages par défaut
        this.categoryAllocations = this.categories.map(category => {
          const percentage = category.pourcentage_defaut;
          return {
            id: category.id,
            nom: category.nom,
            couleur: category.couleur,
            pourcentage: percentage,
            montant: (percentage / 100) * this.totalSelectedIncome
          };
        });
      }
    },
    updateAllocations(index) {
      const allocation = this.categoryAllocations[index];
      allocation.montant = (allocation.pourcentage / 100) * this.totalSelectedIncome;
    },
    updateAllocationPercentage(index) {
      const allocation = this.categoryAllocations[index];
      allocation.pourcentage = (allocation.montant / this.totalSelectedIncome) * 100;
    },
    resetToDefaultPercentages() {
      this.categoryAllocations.forEach((allocation, index) => {
        const category = this.categories.find(c => c.id === allocation.id);
        if (category) {
          allocation.pourcentage = category.pourcentage_defaut;
          this.updateAllocations(index);
        }
      });
    },
    removeCategory(index) {
      if (this.categoryAllocations.length > 1) {
        this.categoryAllocations.splice(index, 1);
      }
    },
    createBudget() {
      if (!this.isBalanced) {
        this.errors = ['Veuillez équilibrer votre budget avant de continuer.'];
        return;
      }
      
      // Préparer les données du budget
      const budgetData = {
        nom: this.budget.nom,
        date_debut: this.budget.date_debut,
        date_fin: this.budget.date_fin,
        revenus: this.selectedIncomes,
        categories: this.categoryAllocations.map(allocation => ({
          id: allocation.id,
          pourcentage: allocation.pourcentage,
          montant: allocation.montant
        }))
      };
      
      // Créer le budget
      axios.post('/api/user/budgets', budgetData)
        .then(response => {
          this.$toasted.success('Budget créé avec succès!');
          this.$router.push({ 
            name: 'user.budgets.show', 
            params: { id: response.data.data.id } 
          });
        })
        .catch(error => {
          console.error('Erreur lors de la création du budget:', error);
          
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.errors = Object.values(serverErrors).flat();
          } else {
            this.errors = ['Une erreur est survenue lors de la création du budget.'];
          }
        });
    }
  },
  mounted() {
    this.fetchInitialData();
  },
  metaInfo() {
    return {
      title: 'Créer un budget - Budget Manager'
    };
  }
};
</script>