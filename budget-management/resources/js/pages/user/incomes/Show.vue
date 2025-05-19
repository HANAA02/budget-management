<template>
  <div class="income-show-container">
    <!-- En-tête avec information de navigation et actions -->
    <div class="header-section mb-6">
      <div class="flex justify-between items-center">
        <div>
          <router-link :to="{ name: 'user.incomes.index' }" class="text-blue-600 flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Retour aux revenus
          </router-link>
          <h1 class="text-2xl font-semibold">{{ income.source }}</h1>
          <p class="text-gray-600">{{ income.description || 'Aucune description fournie' }}</p>
        </div>
        
        <div class="actions flex space-x-2">
          <router-link :to="{ name: 'user.incomes.edit', params: { id: income.id } }" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
            </svg>
            Modifier
          </router-link>
          
          <button @click="confirmDelete" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Supprimer
          </button>
        </div>
      </div>
    </div>
    
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="spinner"></div>
      <span class="ml-3 text-gray-600">Chargement des données...</span>
    </div>
    
    <div v-else class="content-grid grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Panneau principal d'informations -->
      <div class="lg:col-span-2">
        <!-- Informations générales -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-xl font-medium mb-4">Informations générales</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="info-group">
              <h3 class="text-sm text-gray-500">Montant</h3>
              <p class="text-2xl font-semibold">{{ formatCurrency(income.montant) }}</p>
            </div>
            
            <div class="info-group">
              <h3 class="text-sm text-gray-500">Compte de destination</h3>
              <div class="flex items-center">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                  </svg>
                </div>
                <span class="text-lg">{{ compte ? compte.nom : 'Compte inconnu' }}</span>
              </div>
              <p class="text-sm text-gray-500 mt-1">Solde actuel : {{ compte ? formatCurrency(compte.solde) : '—' }}</p>
            </div>
            
            <div class="info-group">
              <h3 class="text-sm text-gray-500">Date de perception</h3>
              <p class="text-lg">{{ formatDate(income.date_perception) }}</p>
            </div>
            
            <div class="info-group">
              <h3 class="text-sm text-gray-500">Périodicité</h3>
              <div class="flex items-center">
                <span class="px-2 py-1 text-sm font-semibold rounded-full mr-2" 
                      :class="getPeriodicityClass(income.periodicite)">
                  {{ getPeriodicityLabel(income.periodicite) }}
                </span>
                <span v-if="income.recurrent" class="px-2 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                  Récurrent
                </span>
              </div>
            </div>
          </div>
          
          <!-- Récurrence (si applicable) -->
          <div v-if="income.recurrent" class="mt-6 pt-4 border-t border-gray-100">
            <h3 class="text-lg font-medium mb-3">Informations de récurrence</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="info-group">
                <h4 class="text-sm text-gray-500">Jour de perception mensuel</h4>
                <p class="text-lg">{{ income.jour_perception || getDayFromDate(income.date_perception) }}</p>
              </div>
              
              <div class="info-group">
                <h4 class="text-sm text-gray-500">Fin de récurrence</h4>
                <p class="text-lg">{{ income.date_fin_recurrence ? formatDate(income.date_fin_recurrence) : 'Sans limite' }}</p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Historique des occurrences (pour revenus récurrents) -->
        <div v-if="income.recurrent" class="bg-white rounded-lg shadow-md p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-medium">Historique des occurrences</h2>
            
            <div class="flex space-x-2">
              <select v-model="occurrencesFilter" class="border rounded p-1 text-sm">
                <option value="all">Toutes</option>
                <option value="past">Passées</option>
                <option value="future">Futures</option>
              </select>
            </div>
          </div>
          
          <div v-if="loadingOccurrences" class="flex justify-center items-center py-8">
            <div class="spinner"></div>
            <span class="ml-3 text-gray-600">Chargement de l'historique...</span>
          </div>
          
          <div v-else-if="filteredOccurrences.length === 0" class="text-center py-8 text-gray-500">
            Aucune occurrence trouvée pour ce revenu récurrent.
          </div>
          
          <div v-else class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Montant
                  </th>
                  <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Statut
                  </th>
                  <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="occurrence in filteredOccurrences" :key="occurrence.id || occurrence.date" class="hover:bg-gray-50">
                  <td class="px-3 py-2 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(occurrence.date) }}</div>
                  </td>
                  <td class="px-3 py-2 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ formatCurrency(occurrence.montant) }}</div>
                  </td>
                  <td class="px-3 py-2 whitespace-nowrap">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                          :class="getOccurrenceStatusClass(occurrence.statut)">
                      {{ getOccurrenceStatusLabel(occurrence.statut) }}
                    </span>
                  </td>
                  <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                    <a v-if="occurrence.statut === 'prevu'" href="#" @click.prevent="confirmSkipOccurrence(occurrence)" class="text-gray-600 hover:text-gray-900 mr-2">
                      Ignorer
                    </a>
                    <a v-if="occurrence.id" :href="`/incomes/${occurrence.id}`" class="text-blue-600 hover:text-blue-900">
                      Détails
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        
        <!-- Transactions liées -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-medium mb-4">Transactions liées</h2>
          
          <div v-if="transactions.length === 0" class="text-center py-6 text-gray-500">
            Aucune transaction n'est associée à ce revenu.
          </div>
          
          <div v-else>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                  </th>
                  <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Description
                  </th>
                  <th scope="col" class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Montant
                  </th>
                  <th scope="col" class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="transaction in transactions" :key="transaction.id" class="hover:bg-gray-50">
                  <td class="px-3 py-2 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ formatDate(transaction.date) }}</div>
                  </td>
                  <td class="px-3 py-2">
                    <div class="text-sm text-gray-900">{{ transaction.description }}</div>
                  </td>
                  <td class="px-3 py-2 whitespace-nowrap">
                    <div class="text-sm font-medium text-green-600">{{ formatCurrency(transaction.montant) }}</div>
                  </td>
                  <td class="px-3 py-2 whitespace-nowrap text-right text-sm font-medium">
                    <a :href="`/transactions/${transaction.id}`" class="text-blue-600 hover:text-blue-900">
                      Détails
                    </a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Panneau latéral d'informations complémentaires -->
      <div class="lg:col-span-1">
        <!-- Statistiques -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-lg font-medium mb-3">Statistiques</h2>
          
          <div class="space-y-4">
            <div class="stat-item">
              <span class="text-sm text-gray-500">Total perçu</span>
              <p class="text-xl font-semibold">{{ formatCurrency(totalPerceived) }}</p>
            </div>
            
            <div class="stat-item">
              <span class="text-sm text-gray-500">Nombre d'occurrences</span>
              <p class="text-xl font-semibold">{{ occurrencesCount }}</p>
            </div>
            
            <div v-if="income.recurrent" class="stat-item">
              <span class="text-sm text-gray-500">Prochaine occurrence</span>
              <p class="text-xl font-semibold">{{ nextOccurrenceDate ? formatDate(nextOccurrenceDate) : '—' }}</p>
            </div>
            
            <div class="stat-item">
              <span class="text-sm text-gray-500">Part dans le budget mensuel</span>
              <div class="flex items-center">
                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                  <div class="bg-blue-600 h-2.5 rounded-full" :style="{ width: `${budgetPercentage}%` }"></div>
                </div>
                <span>{{ budgetPercentage }}%</span>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Répartition sur les catégories de budget -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-medium mb-3">Répartition budgétaire</h2>
          
          <div v-if="!budgetAllocations.length" class="text-center py-6 text-gray-500">
            Ce revenu n'est pas encore alloué à des catégories de budget.
          </div>
          
          <div v-else>
            <div v-for="allocation in budgetAllocations" :key="allocation.id" class="mb-3 last:mb-0">
              <div class="flex justify-between items-center mb-1">
                <span class="text-sm">{{ allocation.category }}</span>
                <span class="text-sm font-medium">{{ formatCurrency(allocation.amount) }}</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full" :style="{ width: `${allocation.percentage}%`, backgroundColor: allocation.color }"></div>
              </div>
            </div>
          </div>
          
          <div class="mt-4 pt-4 border-t border-gray-100">
            <router-link :to="{ name: 'user.budgets.create' }" class="text-blue-600 text-sm hover:text-blue-800 flex items-center">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Créer un nouveau budget
            </router-link>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal pour confirmer la suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-semibold mb-4">Confirmer la suppression</h3>
        
        <p class="mb-4">Êtes-vous sûr de vouloir supprimer ce revenu ? Cette action est irréversible.</p>
        
        <div v-if="income.recurrent" class="mb-4 p-3 bg-yellow-50 rounded border border-yellow-200">
          <div class="flex items-center mb-2">
            <input
              type="checkbox"
              id="delete_future"
              v-model="deleteFutureOccurrences"
              class="h-4 w-4 text-red-600 focus:ring focus:ring-red-200 focus:ring-opacity-50 border-gray-300 rounded"
            >
            <label for="delete_future" class="ml-2 block text-sm text-yellow-800 font-medium">
              Supprimer toutes les occurrences futures
            </label>
          </div>
          <p class="text-xs text-yellow-700">
            Ce revenu fait partie d'une série récurrente. Vous pouvez choisir de supprimer uniquement cette occurrence
            ou toutes les occurrences futures.
          </p>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
            Annuler
          </button>
          <button @click="deleteIncome" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
            Supprimer
          </button>
        </div>
      </div>
    </div>
    
    <!-- Modal pour confirmer l'ignore d'une occurrence -->
    <div v-if="showSkipModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-semibold mb-4">Ignorer cette occurrence</h3>
        
        <p class="mb-4">Êtes-vous sûr de vouloir ignorer cette occurrence de revenu prévue le {{ selectedOccurrence ? formatDate(selectedOccurrence.date) : '' }} ?</p>
        
        <div class="flex justify-end space-x-3">
          <button @click="showSkipModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
            Annuler
          </button>
          <button @click="skipOccurrence" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
            Ignorer
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      income: {
        id: null,
        source: '',
        montant: 0,
        date_perception: null,
        periodicite: 'mensuel',
        compte_id: null,
        description: '',
        recurrent: false,
        date_fin_recurrence: null,
        jour_perception: null
      },
      compte: null,
      occurrences: [],
      transactions: [],
      budgetAllocations: [],
      loading: true,
      loadingOccurrences: false,
      occurrencesFilter: 'all',
      showDeleteModal: false,
      showSkipModal: false,
      deleteFutureOccurrences: false,
      selectedOccurrence: null,
      totalPerceived: 0,
      occurrencesCount: 0,
      nextOccurrenceDate: null,
      budgetPercentage: 0
    }
  },
  
  computed: {
    filteredOccurrences() {
      if (!this.occurrences.length) return [];
      
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      switch (this.occurrencesFilter) {
        case 'past':
          return this.occurrences.filter(o => new Date(o.date) < today);
        case 'future':
          return this.occurrences.filter(o => new Date(o.date) >= today);
        default:
          return this.occurrences;
      }
    }
  },
  
  created() {
    this.fetchIncomeDetails();
  },
  
  methods: {
    async fetchIncomeDetails() {
      try {
        this.loading = true;
        const incomeId = this.$route.params.id;
        
        // Récupérer les détails du revenu
        const response = await axios.get(`/api/incomes/${incomeId}`);
        this.income = response.data.data;
        
        // Récupérer les détails du compte associé
        if (this.income.compte_id) {
          try {
            const compteResponse = await axios.get(`/api/accounts/${this.income.compte_id}`);
            this.compte = compteResponse.data.data;
          } catch (error) {
            console.error('Erreur lors de la récupération des détails du compte:', error);
          }
        }
        
        // Récupérer l'historique des occurrences (pour les revenus récurrents)
        if (this.income.recurrent) {
          this.fetchOccurrences();
        }
        
        // Récupérer les transactions liées
        try {
          const transactionsResponse = await axios.get(`/api/incomes/${incomeId}/transactions`);
          this.transactions = transactionsResponse.data.data;
        } catch (error) {
          console.error('Erreur lors de la récupération des transactions:', error);
        }
        
        // Récupérer les allocations budgétaires
        try {
          const allocationsResponse = await axios.get(`/api/incomes/${incomeId}/budget-allocations`);
          this.budgetAllocations = allocationsResponse.data.data;
        } catch (error) {
          console.error('Erreur lors de la récupération des allocations budgétaires:', error);
        }
        
        // Calculer les statistiques
        this.calculateStatistics();
      } catch (error) {
        console.error('Erreur lors de la récupération des détails du revenu:', error);
        this.$toast.error('Impossible de récupérer les détails du revenu. Veuillez réessayer.');
      } finally {
        this.loading = false;
      }
    },
    
    async fetchOccurrences() {
      try {
        this.loadingOccurrences = true;
        const response = await axios.get(`/api/incomes/${this.income.id}/occurrences`);
        this.occurrences = response.data.data;
        
        // Calculer la prochaine occurrence
        this.calculateNextOccurrence();
      } catch (error) {
        console.error('Erreur lors de la récupération des occurrences:', error);
      } finally {
        this.loadingOccurrences = false;
      }
    },
    
    calculateStatistics() {
      // Ces calculs seraient normalement basés sur des données réelles provenant de l'API
      // Pour l'exemple, nous utilisons des valeurs simulées
      
      // Total perçu
      this.totalPerceived = this.transactions.reduce((sum, transaction) => sum + transaction.montant, 0);
      
      // Nombre d'occurrences
      this.occurrencesCount = this.transactions.length;
      
      // Pourcentage du budget
      // Simulé: normalement calculé à partir du budget mensuel total
      this.budgetPercentage = Math.min(Math.round((this.income.montant / 12000) * 100), 100);
      
      // Exemple d'allocations budgétaires (simulées)
      if (this.budgetAllocations.length === 0) {
        this.budgetAllocations = [
          { id: 1, category: 'Logement', amount: this.income.montant * 0.4, percentage: 40, color: '#4299e1' },
          { id: 2, category: 'Alimentation', amount: this.income.montant * 0.25, percentage: 25, color: '#48bb78' },
          { id: 3, category: 'Transport', amount: this.income.montant * 0.15, percentage: 15, color: '#ed8936' },
          { id: 4, category: 'Épargne', amount: this.income.montant * 0.1, percentage: 10, color: '#9f7aea' },
          { id: 5, category: 'Divers', amount: this.income.montant * 0.1, percentage: 10, color: '#a0aec0' }
        ];
      }
    },
    
    calculateNextOccurrence() {
      // Trouver la prochaine occurrence prévue
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      
      const futureOccurrences = this.occurrences.filter(o => 
        new Date(o.date) >= today && o.statut === 'prevu'
      );
      
      if (futureOccurrences.length > 0) {
        // Trier par date croissante
        futureOccurrences.sort((a, b) => new Date(a.date) - new Date(b.date));
        this.nextOccurrenceDate = futureOccurrences[0].date;
      } else {
        this.nextOccurrenceDate = null;
      }
    },
    
    confirmDelete() {
      this.showDeleteModal = true;
    },
    
    async deleteIncome() {
      try {
        await axios.delete(`/api/incomes/${this.income.id}`, {
          data: {
            delete_future_occurrences: this.deleteFutureOccurrences
          }
        });
        
        this.$toast.success('Le revenu a été supprimé avec succès!');
        this.showDeleteModal = false;
        
        // Redirection vers la liste des revenus
        this.$router.push({ name: 'user.incomes.index' });
      } catch (error) {
        console.error('Erreur lors de la suppression du revenu:', error);
        this.$toast.error('Une erreur est survenue lors de la suppression du revenu. Veuillez réessayer.');
      }
    },
    
    confirmSkipOccurrence(occurrence) {
      this.selectedOccurrence = occurrence;
      this.showSkipModal = true;
    },
    
    async skipOccurrence() {
      if (!this.selectedOccurrence) return;
      
      try {
        await axios.post(`/api/incomes/${this.income.id}/skip-occurrence`, {
          occurrence_date: this.selectedOccurrence.date
        });
        
        // Mettre à jour le statut dans la liste locale
        const index = this.occurrences.findIndex(o => 
          o.date === this.selectedOccurrence.date || o.id === this.selectedOccurrence.id
        );
        
        if (index !== -1) {
          this.occurrences[index].statut = 'ignore';
        }
        
        this.$toast.success('L\'occurrence a été ignorée avec succès.');
        this.showSkipModal = false;
        this.selectedOccurrence = null;
        
        // Recalculer la prochaine occurrence
        this.calculateNextOccurrence();
      } catch (error) {
        console.error('Erreur lors de l\'ignorance de l\'occurrence:', error);
        this.$toast.error('Une erreur est survenue. Veuillez réessayer.');
      }
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
    
    getOccurrenceStatusLabel(status) {
      const labels = {
        'percu': 'Perçu',
        'prevu': 'Prévu',
        'ignore': 'Ignoré',
        'en_retard': 'En retard'
      };
      
      return labels[status] || status;
    },
    
    getOccurrenceStatusClass(status) {
      const classes = {
        'percu': 'bg-green-100 text-green-800',
        'prevu': 'bg-blue-100 text-blue-800',
        'ignore': 'bg-gray-100 text-gray-800',
        'en_retard': 'bg-red-100 text-red-800'
      };
      
      return classes[status] || 'bg-gray-100 text-gray-800';
    },
    
    getDayFromDate(dateString) {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      return date.getDate();
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    },
    
    formatDate(dateString) {
      if (!dateString) return '';
      
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