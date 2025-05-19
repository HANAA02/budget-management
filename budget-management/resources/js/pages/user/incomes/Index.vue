<template>
  <div class="incomes-index-container">
    <div class="header-section mb-6">
      <h1 class="text-2xl font-semibold">Mes Revenus</h1>
      <p class="text-gray-600">Gérez vos sources de revenus et suivez vos entrées d'argent</p>
      
      <div class="actions mt-4 flex flex-wrap justify-between items-center">
        <div class="filters flex flex-wrap gap-2 mb-2 md:mb-0">
          <select v-model="filters.period" class="border rounded p-2">
            <option value="all">Toutes les périodes</option>
            <option value="current_month">Mois en cours</option>
            <option value="last_month">Mois précédent</option>
            <option value="last_3_months">3 derniers mois</option>
            <option value="current_year">Année en cours</option>
          </select>
          
          <select v-model="filters.account" class="border rounded p-2">
            <option value="all">Tous les comptes</option>
            <option v-for="compte in comptes" :key="compte.id" :value="compte.id">
              {{ compte.nom }}
            </option>
          </select>
          
          <select v-model="filters.sort" class="border rounded p-2">
            <option value="date_desc">Date (récent → ancien)</option>
            <option value="date_asc">Date (ancien → récent)</option>
            <option value="amount_desc">Montant (décroissant)</option>
            <option value="amount_asc">Montant (croissant)</option>
          </select>
        </div>
        
        <router-link :to="{ name: 'user.incomes.create' }" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow flex items-center">
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Ajouter un revenu
        </router-link>
      </div>
    </div>
    
    <div class="summary-section mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="summary-card bg-white rounded-lg shadow-md p-4">
        <h3 class="text-sm font-medium text-gray-500">Revenus Totaux ({{ getPeriodLabel() }})</h3>
        <p class="text-2xl font-semibold mt-1">{{ formatCurrency(totalIncomes) }}</p>
        <p class="text-sm mt-1" :class="totalIncomesChange >= 0 ? 'text-green-600' : 'text-red-600'">
          <span v-if="totalIncomesChange > 0">+</span>{{ totalIncomesChange }}% par rapport à la période précédente
        </p>
      </div>
      
      <div class="summary-card bg-white rounded-lg shadow-md p-4">
        <h3 class="text-sm font-medium text-gray-500">Revenu Mensuel Moyen</h3>
        <p class="text-2xl font-semibold mt-1">{{ formatCurrency(averageMonthlyIncome) }}</p>
        <p class="text-sm text-gray-500 mt-1">Calculé sur les 6 derniers mois</p>
      </div>
      
      <div class="summary-card bg-white rounded-lg shadow-md p-4">
        <h3 class="text-sm font-medium text-gray-500">Prochain Revenu Prévu</h3>
        <p class="text-2xl font-semibold mt-1">{{ formatCurrency(nextIncomeAmount) }}</p>
        <p class="text-sm text-gray-500 mt-1">
          <span v-if="nextIncomeDate">{{ nextIncomeSource }} - {{ formatDate(nextIncomeDate) }}</span>
          <span v-else>Aucun revenu récurrent prévu</span>
        </p>
      </div>
    </div>
    
    <div class="incomes-content">
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="spinner"></div>
        <span class="ml-3 text-gray-600">Chargement des revenus...</span>
      </div>
      
      <div v-else-if="filteredIncomes.length === 0" class="empty-state text-center py-10 bg-gray-50 rounded-lg">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3 class="mt-2 text-lg font-medium">Aucun revenu trouvé</h3>
        <p class="mt-1 text-gray-500">Commencez par ajouter votre premier revenu</p>
        <router-link :to="{ name: 'user.incomes.create' }" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
          Ajouter un revenu
        </router-link>
      </div>
      
      <div v-else>
        <div class="incomes-list bg-white rounded-lg shadow-md overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Source
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Montant
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Compte
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Périodicité
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="income in filteredIncomes" :key="income.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ income.source }}
                      </div>
                      <div v-if="income.description" class="text-xs text-gray-500 truncate max-w-xs">
                        {{ income.description }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900">{{ formatCurrency(income.montant) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ formatDate(income.date_perception) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ getAccountName(income.compte_id) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                        :class="getPeriodicityClass(income.periodicite)">
                    {{ getPeriodicityLabel(income.periodicite) }}
                  </span>
                  <span v-if="income.recurrent" class="ml-1 px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    Récurrent
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <router-link :to="{ name: 'user.incomes.show', params: { id: income.id } }" class="text-blue-600 hover:text-blue-900 mr-3">
                    Détails
                  </router-link>
                  <router-link :to="{ name: 'user.incomes.edit', params: { id: income.id } }" class="text-gray-600 hover:text-gray-900">
                    Modifier
                  </router-link>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div class="pagination-controls mt-4 flex justify-between items-center">
          <div class="text-sm text-gray-500">
            Affichage de {{ startIndex + 1 }} à {{ endIndex }} sur {{ totalIncomes }} revenus
          </div>
          
          <div class="flex space-x-2">
            <button 
              @click="currentPage = Math.max(1, currentPage - 1)"
              :disabled="currentPage === 1"
              :class="currentPage === 1 ? 'bg-gray-200 cursor-not-allowed' : 'bg-white hover:bg-gray-50'"
              class="px-3 py-1 border rounded shadow-sm text-sm text-gray-700"
            >
              Précédent
            </button>
            
            <button
              v-for="page in displayedPages"
              :key="page"
              @click="currentPage = page"
              :class="currentPage === page ? 'bg-blue-600 text-white' : 'bg-white hover:bg-gray-50 text-gray-700'"
              class="px-3 py-1 border rounded shadow-sm text-sm"
            >
              {{ page }}
            </button>
            
            <button 
              @click="currentPage = Math.min(totalPages, currentPage + 1)"
              :disabled="currentPage === totalPages"
              :class="currentPage === totalPages ? 'bg-gray-200 cursor-not-allowed' : 'bg-white hover:bg-gray-50'"
              class="px-3 py-1 border rounded shadow-sm text-sm text-gray-700"
            >
              Suivant
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      incomes: [],
      comptes: [],
      loading: true,
      filters: {
        period: 'all',
        account: 'all',
        sort: 'date_desc'
      },
      currentPage: 1,
      itemsPerPage: 10,
      totalIncomes: 0,
      totalIncomesChange: 8.2, // Simulé: pourcentage de changement par rapport à la période précédente
      averageMonthlyIncome: 12000, // Simulé: revenu mensuel moyen
      nextIncomeAmount: 15000, // Simulé: montant du prochain revenu
      nextIncomeDate: '2025-05-25', // Simulé: date du prochain revenu
      nextIncomeSource: 'Salaire' // Simulé: source du prochain revenu
    }
  },
  
  computed: {
    filteredIncomes() {
      let result = [...this.incomes];
      
      // Filtre par période
      if (this.filters.period !== 'all') {
        const today = new Date();
        let startDate = new Date();
        
        switch (this.filters.period) {
          case 'current_month':
            startDate.setDate(1);
            startDate.setHours(0, 0, 0, 0);
            break;
            
          case 'last_month':
            startDate.setMonth(startDate.getMonth() - 1);
            startDate.setDate(1);
            startDate.setHours(0, 0, 0, 0);
            const endOfLastMonth = new Date();
            endOfLastMonth.setDate(0);
            endOfLastMonth.setHours(23, 59, 59, 999);
            result = result.filter(income => {
              const incomeDate = new Date(income.date_perception);
              return incomeDate >= startDate && incomeDate <= endOfLastMonth;
            });
            return result;
            
          case 'last_3_months':
            startDate.setMonth(startDate.getMonth() - 3);
            startDate.setHours(0, 0, 0, 0);
            break;
            
          case 'current_year':
            startDate.setMonth(0);
            startDate.setDate(1);
            startDate.setHours(0, 0, 0, 0);
            break;
        }
        
        if (this.filters.period !== 'last_month') {
          result = result.filter(income => {
            const incomeDate = new Date(income.date_perception);
            return incomeDate >= startDate;
          });
        }
      }
      
      // Filtre par compte
      if (this.filters.account !== 'all') {
        result = result.filter(income => income.compte_id.toString() === this.filters.account.toString());
      }
      
      // Tri
      switch (this.filters.sort) {
        case 'date_desc':
          result.sort((a, b) => new Date(b.date_perception) - new Date(a.date_perception));
          break;
          
        case 'date_asc':
          result.sort((a, b) => new Date(a.date_perception) - new Date(b.date_perception));
          break;
          
        case 'amount_desc':
          result.sort((a, b) => b.montant - a.montant);
          break;
          
        case 'amount_asc':
          result.sort((a, b) => a.montant - b.montant);
          break;
      }
      
      return result;
    },
    
    paginatedIncomes() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredIncomes.slice(start, end);
    },
    
    totalPages() {
      return Math.ceil(this.filteredIncomes.length / this.itemsPerPage);
    },
    
    displayedPages() {
      const pages = [];
      const maxPages = 5; // Nombre maximum de pages à afficher
      
      if (this.totalPages <= maxPages) {
        // Si le nombre total de pages est inférieur ou égal à maxPages, afficher toutes les pages
        for (let i = 1; i <= this.totalPages; i++) {
          pages.push(i);
        }
      } else {
        // Sinon, afficher un sous-ensemble de pages
        if (this.currentPage <= 3) {
          // Si la page actuelle est parmi les 3 premières, afficher les 5 premières pages
          for (let i = 1; i <= maxPages; i++) {
            pages.push(i);
          }
        } else if (this.currentPage >= this.totalPages - 2) {
          // Si la page actuelle est parmi les 3 dernières, afficher les 5 dernières pages
          for (let i = this.totalPages - maxPages + 1; i <= this.totalPages; i++) {
            pages.push(i);
          }
        } else {
          // Sinon, afficher 2 pages avant et 2 pages après la page actuelle
          for (let i = this.currentPage - 2; i <= this.currentPage + 2; i++) {
            pages.push(i);
          }
        }
      }
      
      return pages;
    },
    
    startIndex() {
      return (this.currentPage - 1) * this.itemsPerPage;
    },
    
    endIndex() {
      return Math.min(this.startIndex + this.itemsPerPage, this.filteredIncomes.length);
    }
  },
  
  watch: {
    'filters.period'() {
      this.currentPage = 1;
    },
    'filters.account'() {
      this.currentPage = 1;
    },
    'filters.sort'() {
      this.currentPage = 1;
    }
  },
  
  created() {
    this.fetchData();
  },
  
  methods: {
    async fetchData() {
      try {
        this.loading = true;
        
        // Récupérer les comptes et les revenus en parallèle
        const [comptesResponse, incomesResponse] = await Promise.all([
          axios.get('/api/accounts'),
          axios.get('/api/incomes')
        ]);
        
        this.comptes = comptesResponse.data.data;
        this.incomes = incomesResponse.data.data;
        this.totalIncomes = this.incomes.length;
        
        // Note: Dans une application réelle, les statistiques comme averageMonthlyIncome, 
        // totalIncomesChange, etc. seraient probablement calculées côté serveur ou à partir des données récupérées
      } catch (error) {
        console.error('Erreur lors de la récupération des données:', error);
        this.$toast.error('Impossible de récupérer la liste des revenus. Veuillez réessayer.');
      } finally {
        this.loading = false;
      }
    },
    
    getAccountName(accountId) {
      const account = this.comptes.find(compte => compte.id === accountId);
      return account ? account.nom : 'Compte inconnu';
    },
    
    getPeriodicityLabel(periodicite) {
      const labels = {
        'mensuel': 'Mensuel',
        'bimensuel': 'Bimensuel',
        'hebdomadaire': 'Hebdomadaire',
        'trimestriel': 'Trimestriel',
        'annuel': 'Annuel',
        'ponctuel': 'Ponctuel'
      };
      
      return labels[periodicite] || periodicite;
    },
    
    getPeriodicityClass(periodicite) {
      const classes = {
        'mensuel': 'bg-green-100 text-green-800',
        'bimensuel': 'bg-blue-100 text-blue-800',
        'hebdomadaire': 'bg-purple-100 text-purple-800',
        'trimestriel': 'bg-yellow-100 text-yellow-800',
        'annuel': 'bg-red-100 text-red-800',
        'ponctuel': 'bg-gray-100 text-gray-800'
      };
      
      return classes[periodicite] || 'bg-gray-100 text-gray-800';
    },
    
    getPeriodLabel() {
      const labels = {
        'all': 'Tous',
        'current_month': 'Mois en cours',
        'last_month': 'Mois précédent',
        'last_3_months': '3 derniers mois',
        'current_year': 'Année en cours'
      };
      
      return labels[this.filters.period] || '';
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    },
    
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('fr-FR');
    }
  }
}
</script>

<style scoped>
.spinner {
  border: 3px solid rgba(0, 0, 0, 0.1);
  border-radius: 50%;
  border-top: 3px solid #3498db;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>