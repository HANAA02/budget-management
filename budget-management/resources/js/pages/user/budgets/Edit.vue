<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Modifier le budget</h1>
      <div>
        <router-link :to="{ name: 'user.budgets.show', params: { id: $route.params.id } }" class="btn btn-info me-2">
          <i class="fas fa-eye me-1"></i> Voir les détails
        </router-link>
        <router-link :to="{ name: 'user.budgets.index' }" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <div v-else>
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
        </div>
        <div class="card-body">
          <form @submit.prevent="updateBudgetInfo">
            <div v-if="generalErrors.length" class="alert alert-danger">
              <ul class="mb-0">
                <li v-for="(error, index) in generalErrors" :key="index">{{ error }}</li>
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

            <div class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              <strong>Note:</strong> La modification des dates peut affecter la validité de certaines dépenses. 
              Les dépenses en dehors de la nouvelle période seront automatiquement marquées comme "hors budget".
            </div>

            <div class="text-end">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Mettre à jour les informations
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Allocations par catégorie</h6>
        </div>
        <div class="card-body">
          <div v-if="allocationErrors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in allocationErrors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Attention:</strong> La modification des allocations peut affecter le suivi des dépenses. 
            Assurez-vous que le total des allocations correspond au montant total du budget.
          </div>

          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5>Montant total: {{ formatAmount(totalBudgetAmount) }}</h5>
              <button 
                type="button" 
                class="btn btn-sm btn-outline-secondary"
                @click="resetToDefaultPercentages"
              >
                <i class="fas fa-sync-alt me-1"></i> Réinitialiser aux valeurs par défaut
              </button>
            </div>

            <div class="progress mb-2" style="height: 25px;">
              <div 
                class="progress-bar" 
                role="progressbar" 
                :style="{ width: `${totalPercentage}%` }" 
                :class="isBalanced ? 'bg-success' : (totalPercentage > 100 ? 'bg-danger' : 'bg-warning')"
                aria-valuemin="0" 
                aria-valuemax="100"
              >
                {{ totalPercentage.toFixed(2) }}%
              </div>
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
                  ({{ formatAmount(totalBudgetAmount - totalAllocated) }}).
                </span>
                <span v-else>
                  Vous avez dépassé 100% de {{ (totalPercentage - 100).toFixed(2) }}% 
                  ({{ formatAmount(totalAllocated - totalBudgetAmount) }}).
                </span>
              </span>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Catégorie</th>
                  <th>Pourcentage (%)</th>
                  <th>Montant alloué</th>
                  <th>Dépenses actuelles</th>
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
                  <td style="width: 20%;">
                    <div class="input-group">
                      <input 
                        type="number" 
                        class="form-control" 
                        v-model.number="allocation.pourcentage"
                        min="0" 
                        max="100" 
                        step="0.01"
                        @input="updateAllocationAmount(index)"
                      />
                      <span class="input-group-text">%</span>
                    </div>
                  </td>
                  <td style="width: 25%;">
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
                    {{ formatAmount(allocation.montant_depense) }}
                    <div class="progress mt-1" style="height: 8px;">
                      <div 
                        class="progress-bar" 
                        role="progressbar" 
                        :style="{ width: `${getAllocationProgressPercentage(allocation)}%` }" 
                        :class="getAllocationProgressClass(allocation)"
                      ></div>
                    </div>
                  </td>
                  <td>
                    <button 
                      type="button" 
                      class="btn btn-sm btn-danger"
                      @click="removeCategory(index)"
                      title="Supprimer cette catégorie"
                      :disabled="categoryAllocations.length <= 1"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="table-active">
                  <th>Total</th>
                  <th>{{ totalPercentage.toFixed(2) }}%</th>
                  <th>{{ formatAmount(totalAllocated) }}</th>
                  <th>{{ formatAmount(totalSpent) }}</th>
                  <th>
                    <button 
                      type="button" 
                      class="btn btn-sm btn-success"
                      @click="showAddCategoryModal"
                      title="Ajouter une catégorie"
                    >
                      <i class="fas fa-plus"></i>
                    </button>
                  </th>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="text-end mt-3">
            <button 
              type="button" 
              class="btn btn-primary"
              @click="updateBudgetAllocations"
              :disabled="!isBalanced"
            >
              <i class="fas fa-save me-1"></i> Mettre à jour les allocations
            </button>
          </div>
        </div>
      </div>

      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Revenus associés</h6>
        </div>
        <div class="card-body">
          <div v-if="incomeErrors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in incomeErrors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div v-if="availableIncomes.length === 0 && budgetIncomes.length === 0" class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Aucun revenu disponible. 
            <router-link :to="{ name: 'user.incomes.create' }" class="btn btn-sm btn-primary ml-2">
              <i class="fas fa-plus me-1"></i> Ajouter un revenu
            </router-link>
          </div>

          <div v-else>
            <h5 class="mb-3">Revenus actuels: {{ formatAmount(totalIncome) }}</h5>

            <div class="table-responsive mb-4">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Source</th>
                    <th>Montant</th>
                    <th>Date de perception</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="income in budgetIncomes" :key="income.id">
                    <td>{{ income.source }}</td>
                    <td>{{ formatAmount(income.montant) }}</td>
                    <td>{{ formatDate(income.date_perception) }}</td>
                    <td>
                      <button 
                        type="button" 
                        class="btn btn-sm btn-danger"
                        @click="removeIncome(income.id)"
                      >
                        <i class="fas fa-minus-circle"></i> Retirer
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-if="availableIncomes.length > 0">
              <h6 class="mb-3">Ajouter des revenus</h6>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Source</th>
                      <th>Montant</th>
                      <th>Date de perception</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="income in availableIncomes" :key="income.id">
                      <td>{{ income.source }}</td>
                      <td>{{ formatAmount(income.montant) }}</td>
                      <td>{{ formatDate(income.date_perception) }}</td>
                      <td>
                        <button 
                          type="button" 
                          class="btn btn-sm btn-success"
                          @click="addIncome(income.id)"
                        >
                          <i class="fas fa-plus-circle"></i> Ajouter
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal pour ajouter une catégorie -->
    <div 
      class="modal fade" 
      id="addCategoryModal" 
      tabindex="-1" 
      aria-labelledby="addCategoryModalLabel" 
      aria-hidden="true"
      ref="addCategoryModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addCategoryModalLabel">Ajouter une catégorie</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="new_category" class="form-label">Catégorie</label>
              <select id="new_category" v-model="newCategory.id" class="form-select">
                <option value="" disabled selected>Sélectionnez une catégorie</option>
                <option 
                  v-for="category in availableCategories" 
                  :key="category.id" 
                  :value="category.id"
                >
                  {{ category.nom }}
                </option>
              </select>
            </div>
            <div class="mb-3">
              <label for="new_percentage" class="form-label">Pourcentage (%)</label>
              <div class="input-group">
                <input 
                  type="number" 
                  id="new_percentage" 
                  v-model.number="newCategory.pourcentage"
                  class="form-control" 
                  min="0" 
                  max="100" 
                  step="0.01"
                />
                <span class="input-group-text">%</span>
              </div>
            </div>
            <div class="mb-3">
              <label for="new_amount" class="form-label">Montant</label>
              <div class="input-group">
                <span class="input-group-text">MAD</span>
                <input 
                  type="number" 
                  id="new_amount" 
                  v-model.number="newCategory.montant"
                  class="form-control" 
                  min="0" 
                  step="0.01"
                />
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button 
              type="button" 
              class="btn btn-secondary" 
              data-bs-dismiss="modal"
            >
              Annuler
            </button>
            <button 
              type="button" 
              class="btn btn-success"
              @click="addCategory"
              :disabled="!newCategory.id || (!newCategory.pourcentage && !newCategory.montant)"
            >
              <i class="fas fa-plus me-1"></i> Ajouter
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  name: 'UserBudgetEdit',
  data() {
    return {
      budget: {
        id: null,
        nom: '',
        date_debut: '',
        date_fin: '',
        montant_total: 0
      },
      categoryAllocations: [],
      budgetIncomes: [],
      availableIncomes: [],
      availableCategories: [],
      loading: true,
      generalErrors: [],
      allocationErrors: [],
      incomeErrors: [],
      newCategory: {
        id: '',
        pourcentage: 0,
        montant: 0
      },
      addCategoryModal: null
    };
  },
  computed: {
    totalAllocated() {
      return this.categoryAllocations.reduce((sum, allocation) => sum + parseFloat(allocation.montant || 0), 0);
    },
    totalSpent() {
      return this.categoryAllocations.reduce((sum, allocation) => sum + parseFloat(allocation.montant_depense || 0), 0);
    },
    totalPercentage() {
      return this.categoryAllocations.reduce((sum, allocation) => sum + parseFloat(allocation.pourcentage || 0), 0);
    },
    isBalanced() {
      return Math.abs(this.totalPercentage - 100) < 0.01;
    },
    totalIncome() {
      return this.budgetIncomes.reduce((sum, income) => sum + parseFloat(income.montant), 0);
    },
    totalBudgetAmount() {
      return this.budget.montant_total;
    }
  },
  methods: {
    fetchBudget() {
      this.loading = true;
      
      axios.get(`/api/user/budgets/${this.$route.params.id}`)
        .then(response => {
          this.budget = response.data.data;
          return axios.get(`/api/user/budgets/${this.budget.id}/categories`);
        })
        .then(response => {
          this.categoryAllocations = response.data;
          return axios.get(`/api/user/budgets/${this.budget.id}/incomes`);
        })
        .then(response => {
          this.budgetIncomes = response.data;
          return axios.get('/api/user/incomes/available');
        })
        .then(response => {
          this.availableIncomes = response.data.data;
          return axios.get('/api/categories');
        })
        .then(response => {
          const allCategories = response.data.data;
          
          // Filtrer les catégories déjà présentes dans le budget
          const existingCategoryIds = this.categoryAllocations.map(allocation => allocation.categorie_id);
          this.availableCategories = allCategories.filter(category => !existingCategoryIds.includes(category.id));
          
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement du budget:', error);
          this.$toasted.error('Impossible de charger les détails du budget.');
          this.loading = false;
          this.$router.push({ name: 'user.budgets.index' });
        });
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
    getAllocationProgressPercentage(allocation) {
      if (!allocation.montant || allocation.montant <= 0) return 0;
      
      const percentage = (allocation.montant_depense / allocation.montant) * 100;
      return Math.min(Math.max(0, percentage), 100);
    },
    getAllocationProgressClass(allocation) {
      const percentage = this.getAllocationProgressPercentage(allocation);
      
      if (percentage < 50) {
        return 'bg-success';
      } else if (percentage < 75) {
        return 'bg-info';
      } else if (percentage < 90) {
        return 'bg-warning';
      } else {
        return 'bg-danger';
      }
    },
    updateAllocationAmount(index) {
      const allocation = this.categoryAllocations[index];
      allocation.montant = (allocation.pourcentage / 100) * this.totalBudgetAmount;
    },
    updateAllocationPercentage(index) {
      const allocation = this.categoryAllocations[index];
      allocation.pourcentage = (allocation.montant / this.totalBudgetAmount) * 100;
    },
    resetToDefaultPercentages() {
      // Récupérer les pourcentages par défaut de chaque catégorie
      this.categoryAllocations.forEach((allocation, index) => {
        const category = this.availableCategories.find(c => c.id === allocation.categorie_id) || { pourcentage_defaut: 0 };
        allocation.pourcentage = category.pourcentage_defaut || 0;
        this.updateAllocationAmount(index);
      });
      
      // Répartir le reste proportionnellement si le total n'est pas 100%
      if (Math.abs(this.totalPercentage - 100) > 0.01) {
        const factor = 100 / this.totalPercentage;
        
        this.categoryAllocations.forEach((allocation, index) => {
          allocation.pourcentage *= factor;
          this.updateAllocationAmount(index);
        });
      }
    },
    removeCategory(index) {
      // Vérifier si la catégorie a des dépenses
      const allocation = this.categoryAllocations[index];
      
      if (allocation.montant_depense > 0) {
        if (!confirm(`Cette catégorie contient des dépenses (${this.formatAmount(allocation.montant_depense)}). Êtes-vous sûr de vouloir la supprimer ?`)) {
          return;
        }
      }
      
      // Ajouter la catégorie à la liste des disponibles
      const removedCategory = {
        id: allocation.categorie_id,
        nom: allocation.nom,
        couleur: allocation.couleur,
        pourcentage_defaut: allocation.pourcentage_defaut
      };
      
      this.availableCategories.push(removedCategory);
      
      // Supprimer la catégorie de la liste des allocations
      this.categoryAllocations.splice(index, 1);
      
      // Mettre à jour les pourcentages pour répartir le budget restant
      if (this.categoryAllocations.length > 0) {
        const factor = 100 / this.totalPercentage;
        
        this.categoryAllocations.forEach((allocation, idx) => {
          allocation.pourcentage *= factor;
          this.updateAllocationAmount(idx);
        });
      }
    },
    showAddCategoryModal() {
      this.newCategory = {
        id: '',
        pourcentage: 0,
        montant: 0
      };
      
      this.addCategoryModal.show();
    },
    addCategory() {
      if (!this.newCategory.id) {
        return;
      }
      
      // Récupérer les informations de la catégorie
      const category = this.availableCategories.find(c => c.id === this.newCategory.id);
      
      if (!category) {
        return;
      }
      
      // Calculer le montant alloué
      let montant = this.newCategory.montant;
      let pourcentage = this.newCategory.pourcentage;
      
      if (montant && !pourcentage) {
        pourcentage = (montant / this.totalBudgetAmount) * 100;
      } else if (pourcentage && !montant) {
        montant = (pourcentage / 100) * this.totalBudgetAmount;
      } else if (!montant && !pourcentage) {
        // Utiliser le pourcentage par défaut
        pourcentage = category.pourcentage_defaut || 0;
        montant = (pourcentage / 100) * this.totalBudgetAmount;
      }
      
      // Créer la nouvelle allocation
      const newAllocation = {
        id: null, // Sera attribué par le serveur
        categorie_id: category.id,
        nom: category.nom,
        couleur: category.couleur,
        pourcentage_defaut: category.pourcentage_defaut,
        pourcentage: pourcentage,
        montant: montant,
        montant_depense: 0,
        montant_restant: montant
      };
      
      // Ajouter la nouvelle allocation
      this.categoryAllocations.push(newAllocation);
      
      // Retirer la catégorie de la liste des disponibles
      const index = this.availableCategories.findIndex(c => c.id === category.id);
      if (index !== -1) {
        this.availableCategories.splice(index, 1);
      }
      
      // Fermer le modal
      this.addCategoryModal.hide();
    },
    validateBudgetInfo() {
      this.generalErrors = [];
      
      if (!this.budget.nom) {
        this.generalErrors.push('Le nom du budget est requis.');
      }
      
      if (!this.budget.date_debut || !this.budget.date_fin) {
        this.generalErrors.push('Les dates de début et de fin sont requises.');
      } else {
        const startDate = new Date(this.budget.date_debut);
        const endDate = new Date(this.budget.date_fin);
        
        if (endDate <= startDate) {
          this.generalErrors.push('La date de fin doit être postérieure à la date de début.');
        }
      }
      
      return this.generalErrors.length === 0;
    },
    validateAllocations() {
      this.allocationErrors = [];
      
      if (this.categoryAllocations.length === 0) {
        this.allocationErrors.push('Vous devez ajouter au moins une catégorie.');
        return false;
      }
      
      if (!this.isBalanced) {
        this.allocationErrors.push('La somme des pourcentages doit être égale à 100%.');
        return false;
      }
      
      return true;
    },
    updateBudgetInfo() {
      if (!this.validateBudgetInfo()) {
        return;
      }
      
      const budgetData = {
        nom: this.budget.nom,
        date_debut: this.budget.date_debut,
        date_fin: this.budget.date_fin
      };
      
      axios.put(`/api/user/budgets/${this.budget.id}/info`, budgetData)
        .then(response => {
          this.$toasted.success('Informations du budget mises à jour avec succès!');
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.generalErrors = Object.values(serverErrors).flat();
          } else {
            this.generalErrors = ['Une erreur est survenue lors de la mise à jour des informations du budget.'];
          }
        });
    },
    updateBudgetAllocations() {
      if (!this.validateAllocations()) {
        return;
      }
      
      const allocationsData = this.categoryAllocations.map(allocation => ({
        id: allocation.id,
        categorie_id: allocation.categorie_id,
        pourcentage: allocation.pourcentage,
        montant: allocation.montant
      }));
      
      axios.put(`/api/user/budgets/${this.budget.id}/allocations`, { allocations: allocationsData })
        .then(response => {
          this.$toasted.success('Allocations du budget mises à jour avec succès!');
          
          // Mettre à jour les allocations avec les IDs du serveur
          const updatedAllocations = response.data.data;
          this.categoryAllocations.forEach((allocation, index) => {
            if (!allocation.id) {
              const match = updatedAllocations.find(a => a.categorie_id === allocation.categorie_id);
              if (match) {
                this.categoryAllocations[index].id = match.id;
              }
            }
          });
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.allocationErrors = Object.values(serverErrors).flat();
          } else {
            this.allocationErrors = ['Une erreur est survenue lors de la mise à jour des allocations du budget.'];
          }
        });
    },
    addIncome(incomeId) {
      axios.post(`/api/user/budgets/${this.budget.id}/incomes`, { income_id: incomeId })
        .then(response => {
          // Trouver le revenu dans la liste des disponibles
          const index = this.availableIncomes.findIndex(income => income.id === incomeId);
          
          if (index !== -1) {
            // Ajouter le revenu à la liste des revenus du budget
            this.budgetIncomes.push(this.availableIncomes[index]);
            
            // Retirer le revenu de la liste des disponibles
            this.availableIncomes.splice(index, 1);
            
            // Mettre à jour le montant total du budget
            this.budget.montant_total = this.totalIncome;
            
            // Mettre à jour les montants alloués
            this.categoryAllocations.forEach((allocation, idx) => {
              this.updateAllocationAmount(idx);
            });
          }
          
          this.$toasted.success('Revenu ajouté au budget avec succès!');
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.incomeErrors = Object.values(serverErrors).flat();
          } else {
            this.incomeErrors = ['Une erreur est survenue lors de l\'ajout du revenu au budget.'];
          }
        });
    },
    removeIncome(incomeId) {
      if (!confirm('Êtes-vous sûr de vouloir retirer ce revenu du budget ? Cela affectera toutes les allocations.')) {
        return;
      }
      
      axios.delete(`/api/user/budgets/${this.budget.id}/incomes/${incomeId}`)
        .then(response => {
          // Trouver le revenu dans la liste des revenus du budget
          const index = this.budgetIncomes.findIndex(income => income.id === incomeId);
          
          if (index !== -1) {
            // Ajouter le revenu à la liste des disponibles
            this.availableIncomes.push(this.budgetIncomes[index]);
            
            // Retirer le revenu de la liste des revenus du budget
            this.budgetIncomes.splice(index, 1);
            
            // Mettre à jour le montant total du budget
            this.budget.montant_total = this.totalIncome;
            
            // Mettre à jour les montants alloués
            this.categoryAllocations.forEach((allocation, idx) => {
              this.updateAllocationAmount(idx);
            });
          }
          
          this.$toasted.success('Revenu retiré du budget avec succès!');
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.incomeErrors = Object.values(serverErrors).flat();
          } else {
            this.incomeErrors = ['Une erreur est survenue lors du retrait du revenu du budget.'];
          }
        });
    }
  },
  mounted() {
    this.fetchBudget();
    this.addCategoryModal = new Modal(this.$refs.addCategoryModal);
  },
  metaInfo() {
    return {
      title: 'Modifier le budget - Budget Manager'
    };
  }
};
</script>