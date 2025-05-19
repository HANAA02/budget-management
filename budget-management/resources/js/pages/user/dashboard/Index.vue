<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Tableau de bord</h1>
      <div>
        <router-link :to="{ name: 'user.budgets.create' }" class="btn btn-primary">
          <i class="fas fa-plus me-1"></i> Nouveau budget
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <div v-else>
      <!-- Résumé financier -->
      <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
          <div class="card border-left-primary shadow h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                    Solde total
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ formatAmount(dashboardData.solde_total) }}
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-wallet fa-2x text-gray-300"></i>
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
                    Revenus du mois
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ formatAmount(dashboardData.revenus_mois) }}
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
                    Dépenses du mois
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ formatAmount(dashboardData.depenses_mois) }}
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
          <div class="card border-left-warning shadow h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                    Budget restant
                  </div>
                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                    {{ formatAmount(dashboardData.budget_restant) }}
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Budget courant et dépenses -->
      <div class="row mb-4">
        <div class="col-lg-8">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Budget du mois</h6>
              <div v-if="currentBudget">
                <router-link 
                  :to="{ name: 'user.budgets.show', params: { id: currentBudget.id } }" 
                  class="btn btn-sm btn-outline-primary"
                >
                  Voir les détails
                </router-link>
              </div>
            </div>
            <div class="card-body">
              <div v-if="!currentBudget" class="alert alert-info">
                <p class="mb-0">
                  <i class="fas fa-info-circle me-2"></i>
                  Vous n'avez pas encore créé de budget pour ce mois-ci.
                </p>
                <div class="mt-3">
                  <router-link :to="{ name: 'user.budgets.create' }" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Créer un budget
                  </router-link>
                </div>
              </div>
              <div v-else>
                <div class="mb-4">
                  <h5>
                    {{ currentBudget.nom }}
                    <span class="badge bg-secondary ms-2">
                      {{ formatDate(currentBudget.date_debut) }} - {{ formatDate(currentBudget.date_fin) }}
                    </span>
                  </h5>
                  <div class="progress" style="height: 25px;">
                    <div 
                      class="progress-bar" 
                      role="progressbar" 
                      :style="{ width: `${currentBudgetProgress}%` }"
                      :class="getBudgetProgressClass(currentBudgetProgress)"
                      aria-valuemin="0" 
                      aria-valuemax="100"
                    >
                      {{ currentBudgetProgress }}% utilisé
                    </div>
                  </div>
                </div>

                <div class="table-responsive">
                  <table class="table table-sm">
                    <thead>
                      <tr>
                        <th>Catégorie</th>
                        <th>Alloué</th>
                        <th>Dépensé</th>
                        <th>Restant</th>
                        <th>Progression</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="category in currentBudgetCategories" :key="category.id">
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
                        <td style="width: 25%;">
                          <div class="progress" style="height: 10px;">
                            <div 
                              class="progress-bar" 
                              role="progressbar" 
                              :style="{ width: `${category.pourcentage_utilisation}%` }"
                              :class="getBudgetProgressClass(category.pourcentage_utilisation)"
                              aria-valuemin="0" 
                              aria-valuemax="100"
                            >
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Répartition des dépenses</h6>
            </div>
            <div class="card-body">
              <div v-if="!hasExpenses" class="text-center py-5">
                <div class="mb-3">
                  <i class="fas fa-receipt fa-3x text-gray-300"></i>
                </div>
                <p>Aucune dépense enregistrée pour ce mois-ci.</p>
                <router-link :to="{ name: 'user.expenses.create' }" class="btn btn-sm btn-primary">
                  <i class="fas fa-plus me-1"></i> Ajouter une dépense
                </router-link>
              </div>
              <div v-else class="chart-container" style="height: 250px;">
                <canvas ref="expensesChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Transactions récentes et objectifs -->
      <div class="row">
        <div class="col-lg-6">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Dépenses récentes</h6>
              <router-link :to="{ name: 'user.expenses.index' }" class="btn btn-sm btn-outline-primary">
                Voir tout
              </router-link>
            </div>
            <div class="card-body">
              <div v-if="recentExpenses.length === 0" class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Aucune dépense récente.
              </div>
              <div v-else class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Description</th>
                      <th>Catégorie</th>
                      <th>Montant</th>
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
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
              <h6 class="m-0 font-weight-bold text-primary">Objectifs</h6>
              <router-link :to="{ name: 'user.goals.index' }" class="btn btn-sm btn-outline-primary">
                Voir tout
              </router-link>
            </div>
            <div class="card-body">
              <div v-if="goals.length === 0" class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Vous n'avez défini aucun objectif financier.
                <div class="mt-2">
                  <router-link :to="{ name: 'user.goals.create' }" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Créer un objectif
                  </router-link>
                </div>
              </div>
              <div v-else>
                <div v-for="goal in goals" :key="goal.id" class="goal-item mb-3">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="mb-0">{{ goal.titre }}</h6>
                    <span class="badge" :class="getGoalStatusBadgeClass(goal.statut)">
                      {{ goal.statut }}
                    </span>
                  </div>
                  <div class="progress mb-1" style="height: 15px;">
                    <div 
                      class="progress-bar" 
                      role="progressbar" 
                      :style="{ width: `${goal.progression}%` }"
                      :class="getGoalProgressClass(goal.progression)"
                      aria-valuemin="0" 
                      aria-valuemax="100"
                    >
                      {{ goal.progression }}%
                    </div>
                  </div>
                  <div class="d-flex justify-content-between">
                    <small>{{ formatAmount(goal.montant_actuel) }} / {{ formatAmount(goal.montant_cible) }}</small>
                    <small>Échéance: {{ formatDate(goal.date_fin) }}</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Alertes -->
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
          <h6 class="m-0 font-weight-bold text-primary">Alertes</h6>
          <router-link :to="{ name: 'user.alerts.index' }" class="btn btn-sm btn-outline-primary">
            Gérer les alertes
          </router-link>
        </div>
        <div class="card-body">
          <div v-if="alerts.length === 0" class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            Aucune alerte active pour le moment.
          </div>
          <div v-else>
            <div v-for="(alert, index) in alerts" :key="index" 
                 class="alert" 
                 :class="getAlertClass(alert.type)">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <i :class="getAlertIcon(alert.type) + ' me-2'"></i>
                  {{ alert.message }}
                </div>
                <button 
                  class="btn btn-sm btn-outline-secondary" 
                  @click="dismissAlert(alert.id)"
                  title="Ignorer cette alerte"
                >
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';

export default {
  name: 'UserDashboard',
  data() {
    return {
      loading: true,
      dashboardData: {
        solde_total: 0,
        revenus_mois: 0,
        depenses_mois: 0,
        budget_restant: 0
      },
      currentBudget: null,
      currentBudgetCategories: [],
      currentBudgetProgress: 0,
      recentExpenses: [],
      goals: [],
      alerts: [],
      expensesChart: null,
      hasExpenses: false
    };
  },
  methods: {
    fetchDashboardData() {
      this.loading = true;
      axios.get('/api/user/dashboard')
        .then(response => {
          this.dashboardData = response.data.financial_summary;
          this.currentBudget = response.data.current_budget;
          this.currentBudgetCategories = response.data.budget_categories || [];
          this.currentBudgetProgress = response.data.budget_progress || 0;
          this.recentExpenses = response.data.recent_expenses || [];
          this.goals = response.data.goals || [];
          this.alerts = response.data.alerts || [];
          this.hasExpenses = this.recentExpenses.length > 0;
          this.loading = false;
          
          if (this.hasExpenses) {
            this.$nextTick(() => {
              this.renderExpensesChart(response.data.expenses_by_category);
            });
          }
        })
        .catch(error => {
          console.error('Erreur lors du chargement des données du tableau de bord:', error);
          this.$toasted.error('Impossible de charger les données du tableau de bord.');
          this.loading = false;
        });
    },
    renderExpensesChart(data) {
      const ctx = this.$refs.expensesChart.getContext('2d');
      
      // Détruire le graphique existant s'il y en a un
      if (this.expensesChart) {
        this.expensesChart.destroy();
      }
      
      this.expensesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: data.labels,
          datasets: [{
            data: data.values,
            backgroundColor: data.colors,
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
    formatAmount(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount || 0);
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
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
    getBudgetProgressClass(percentage) {
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
    getGoalProgressClass(percentage) {
      if (percentage >= 100) {
        return 'bg-success';
      } else if (percentage >= 75) {
        return 'bg-info';
      } else if (percentage >= 50) {
        return 'bg-primary';
      } else if (percentage >= 25) {
        return 'bg-warning';
      } else {
        return 'bg-danger';
      }
    },
    getGoalStatusBadgeClass(status) {
      switch (status) {
        case 'en cours':
          return 'bg-primary';
        case 'atteint':
          return 'bg-success';
        case 'abandonné':
          return 'bg-danger';
        default:
          return 'bg-secondary';
      }
    },
    getAlertClass(type) {
      switch (type) {
        case 'danger':
          return 'alert-danger';
        case 'warning':
          return 'alert-warning';
        case 'info':
          return 'alert-info';
        default:
          return 'alert-secondary';
      }
    },
    getAlertIcon(type) {
      switch (type) {
        case 'danger':
          return 'fas fa-exclamation-circle';
        case 'warning':
          return 'fas fa-exclamation-triangle';
        case 'info':
          return 'fas fa-info-circle';
        default:
          return 'fas fa-bell';
      }
    },
    dismissAlert(alertId) {
      axios.post(`/api/user/alerts/${alertId}/dismiss`)
        .then(() => {
          this.alerts = this.alerts.filter(alert => alert.id !== alertId);
          this.$toasted.success('Alerte ignorée.');
        })
        .catch(error => {
          console.error('Erreur lors de la suppression de l\'alerte:', error);
          this.$toasted.error('Impossible d\'ignorer cette alerte.');
        });
    }
  },
  mounted() {
    this.fetchDashboardData();
  },
  beforeUnmount() {
    if (this.expensesChart) {
      this.expensesChart.destroy();
    }
  },
  metaInfo() {
    return {
      title: 'Tableau de bord - Budget Manager'
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

.goal-item {
  padding: 10px;
  border-radius: 5px;
  background-color: #f8f9fc;
  transition: all 0.2s;
}

.goal-item:hover {
  box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}
</style>