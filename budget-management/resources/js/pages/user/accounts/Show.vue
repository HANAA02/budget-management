<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>{{ account.nom }}</h1>
      <div>
        <router-link 
          :to="{ name: 'user.incomes.create', query: { account_id: account.id } }" 
          class="btn btn-success me-2"
        >
          <i class="fas fa-plus me-1"></i> Ajouter un revenu
        </router-link>
        <router-link :to="{ name: 'user.accounts.edit', params: { id: account.id } }" class="btn btn-primary me-2">
          <i class="fas fa-edit me-1"></i> Modifier
        </router-link>
        <router-link :to="{ name: 'user.accounts.index' }" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-1"></i> Retour
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <div v-else>
      <!-- Résumé du compte -->
      <div class="row mb-4">
        <div class="col-lg-4">
          <div 
            class="card border-left-primary shadow h-100" 
            :class="{
              'border-left-success': account.solde > 0, 
              'border-left-danger': account.solde < 0, 
              'border-left-warning': account.solde === 0
            }"
          >
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                    Solde actuel
                  </div>
                  <div 
                    class="h3 mb-0 font-weight-bold" 
                    :class="{
                      'text-success': account.solde > 0, 
                      'text-danger': account.solde < 0, 
                      'text-warning': account.solde === 0
                    }"
                  >
                    {{ formatAmount(account.solde) }}
                  </div>
                </div>
                <div class="col-auto">
                  <i :class="getAccountIcon(account.type) + ' fa-2x text-gray-300'"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-left-success shadow h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                    Revenus du mois
                  </div>
                  <div class="h4 mb-0 font-weight-bold text-success">
                    {{ formatAmount(monthlyIncomes) }}
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-left-danger shadow h-100">
            <div class="card-body">
              <div class="row no-gutters align-items-center">
                <div class="col mr-2">
                  <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                    Dépenses du mois
                  </div>
                  <div class="h4 mb-0 font-weight-bold text-danger">
                    {{ formatAmount(monthlyExpenses) }}
                  </div>
                </div>
                <div class="col-auto">
                  <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Détails du compte -->
      <div class="row mb-4">
        <div class="col-lg-5">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Détails du compte</h6>
            </div>
            <div class="card-body">
              <table class="table table-borderless">
                <tbody>
                  <tr>
                    <th style="width: 40%">Type</th>
                    <td>{{ getAccountTypeName(account.type) }}</td>
                  </tr>
                  <tr>
                    <th>Devise</th>
                    <td>{{ account.devise }}</td>
                  </tr>
                  <tr>
                    <th>Statut</th>
                    <td>
                      <span 
                        class="badge" 
                        :class="account.est_actif ? 'bg-success' : 'bg-danger'"
                      >
                        {{ account.est_actif ? 'Actif' : 'Inactif' }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th>Inclus dans le total</th>
                    <td>
                      <span 
                        class="badge" 
                        :class="account.inclure_dans_total ? 'bg-success' : 'bg-secondary'"
                      >
                        {{ account.inclure_dans_total ? 'Oui' : 'Non' }}
                      </span>
                    </td>
                  </tr>
                  <tr>
                    <th>Date de création</th>
                    <td>{{ formatDate(account.date_creation) }}</td>
                  </tr>
                  <tr v-if="account.description">
                    <th>Description</th>
                    <td>{{ account.description }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Évolution du solde</h6>
            </div>
            <div class="card-body">
              <div class="chart-container" style="height: 250px;">
                <canvas ref="balanceChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Transactions récentes -->
      <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
          <h6 class="m-0 font-weight-bold text-primary">Transactions récentes</h6>
          <div>
            <select v-model="transactionType" class="form-select form-select-sm" style="width: auto; display: inline-block;">
              <option value="all">Toutes les transactions</option>
              <option value="income">Revenus</option>
              <option value="expense">Dépenses</option>
            </select>
          </div>
        </div>
        <div class="card-body">
          <div v-if="loadingTransactions" class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Chargement...</span>
            </div>
          </div>
          <div v-else-if="filteredTransactions.length === 0" class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Aucune transaction trouvée pour ce compte.
          </div>
          <div v-else class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Description</th>
                  <th>Catégorie</th>
                  <th>Montant</th>
                  <th>Solde après</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="transaction in filteredTransactions" :key="transaction.id">
                  <td>{{ formatDate(transaction.date) }}</td>
                  <td>{{ transaction.description }}</td>
                  <td>
                    <span 
                      class="badge rounded-pill" 
                      :style="getTransactionCategoryStyle(transaction)"
                    >
                      {{ transaction.categorie }}
                    </span>
                  </td>
                  <td 
                    :class="transaction.type === 'income' ? 'text-success' : 'text-danger'"
                  >
                    {{ transaction.type === 'income' ? '+' : '-' }} {{ formatAmount(Math.abs(transaction.montant)) }}
                  </td>
                  <td>{{ formatAmount(transaction.solde_apres) }}</td>
                </tr>
              </tbody>
            </table>
            <div class="text-center mt-3" v-if="hasMoreTransactions">
              <button class="btn btn-outline-primary" @click="loadMoreTransactions">
                Charger plus de transactions
              </button>
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
  name: 'UserAccountShow',
  data() {
    return {
      account: {
        id: null,
        nom: '',
        type: '',
        solde: 0,
        devise: 'MAD',
        description: '',
        inclure_dans_total: true,
        est_actif: true,
        date_creation: null
      },
      transactions: [],
      loading: true,
      loadingTransactions: true,
      transactionType: 'all',
      monthlyIncomes: 0,
      monthlyExpenses: 0,
      balanceChart: null,
      transactionPage: 1,
      hasMoreTransactions: false
    };
  },
  computed: {
    filteredTransactions() {
      if (this.transactionType === 'all') {
        return this.transactions;
      } else {
        return this.transactions.filter(transaction => transaction.type === this.transactionType);
      }
    }
  },
  methods: {
    fetchAccount() {
      this.loading = true;
      axios.get(`/api/user/accounts/${this.$route.params.id}`)
        .then(response => {
          this.account = response.data.data;
          this.loading = false;
          
          // Charger les statistiques mensuelles
          return axios.get(`/api/user/accounts/${this.account.id}/stats`);
        })
        .then(response => {
          this.monthlyIncomes = response.data.monthly_incomes;
          this.monthlyExpenses = response.data.monthly_expenses;
          
          // Charger les données pour le graphique
          return axios.get(`/api/user/accounts/${this.account.id}/balance-history`);
        })
        .then(response => {
          this.$nextTick(() => {
            this.renderBalanceChart(response.data);
          });
          
          // Charger les transactions
          this.fetchTransactions();
        })
        .catch(error => {
          console.error('Erreur lors du chargement du compte:', error);
          this.$toasted.error('Impossible de charger les détails du compte.');
          this.loading = false;
          this.$router.push({ name: 'user.accounts.index' });
        });
    },
    fetchTransactions() {
      this.loadingTransactions = true;
      
      axios.get(`/api/user/accounts/${this.account.id}/transactions`, {
        params: { page: this.transactionPage, limit: 10 }
      })
        .then(response => {
          this.transactions = this.transactionPage === 1 
            ? response.data.data 
            : [...this.transactions, ...response.data.data];
          
          this.hasMoreTransactions = response.data.meta.current_page < response.data.meta.last_page;
          this.loadingTransactions = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des transactions:', error);
          this.loadingTransactions = false;
        });
    },
    loadMoreTransactions() {
      this.transactionPage++;
      this.fetchTransactions();
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatAmount(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: this.account.devise
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
    getTransactionCategoryStyle(transaction) {
      // Pour les revenus, utiliser une couleur verte
      if (transaction.type === 'income') {
        return { 
          backgroundColor: '#1cc88a',
          color: '#fff'
        };
      }
      
      // Pour les dépenses, utiliser la couleur de la catégorie
      return { 
        backgroundColor: transaction.categorie_couleur || '#4e73df',
        color: this.getContrastColor(transaction.categorie_couleur || '#4e73df')
      };
    },
    getContrastColor(hexColor) {
      if (!hexColor) return '#ffffff';
      
      // Convertir la couleur hex en RGB
      const r = parseInt(hexColor.substr(1, 2), 16);
      const g = parseInt(hexColor.substr(3, 2), 16);
      const b = parseInt(hexColor.substr(5, 2), 16);
      
      // Calculer la luminosité (formule de perception)
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
      
      // Retourner du texte blanc ou noir en fonction de la luminosité
      return luminance > 0.5 ? '#000000' : '#ffffff';
    },
    renderBalanceChart(data) {
      if (!this.$refs.balanceChart) return;
      
      const ctx = this.$refs.balanceChart.getContext('2d');
      
      // Détruire le graphique existant s'il y en a un
      if (this.balanceChart) {
        this.balanceChart.destroy();
      }
      
      this.balanceChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: [{
            label: 'Solde',
            data: data.values,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            borderWidth: 2,
            pointBackgroundColor: function(context) {
              const value = context.dataset.data[context.dataIndex];
              return value >= 0 ? '#1cc88a' : '#e74a3b';
            },
            pointBorderColor: function(context) {
              const value = context.dataset.data[context.dataIndex];
              return value >= 0 ? '#1cc88a' : '#e74a3b';
            },
            pointHoverRadius: 5,
            pointHoverBackgroundColor: function(context) {
              const value = context.dataset.data[context.dataIndex];
              return value >= 0 ? '#1cc88a' : '#e74a3b';
            },
            pointHoverBorderColor: '#fff',
            fill: true,
            tension: 0.1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              ticks: {
                callback: function(value) {
                  return new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD',
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
                  return 'Solde: ' + new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD'
                  }).format(context.raw);
                }
              }
            }
          }
        }
      });
    }
  },
  mounted() {
    this.fetchAccount();
  },
  beforeUnmount() {
    if (this.balanceChart) {
      this.balanceChart.destroy();
    }
  },
  watch: {
    transactionType() {
      // Reset pagination when filter changes
      this.transactionPage = 1;
      this.transactions = [];
      this.fetchTransactions();
    }
  },
  metaInfo() {
    return {
      title: `${this.account.nom || 'Compte'} - Budget Manager`
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