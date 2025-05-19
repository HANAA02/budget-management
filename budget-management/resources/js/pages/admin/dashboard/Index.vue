<template>
  <div>
    <h1 class="mb-4">Tableau de bord administrateur</h1>

    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Utilisateurs enregistrés
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ loading ? '...' : stats.users_count }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-users fa-2x text-gray-300"></i>
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
                  Budgets créés
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ loading ? '...' : stats.budgets_count }}
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
        <div class="card border-left-info shadow h-100">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                  Dépenses enregistrées
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ loading ? '...' : stats.expenses_count }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-receipt fa-2x text-gray-300"></i>
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
                  Montant total géré
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ loading ? '...' : formatAmount(stats.total_amount) }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Graphique: Utilisateurs inscrits par mois -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Inscriptions par mois</h6>
            <div class="dropdown no-arrow">
              <button 
                class="btn btn-link btn-sm dropdown-toggle" 
                type="button"
                id="usersTimeframeDropdown" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
              >
                {{ timeframes.users }}
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="usersTimeframeDropdown">
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="changeTimeframe('users', '6 mois')">
                    6 derniers mois
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="changeTimeframe('users', '1 an')">
                    12 derniers mois
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="changeTimeframe('users', 'Tout')">
                    Tout l'historique
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
              </div>
            </div>
            <div v-else class="chart-container" style="height: 300px;">
              <canvas ref="usersChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Graphique: Distribution des dépenses par catégorie -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Répartition des dépenses par catégorie</h6>
            <div class="dropdown no-arrow">
              <button 
                class="btn btn-link btn-sm dropdown-toggle" 
                type="button"
                id="expensesTimeframeDropdown" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
              >
                {{ timeframes.expenses }}
              </button>
              <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="expensesTimeframeDropdown">
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="changeTimeframe('expenses', 'Ce mois')">
                    Ce mois-ci
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="changeTimeframe('expenses', '3 mois')">
                    3 derniers mois
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="changeTimeframe('expenses', '1 an')">
                    12 derniers mois
                  </a>
                </li>
              </ul>
            </div>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-5">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
              </div>
            </div>
            <div v-else class="chart-container" style="height: 300px;">
              <canvas ref="expensesChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Derniers utilisateurs inscrits -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Derniers utilisateurs inscrits</h6>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-3">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
              </div>
            </div>
            <div v-else-if="recentUsers.length === 0" class="alert alert-info">
              Aucun utilisateur inscrit récemment.
            </div>
            <div v-else class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in recentUsers" :key="user.id">
                    <td>{{ user.prenom }} {{ user.nom }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ formatDate(user.date_creation) }}</td>
                    <td>
                      <router-link 
                        :to="{ name: 'admin.users.show', params: { id: user.id } }" 
                        class="btn btn-sm btn-info"
                      >
                        <i class="fas fa-eye"></i>
                      </router-link>
                    </td>
                  </tr>
                </tbody>
              </table>
              <div class="text-center mt-3">
                <router-link :to="{ name: 'admin.users.index' }" class="btn btn-outline-primary">
                  Voir tous les utilisateurs
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Activité récente -->
      <div class="col-lg-6 mb-4">
        <div class="card shadow">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Activité récente</h6>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-3">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
              </div>
            </div>
            <div v-else-if="recentActivity.length === 0" class="alert alert-info">
              Aucune activité récente.
            </div>
            <div v-else>
              <ul class="timeline">
                <li v-for="(activity, index) in recentActivity" :key="index" class="timeline-item">
                  <div class="timeline-badge" :class="getActivityBadgeClass(activity.type)">
                    <i :class="getActivityIcon(activity.type)"></i>
                  </div>
                  <div class="timeline-content">
                    <h6 class="timeline-title">{{ activity.message }}</h6>
                    <p class="timeline-text">
                      <small class="text-muted">{{ formatDateTime(activity.date) }}</small>
                    </p>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistiques système -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Performances du système</h6>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-3">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </div>
        <div v-else class="row">
          <div class="col-md-3 mb-3">
            <div class="card">
              <div class="card-body text-center">
                <h6 class="card-title">CPU</h6>
                <div class="progress mb-2">
                  <div 
                    class="progress-bar" 
                    role="progressbar" 
                    :style="{ width: `${systemStats.cpu_usage}%` }" 
                    :class="getProgressBarClass(systemStats.cpu_usage)"
                    :aria-valuenow="systemStats.cpu_usage" 
                    aria-valuemin="0" 
                    aria-valuemax="100"
                  >
                    {{ systemStats.cpu_usage }}%
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card">
              <div class="card-body text-center">
                <h6 class="card-title">Mémoire</h6>
                <div class="progress mb-2">
                  <div 
                    class="progress-bar" 
                    role="progressbar" 
                    :style="{ width: `${systemStats.memory_usage}%` }" 
                    :class="getProgressBarClass(systemStats.memory_usage)"
                    :aria-valuenow="systemStats.memory_usage" 
                    aria-valuemin="0" 
                    aria-valuemax="100"
                  >
                    {{ systemStats.memory_usage }}%
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card">
              <div class="card-body text-center">
                <h6 class="card-title">Stockage</h6>
                <div class="progress mb-2">
                  <div 
                    class="progress-bar" 
                    role="progressbar" 
                    :style="{ width: `${systemStats.disk_usage}%` }" 
                    :class="getProgressBarClass(systemStats.disk_usage)"
                    :aria-valuenow="systemStats.disk_usage" 
                    aria-valuemin="0" 
                    aria-valuemax="100"
                  >
                    {{ systemStats.disk_usage }}%
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3 mb-3">
            <div class="card">
              <div class="card-body text-center">
                <h6 class="card-title">Database</h6>
                <div class="progress mb-2">
                  <div 
                    class="progress-bar" 
                    role="progressbar" 
                    :style="{ width: `${systemStats.db_usage}%` }" 
                    :class="getProgressBarClass(systemStats.db_usage)"
                    :aria-valuenow="systemStats.db_usage" 
                    aria-valuemin="0" 
                    aria-valuemax="100"
                  >
                    {{ systemStats.db_usage }}%
                  </div>
                </div>
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
  name: 'AdminDashboard',
  data() {
    return {
      loading: true,
      stats: {
        users_count: 0,
        budgets_count: 0,
        expenses_count: 0,
        total_amount: 0
      },
      recentUsers: [],
      recentActivity: [],
      systemStats: {
        cpu_usage: 0,
        memory_usage: 0,
        disk_usage: 0,
        db_usage: 0
      },
      timeframes: {
        users: '6 mois',
        expenses: 'Ce mois'
      },
      usersChart: null,
      expensesChart: null,
      refreshInterval: null
    };
  },
  methods: {
    loadDashboardData() {
      this.loading = true;
      
      // Charger les statistiques générales
      axios.get('/api/admin/dashboard/stats')
        .then(response => {
          this.stats = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des statistiques:', error);
          this.$toasted.error('Impossible de charger les statistiques du tableau de bord.');
          this.loading = false;
        });
      
      // Charger les utilisateurs récents
      axios.get('/api/admin/dashboard/recent-users')
        .then(response => {
          this.recentUsers = response.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des utilisateurs récents:', error);
        });
      
      // Charger l'activité récente
      axios.get('/api/admin/dashboard/recent-activity')
        .then(response => {
          this.recentActivity = response.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement de l\'activité récente:', error);
        });
      
      // Charger les statistiques système
      axios.get('/api/admin/dashboard/system-stats')
        .then(response => {
          this.systemStats = response.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des statistiques système:', error);
        });
      
      // Charger les données pour le graphique d'inscriptions
      this.loadUsersChart();
      
      // Charger les données pour le graphique de dépenses
      this.loadExpensesChart();
    },
    loadUsersChart() {
      const period = this.timeframes.users === '6 mois' ? 'half-year' : 
                     this.timeframes.users === '1 an' ? 'year' : 'all';
      
      axios.get(`/api/admin/dashboard/users-chart?period=${period}`)
        .then(response => {
          this.renderUsersChart(response.data);
        })
        .catch(error => {
          console.error('Erreur lors du chargement des données du graphique d\'utilisateurs:', error);
        });
    },
    loadExpensesChart() {
      const period = this.timeframes.expenses === 'Ce mois' ? 'month' : 
                     this.timeframes.expenses === '3 mois' ? 'quarter' : 'year';
      
      axios.get(`/api/admin/dashboard/expenses-chart?period=${period}`)
        .then(response => {
          this.renderExpensesChart(response.data);
        })
        .catch(error => {
          console.error('Erreur lors du chargement des données du graphique de dépenses:', error);
        });
    },
    renderUsersChart(data) {
      const ctx = this.$refs.usersChart.getContext('2d');
      
      // Détruire le graphique existant s'il y en a un
      if (this.usersChart) {
        this.usersChart.destroy();
      }
      
      this.usersChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Nouveaux utilisateurs',
            data: data.values,
            borderColor: 'rgba(78, 115, 223, 1)',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
            pointBorderColor: '#fff',
            pointHoverRadius: 5,
            pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
            pointHoverBorderColor: '#fff',
            tension: 0.3,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                precision: 0
              }
            }
          },
          plugins: {
            legend: {
              display: false
            }
          }
        }
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
              position: 'right'
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
    changeTimeframe(chart, timeframe) {
      this.timeframes[chart] = timeframe;
      
      if (chart === 'users') {
        this.loadUsersChart();
      } else if (chart === 'expenses') {
        this.loadExpensesChart();
      }
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatDateTime(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { 
        day: '2-digit', 
        month: '2-digit', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
      };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatAmount(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount || 0);
    },
    getActivityBadgeClass(type) {
      switch (type) {
        case 'user':
          return 'bg-primary';
        case 'budget':
          return 'bg-success';
        case 'expense':
          return 'bg-info';
        case 'system':
          return 'bg-warning';
        default:
          return 'bg-secondary';
      }
    },
    getActivityIcon(type) {
      switch (type) {
        case 'user':
          return 'fas fa-user';
        case 'budget':
          return 'fas fa-wallet';
        case 'expense':
          return 'fas fa-receipt';
        case 'system':
          return 'fas fa-cog';
        default:
          return 'fas fa-info-circle';
      }
    },
    getProgressBarClass(value) {
      if (value < 50) {
        return 'bg-success';
      } else if (value < 75) {
        return 'bg-warning';
      } else {
        return 'bg-danger';
      }
    }
  },
  mounted() {
    this.loadDashboardData();
    
    // Actualiser les statistiques système toutes les 30 secondes
    this.refreshInterval = setInterval(() => {
      axios.get('/api/admin/dashboard/system-stats')
        .then(response => {
          this.systemStats = response.data;
        })
        .catch(error => {
          console.error('Erreur lors de l\'actualisation des statistiques système:', error);
        });
    }, 30000);
  },
  beforeUnmount() {
    if (this.refreshInterval) {
      clearInterval(this.refreshInterval);
    }
    
    if (this.usersChart) {
      this.usersChart.destroy();
    }
    
    if (this.expensesChart) {
      this.expensesChart.destroy();
    }
  },
  metaInfo() {
    return {
      title: 'Tableau de bord - Administration'
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

/* Timeline styling */
.timeline {
  position: relative;
  padding-left: 40px;
  margin-bottom: 50px;
  list-style-type: none;
}

.timeline:before {
  content: '';
  position: absolute;
  left: 15px;
  top: 0;
  height: 100%;
  width: 2px;
  background: #e9ecef;
}

.timeline-item {
  position: relative;
  margin-bottom: 30px;
}

.timeline-badge {
  position: absolute;
  left: -40px;
  width: 30px;
  height: 30px;
  text-align: center;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.timeline-content {
  padding: 0 15px;
}

.timeline-title {
  margin-bottom: 5px;
}

.timeline-text {
  margin-bottom: 0;
}
</style>