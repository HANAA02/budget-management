<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Mes comptes</h1>
      <router-link :to="{ name: 'user.accounts.create' }" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Ajouter un compte
      </router-link>
    </div>

    <div class="row mb-4">
      <div class="col-md-4 mb-4">
        <div class="card border-left-primary shadow h-100">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Solde total
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ formatAmount(totalBalance) }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-wallet fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card border-left-success shadow h-100">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                  Épargne totale
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ formatAmount(savingsTotal) }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card border-left-danger shadow h-100">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                  Dette totale
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">
                  {{ formatAmount(debtTotal) }}
                </div>
              </div>
              <div class="col-auto">
                <i class="fas fa-credit-card fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Liste des comptes</h6>
        <div class="dropdown no-arrow">
          <button 
            class="btn btn-link btn-sm dropdown-toggle" 
            type="button"
            id="accountsFilterDropdown" 
            data-bs-toggle="dropdown" 
            aria-expanded="false"
          >
            {{ activeFilter === 'all' ? 'Tous les comptes' : activeFilter === 'active' ? 'Comptes actifs' : 'Comptes inactifs' }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountsFilterDropdown">
            <li><a class="dropdown-item" href="#" @click.prevent="activeFilter = 'all'">Tous les comptes</a></li>
            <li><a class="dropdown-item" href="#" @click.prevent="activeFilter = 'active'">Comptes actifs</a></li>
            <li><a class="dropdown-item" href="#" @click.prevent="activeFilter = 'inactive'">Comptes inactifs</a></li>
          </ul>
        </div>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des comptes...</p>
        </div>

        <div v-else-if="filteredAccounts.length === 0" class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Aucun compte trouvé{{ activeFilter !== 'all' ? ' pour ce filtre.' : '.' }} 
          <router-link :to="{ name: 'user.accounts.create' }" class="btn btn-sm btn-primary mt-2">
            <i class="fas fa-plus me-1"></i> Ajouter un compte
          </router-link>
        </div>

        <div v-else>
          <div class="row">
            <div 
              v-for="account in filteredAccounts" 
              :key="account.id" 
              class="col-xl-4 col-md-6 mb-4"
            >
              <div 
                class="card" 
                :class="{'border-left-success': account.solde > 0, 'border-left-danger': account.solde < 0, 'border-left-warning': account.solde === 0}"
              >
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                  <div>
                    <i 
                      :class="getAccountIcon(account.type)" 
                      class="me-2"
                    ></i>
                    <span class="font-weight-bold">{{ account.nom }}</span>
                  </div>
                  <span 
                    class="badge" 
                    :class="account.est_actif ? 'bg-success' : 'bg-danger'"
                  >
                    {{ account.est_actif ? 'Actif' : 'Inactif' }}
                  </span>
                </div>
                <div class="card-body py-3">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Solde</span>
                    <span 
                      class="font-weight-bold" 
                      :class="{'text-success': account.solde > 0, 'text-danger': account.solde < 0}"
                    >
                      {{ formatAmount(account.solde, account.devise) }}
                    </span>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span class="text-muted">Type</span>
                    <span>{{ getAccountTypeName(account.type) }}</span>
                  </div>
                </div>
                <div class="card-footer py-2">
                  <div class="d-flex justify-content-between">
                    <router-link 
                      :to="{ name: 'user.accounts.show', params: { id: account.id } }" 
                      class="btn btn-sm btn-info"
                    >
                      <i class="fas fa-eye"></i>
                    </router-link>
                    <router-link 
                      :to="{ name: 'user.accounts.edit', params: { id: account.id } }" 
                      class="btn btn-sm btn-primary"
                    >
                      <i class="fas fa-edit"></i>
                    </router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Répartition des comptes</h6>
      </div>
      <div class="card-body">
        <div class="chart-container" style="height: 300px;">
          <canvas ref="accountsChart"></canvas>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Chart from 'chart.js/auto';

export default {
  name: 'UserAccountsIndex',
  data() {
    return {
      accounts: [],
      loading: true,
      activeFilter: 'all',
      accountsChart: null
    };
  },
  computed: {
    filteredAccounts() {
      if (this.activeFilter === 'all') {
        return this.accounts;
      } else if (this.activeFilter === 'active') {
        return this.accounts.filter(account => account.est_actif);
      } else {
        return this.accounts.filter(account => !account.est_actif);
      }
    },
    totalBalance() {
      return this.accounts
        .filter(account => account.inclure_dans_total && account.est_actif)
        .reduce((sum, account) => sum + parseFloat(account.solde), 0);
    },
    savingsTotal() {
      return this.accounts
        .filter(account => account.est_actif && account.type === 'epargne' && account.solde > 0)
        .reduce((sum, account) => sum + parseFloat(account.solde), 0);
    },
    debtTotal() {
      return Math.abs(this.accounts
        .filter(account => account.est_actif && account.solde < 0)
        .reduce((sum, account) => sum + parseFloat(account.solde), 0));
    }
  },
  methods: {
    fetchAccounts() {
      this.loading = true;
      axios.get('/api/user/accounts')
        .then(response => {
          this.accounts = response.data.data;
          this.loading = false;
          this.$nextTick(() => {
            this.renderAccountsChart();
          });
        })
        .catch(error => {
          console.error('Erreur lors du chargement des comptes:', error);
          this.$toasted.error('Impossible de charger les comptes.');
          this.loading = false;
        });
    },
    formatAmount(amount, currency = 'MAD') {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: currency
      }).format(amount || 0);
    },
    getAccountIcon(type) {
      switch (type) {
        case 'courant':
          return 'fas fa-university';
        case 'epargne':
          return 'fas fa-piggy-bank';
        case 'credit':
          return 'fas fa-credit-card';
        case 'liquide':
          return 'fas fa-money-bill-wave';
        default:
          return 'fas fa-wallet';
      }
    },
    getAccountTypeName(type) {
      switch (type) {
        case 'courant':
          return 'Compte courant';
        case 'epargne':
          return 'Compte d\'épargne';
        case 'credit':
          return 'Carte de crédit';
        case 'liquide':
          return 'Espèces';
        default:
          return 'Autre';
      }
    },
    renderAccountsChart() {
      if (!this.$refs.accountsChart) return;
      
      const ctx = this.$refs.accountsChart.getContext('2d');
      
      // Préparer les données
      const activeAccounts = this.accounts.filter(account => account.est_actif);
      const labels = activeAccounts.map(account => account.nom);
      const data = activeAccounts.map(account => Math.abs(account.solde));
      const colors = activeAccounts.map(account => {
        if (account.solde > 0) {
          return account.type === 'epargne' ? '#1cc88a' : '#4e73df';
        } else {
          return '#e74a3b';
        }
      });
      
      // Détruire le graphique existant s'il y en a un
      if (this.accountsChart) {
        this.accountsChart.destroy();
      }
      
      this.accountsChart = new Chart(ctx, {
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
                  
                  const account = activeAccounts[context.dataIndex];
                  
                  label += new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: account.devise
                  }).format(Math.abs(account.solde));
                  
                  if (account.solde < 0) {
                    label += ' (Dette)';
                  }
                  
                  return label;
                }
              }
            }
          }
        }
      });
    }
  },
  mounted() {
    this.fetchAccounts();
  },
  beforeUnmount() {
    if (this.accountsChart) {
      this.accountsChart.destroy();
    }
  },
  watch: {
    activeFilter() {
      this.$nextTick(() => {
        this.renderAccountsChart();
      });
    }
  },
  metaInfo() {
    return {
      title: 'Mes comptes - Budget Manager'
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

.border-left-warning {
  border-left: 0.25rem solid #f6c23e !important;
}

.border-left-danger {
  border-left: 0.25rem solid #e74a3b !important;
}
</style>