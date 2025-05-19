<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Détails de la catégorie</h1>
      <div>
        <router-link :to="{ name: 'admin.categories.edit', params: { id: $route.params.id } }" class="btn btn-primary me-2">
          <i class="fas fa-edit me-1"></i> Modifier
        </router-link>
        <router-link :to="{ name: 'admin.categories.index' }" class="btn btn-secondary">
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
      <div class="card">
        <div class="card-header" :style="{ backgroundColor: category.couleur, color: getContrastColor(category.couleur) }">
          <div class="d-flex align-items-center">
            <i :class="category.icone || 'fas fa-folder'" class="fa-2x me-3"></i>
            <h3 class="mb-0">{{ category.nom }}</h3>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <h5>Informations générales</h5>
              <table class="table table-borderless">
                <tbody>
                  <tr>
                    <th style="width: 40%">ID</th>
                    <td>{{ category.id }}</td>
                  </tr>
                  <tr>
                    <th>Pourcentage par défaut</th>
                    <td>{{ category.pourcentage_defaut }}%</td>
                  </tr>
                  <tr>
                    <th>Statut</th>
                    <td>
                      <span 
                        class="badge" 
                        :class="category.active ? 'bg-success' : 'bg-danger'"
                      >
                        {{ category.active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th>Couleur</th>
                    <td>
                      <div class="d-flex align-items-center">
                        <div 
                          class="color-sample me-2" 
                          :style="{ backgroundColor: category.couleur }"
                        ></div>
                        {{ category.couleur }}
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <th>Icône</th>
                    <td>
                      <i :class="category.icone || 'fas fa-folder'" class="me-2"></i>
                      {{ category.icone || 'Non définie' }}
                    </td>
                  </tr>
                  <tr>
                    <th>Date de création</th>
                    <td>{{ formatDate(category.created_at) }}</td>
                  </tr>
                  <tr>
                    <th>Dernière modification</th>
                    <td>{{ formatDate(category.updated_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="col-md-6">
              <h5>Description</h5>
              <div class="p-3 bg-light rounded">
                {{ category.description || 'Aucune description disponible.' }}
              </div>

              <h5 class="mt-4">Statistiques d'utilisation</h5>
              <div class="row">
                <div class="col-md-4">
                  <div class="card text-center">
                    <div class="card-body">
                      <h6 class="card-title">Budgets</h6>
                      <p class="h3">{{ categoryStats.budgets_count }}</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card text-center">
                    <div class="card-body">
                      <h6 class="card-title">Dépenses</h6>
                      <p class="h3">{{ categoryStats.expenses_count }}</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card text-center">
                    <div class="card-body">
                      <h6 class="card-title">Montant moyen</h6>
                      <p class="h5">{{ formatAmount(categoryStats.avg_amount) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Graphique d'utilisation de la catégorie -->
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="mb-0">Utilisation par mois</h5>
        </div>
        <div class="card-body">
          <div class="chart-container" style="height: 300px;">
            <canvas ref="monthlyUsageChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Dernières dépenses dans cette catégorie -->
      <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Dernières dépenses</h5>
          <button 
            class="btn btn-sm btn-outline-primary"
            @click="loadMoreExpenses"
            v-if="hasMoreExpenses"
          >
            Voir plus
          </button>
        </div>
        <div class="card-body">
          <div v-if="recentExpenses.length === 0" class="alert alert-info">
            Aucune dépense n'a été enregistrée pour cette catégorie.
          </div>
          <div v-else class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Description</th>
                  <th>Montant</th>
                  <th>Budget</th>
                  <th>Utilisateur</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="expense in recentExpenses" :key="expense.id">
                  <td>{{ formatDate(expense.date_depense) }}</td>
                  <td>{{ expense.description }}</td>
                  <td>{{ formatAmount(expense.montant) }}</td>
                  <td>{{ expense.budget_name }}</td>
                  <td>{{ expense.user_name }}</td>
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
  name: 'AdminCategoryShow',
  data() {
    return {
      category: {},
      categoryStats: {
        budgets_count: 0,
        expenses_count: 0,
        avg_amount: 0
      },
      recentExpenses: [],
      loading: true,
      monthlyUsageChart: null,
      expensesPage: 1,
      hasMoreExpenses: false
    };
  },
  methods: {
    fetchCategory() {
      this.loading = true;
      axios.get(`/api/admin/categories/${this.$route.params.id}`)
        .then(response => {
          this.category = response.data.data;
          this.fetchCategoryStats();
        })
        .catch(error => {
          console.error('Erreur lors du chargement de la catégorie:', error);
          this.$toasted.error('Impossible de charger les détails de la catégorie.');
          this.loading = false;
        });
    },
    fetchCategoryStats() {
      axios.get(`/api/admin/categories/${this.$route.params.id}/stats`)
        .then(response => {
          this.categoryStats = response.data;
          this.fetchMonthlyUsage();
        })
        .catch(error => {
          console.error('Erreur lors du chargement des statistiques:', error);
          this.fetchMonthlyUsage();
        });
    },
    fetchMonthlyUsage() {
      axios.get(`/api/admin/categories/${this.$route.params.id}/monthly-usage`)
        .then(response => {
          this.renderMonthlyUsageChart(response.data);
          this.fetchRecentExpenses();
        })
        .catch(error => {
          console.error('Erreur lors du chargement des données mensuelles:', error);
          this.fetchRecentExpenses();
        });
    },
    fetchRecentExpenses() {
      axios.get(`/api/admin/categories/${this.$route.params.id}/expenses`, {
        params: { page: this.expensesPage, limit:.10 }
      })
        .then(response => {
          this.recentExpenses = this.expensesPage === 1 
            ? response.data.data 
            : [...this.recentExpenses, ...response.data.data];
          
          this.hasMoreExpenses = response.data.meta.current_page < response.data.meta.last_page;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des dépenses récentes:', error);
          this.loading = false;
        });
    },
    loadMoreExpenses() {
      this.expensesPage++;
      this.fetchRecentExpenses();
    },
    renderMonthlyUsageChart(data) {
      const ctx = this.$refs.monthlyUsageChart.getContext('2d');
      
      // Détruire le graphique existant s'il y en a un
      if (this.monthlyUsageChart) {
        this.monthlyUsageChart.destroy();
      }
      
      this.monthlyUsageChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: data.labels,
          datasets: [
            {
              label: 'Montant budgétisé (MAD)',
              data: data.budgeted,
              backgroundColor: 'rgba(54, 162, 235, 0.5)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1
            },
            {
              label: 'Dépenses réelles (MAD)',
              data: data.spent,
              backgroundColor: 'rgba(255, 99, 132, 0.5)',
              borderColor: 'rgba(255, 99, 132, 1)',
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
                  return value.toLocaleString('fr-MA') + ' MAD';
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
    }
  },
  created() {
    this.fetchCategory();
  },
  metaInfo() {
    return {
      title: `Détails de la catégorie - Administration`
    };
  }
};
</script>

<style scoped>
.color-sample {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: inline-block;
}
</style>