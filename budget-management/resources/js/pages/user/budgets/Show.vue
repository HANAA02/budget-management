<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>{{ budget.nom }}</h1>
      <div>
        <router-link 
          :to="{ name: 'user.expenses.create', query: { budget_id: budget.id } }" 
          class="btn btn-success me-2"
        >
          <i class="fas fa-plus me-1"></i> Ajouter une dépense
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
      <!-- Résumé du budget -->
      <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-primary shadow h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                    Montant total
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ formatAmount(budget.montant_total) }}
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-success shadow h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                    Dépensé
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ formatAmount(totalSpent) }}
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-info shadow h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                    Restant
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ formatAmount(budget.montant_total - totalSpent) }}
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-warning shadow h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                    Progression
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ budgetProgress }}%
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-percentage fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Informations générales -->
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Informations générales</h6>
          <router-link 
            :to="{ name: 'user.budgets.edit', params: { id: budget.id } }" 
            class="btn btn-sm btn-primary"
          >
            <i class="fas fa-edit me-1"></i> Modifier
          </router-link>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <table class="table table-bordered">
                <tbody>
                  <tr>
                    <th style="width: 40%">Période</th>
                    <td>{{ formatDate(budget.date_debut) }} - {{ formatDate(budget.date_fin) }}</td>
                  </tr>
                  <tr>
                    <th>Date de création</th>
                    <td>{{ formatDate(budget.date_creation) }}</td>
                  </tr>
                  <tr>
                    <th>Progression temporelle</th>
                    <td>
                      <div class="progress" style="height: 20px;">
                        <div 
                          class="progress-bar bg-info" 
                          role="progressbar" 
                          :style="{ width: `${timeProgress}%` }" 
                          :aria-valuenow="timeProgress" 
                          aria-valuemin="0" 
                          aria-valuemax="100"
                        >
                          {{ timeProgress }}%
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-6">
              <h6 class="mb-3">Sources de revenus</h6>
              <table class="table table-bordered table-sm">
                <thead>
                  <tr>
                    <th>Source</th>
                    <th>Montant</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="income in budgetIncomes" :key="income.id">
                    <td>{{ income.source }}</td>
                    <td>{{ formatAmount(income.montant) }}</td>
                  </tr>
                  <tr>
                    <th>Total</th>
                    <td class="font-weight-bold">{{ formatAmount(budget.montant_total) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Répartition des allocations -->
      <div class="row mb-4">
        <div class="col-lg-6">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Répartition du budget</h6>
            </div>
            <div class="card-body">
              <div class="chart-container" style="height: 300px;">
                <canvas ref="allocationChart"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Progression des dépenses</h6>
            </div>
            <div class="card-body">
              <div class="chart-container" style="height: 300px;">
                <canvas ref="expensesChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Détail des catégories -->
      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Détail par catégorie</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Catégorie</th>
                  <th>Montant alloué</th>
                  <th>Dépenses</th>
                  <th>Restant</th>
                  <th>% Utilisé</th>
                  <th>Progression</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="category in budgetCategories" :key="category.id">
                  <td>
                    <span 
                      class="badge rounded-pill" 
                      :style="{ backgroundColor: category.couleur, color: getContrastColor(category.couleur) }"
                    >
                      {{ category.nom }}
                    </span>
                  </td>
                  <td>{{ formatAmount(category.montant_alloue) }}</td>
                  <td>{{ formatAmount(category.montant_depense) }}</td>
                  <td 
                    :class="category.montant_restant < 0 ? 'text-danger' : 'text-success'"
                  >
                    {{ formatAmount(category.montant_restant) }}
                  </td>
                  <td>{{ category.pourcentage_utilisation }}%</td>
                  <td style="width: 20%;">
                    <div class="progress" style="height: 20px;">
                      <div 
                        class="progress-bar" 
                        role="progressbar" 
                        :style="{ width: `${category.pourcentage_utilisation}%` }" 
                        :class="getCategoryProgressClass(category.pourcentage_utilisation)"
                        :aria-valuenow="category.pourcentage_utilisation" 
                        aria-valuemin="0" 
                        aria-valuemax="100"
                      >
                        {{ category.pourcentage_utilisation }}%
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Dépenses récentes -->
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Dépenses récentes</h6>
          <router-link 
            :to="{ name: 'user.expenses.index', query: { budget_id: budget.id } }" 
            class="btn btn-sm btn-outline-primary"
          >
            Voir toutes les dépenses
          </router-link>
        </div>
        <div class="card-body">
          <div v-if="recentExpenses.length === 0" class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Aucune dépense n'a été enregistrée pour ce budget.
            <div class="mt-2">
              <router-link 
                :to="{ name: 'user.expenses.create', query: { budget_id: budget.id } }" 
                class="btn btn-sm btn-primary"
              >
                <i class="fas fa-plus me-1"></i> Ajouter une dépense
              </router-link>
            </div>
          </div>
          <div v-else class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Description</th>
                  <th>Catégorie</th>
                  <th>Montant</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="expense in recentExpenses" :key="expense.id">
                  <td>{{ formatDate(expense.date_depense) }}</td>
                  <td>{{ expense.description }}</td>
                  <td>
                    <span 
                      class="badge rounded-pill" 
                      :style="{ backgroundColor: expense.categorie.couleur, color: getContrastColor(expense.categorie.couleur) }"
                    >
                      {{ expense.categorie.nom }}
                    </span>
                  </td>
                  <td>{{ formatAmount(expense.montant) }}</td>
                  <td>
                    <router-link 
                      :to="{ name: 'user.expenses.show', params: { id: expense.id } }" 
                      class="btn btn-sm btn-info me-1"
                    >
                      <i class="fas fa-eye"></i>
                    </router-link>
                    <router-link 
                      :to="{ name: 'user.expenses.edit', params: { id: expense.id } }" 
                      class="btn btn-sm btn-primary"
                    >
                      <i class="fas fa-edit"></i>
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';

export default {
  name: 'UserBudgetShow',
  data() {
    return {
      budget: {
        id: null,
        nom: '',
        date_debut: null,
        date_fin: null,
        montant_total: 0,
        date_creation: null
      },
      budgetIncomes: [],
      budgetCategories: [],
      recentExpenses: [],
      loading: true,
      allocationChart: null,
      expensesChart: null
    };
  },
  computed: {
    totalSpent() {
      return this.budgetCategories.reduce((sum, category) => sum + parseFloat(category.montant_depense || 0), 0);
    },
    budgetProgress() {
      return this.budget.montant_total > 0 
        ? Math.round((this.totalSpent / this.budget.montant_total) * 100) 
        : 0;
    },
    timeProgress() {
      const startDate = new Date(this.budget.date_debut);
      const endDate = new Date(this.budget.date_fin);
      const today = new Date();
      
      if (today < startDate) return 0;
      if (today > endDate) return 100;
      
      const totalDuration = endDate - startDate;
      const elapsedDuration = today - startDate;
      
      return Math.round((elapsedDuration / totalDuration) * 100);
    }
  },
  methods: {
    fetchBudgetData() {
      this.loading = true;
      
      axios.get(`/api/user/budgets/${this.$route.params.id}`)
        .then(response => {
          this.budget = response.data.data;
          this.fetchBudgetDetails();
        })
        .catch(error => {
          console.error('Erreur lors du chargement du budget:', error);
          this.$toasted.error('Impossible de charger les détails du budget.');
          this.loading = false;
          this.$router.push({ name: 'user.budgets.index' });
        });
    },
    fetchBudgetDetails() {
      const budgetId = this.budget.id;
      
      // Charger les revenus du budget
      axios.get(`/api/user/budgets/${budgetId}/incomes`)
        .then(response => {
          this.budgetIncomes = response.data;
          
          // Charger les catégories du budget
          return axios.get(`/api/user/budgets/${budgetId}/categories`);
        })
        .then(response => {
          this.budgetCategories = response.data;
          
          // Charger les dépenses récentes
          return axios.get(`/api/user/budgets/${budgetId}/expenses`, {
            params: { limit: 5 }
          });
        })
        .then(response => {
          this.recentExpenses = response.data.data;
          this.loading = false;
          
          // Rendre les graphiques
          this.$nextTick(() => {
            this.renderAllocationChart();
            this.renderExpensesChart();
          });
        })
        .catch(error => {
          console.error('Erreur lors du chargement des détails du budget:', error);
          this.loading = false;
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
    getCategoryProgressClass(percentage) {
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
    renderAllocationChart() {
      const ctx = this.$refs.allocationChart.getContext('2d');
      
      // Préparer les données
      const labels = this.budgetCategories.map(category => category.nom);
      const data = this.budgetCategories.map(category => category.montant_alloue);
      const colors = this.budgetCategories.map(category => category.couleur);
      
      // Détruire le graphique existant s'il y en a un
      if (this.allocationChart) {
        this.allocationChart.destroy();
      }
      
      this.allocationChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: colors,
            hoverOffset: 4,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
              labels: {
                boxWidth: 12,
                font: {
                  size: 10
                }
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.label || '';
                  if (label) {
                    label += ': ';
                  }
                  label += new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD'
                  }).format(context.raw);
                  return label;
                }
              }
            }
          }
        }
      });
    },
    renderExpensesChart() {
      const ctx = this.$refs.expensesChart.getContext('2d');
      
      // Préparer les données
      const labels = this.budgetCategories.map(category => category.nom);
      const allocatedData = this.budgetCategories.map(category => category.montant_alloue);
      const spentData = this.budgetCategories.map(category => category.montant_depense);
      const colors = this.budgetCategories.map(category => category.couleur);
      
      // Détruire le graphique existant s'il y en a un
      if (this.expensesChart) {
        this.expensesChart.destroy();
      }
      
      this.expensesChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Montant alloué',
              data: allocatedData,
              backgroundColor: colors.map(color => `${color}80`), // Ajouter transparence
              borderColor: colors,
              borderWidth: 1
            },
            {
              label: 'Dépenses',
              data: spentData,
              backgroundColor: colors,
              borderColor: colors.map(color => this.darkenColor(color, 20)),
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD',
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                  }).format(value);
                }
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.dataset.label || '';
                  if (label) {
                    label += ': ';
                  }
                  label += new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD'
                  }).format(context.raw);
                  return label;
                }
              }
            }
          }
        }
      });
    },
    darkenColor(hex, percent) {
      // Fonction pour assombrir une couleur
      hex = hex.replace('#', '');
      const r = parseInt(hex.substr(0, 2), 16);
      const g = parseInt(hex.substr(2, 2), 16);
      const b = parseInt(hex.substr(4, 2), 16);
      
      const darken = (color, percent) => {
        return Math.floor(color * (1 - percent / 100));
      };
      
      const rDark = darken(r, percent);
      const gDark = darken(g, percent);
      const bDark = darken(b, percent);
      
      return `#${rDark.toString(16).padStart(2, '0')}${gDark.toString(16).padStart(2, '0')}${bDark.toString(16).padStart(2, '0')}`;
    }
  },
  mounted() {
    this.fetchBudgetData();
  },
  beforeUnmount() {
    if (this.allocationChart) {
      this.allocationChart.destroy();
    }
    
    if (this.expensesChart) {
      this.expensesChart.destroy();
    }
  },
  metaInfo() {
    return {
      title: `${this.budget.nom || 'Budget'} - Budget Manager`
    };
  }
};
</script>

<style scoped>
.border-left-primary {
  border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
  border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
  border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
  border-left: 0.25rem solid #f6c23e !important;
}
</style>