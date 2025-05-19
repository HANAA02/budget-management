<template>
  <div class="income-edit-container">
    <div class="header-section mb-6">
      <div class="flex items-center justify-between">
        <div>
          <router-link :to="{ name: 'user.incomes.index' }" class="text-blue-600 flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Retour aux revenus
          </router-link>
          <h1 class="text-2xl font-semibold">Modifier le revenu</h1>
          <p class="text-gray-600">Mettez à jour les informations de votre source de revenu</p>
        </div>
      </div>
    </div>
    
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="spinner"></div>
      <span class="ml-3 text-gray-600">Chargement des données...</span>
    </div>
    
    <div v-else class="content-section">
      <div class="bg-white rounded-lg shadow-md p-6">
        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Champ source de revenu -->
            <div class="form-group">
              <label for="source" class="block text-sm font-medium text-gray-700 mb-1">Source de revenu *</label>
              <input
                type="text"
                id="source"
                v-model="form.source"
                class="w-full p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500"
                :class="{ 'border-red-500': errors.source }"
                placeholder="Ex: Salaire, Freelance, Dividendes..."
                required
              >
              <p v-if="errors.source" class="text-red-500 text-xs mt-1">{{ errors.source[0] }}</p>
            </div>
            
            <!-- Champ montant -->
            <div class="form-group">
              <label for="montant" class="block text-sm font-medium text-gray-700 mb-1">Montant *</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <span class="text-gray-500">MAD</span>
                </div>
                <input
                  type="number"
                  id="montant"
                  v-model="form.montant"
                  class="w-full pl-12 p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500"
                  :class="{ 'border-red-500': errors.montant }"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  required
                >
              </div>
              <p v-if="errors.montant" class="text-red-500 text-xs mt-1">{{ errors.montant[0] }}</p>
            </div>
            
            <!-- Champ date de perception -->
            <div class="form-group">
              <label for="date_perception" class="block text-sm font-medium text-gray-700 mb-1">Date de perception *</label>
              <input
                type="date"
                id="date_perception"
                v-model="form.date_perception"
                class="w-full p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500"
                :class="{ 'border-red-500': errors.date_perception }"
                required
              >
              <p v-if="errors.date_perception" class="text-red-500 text-xs mt-1">{{ errors.date_perception[0] }}</p>
            </div>
            
            <!-- Champ périodicité -->
            <div class="form-group">
              <label for="periodicite" class="block text-sm font-medium text-gray-700 mb-1">Périodicité</label>
              <select
                id="periodicite"
                v-model="form.periodicite"
                class="w-full p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500"
                :class="{ 'border-red-500': errors.periodicite }"
              >
                <option value="mensuel">Mensuel</option>
                <option value="bimensuel">Bimensuel</option>
                <option value="hebdomadaire">Hebdomadaire</option>
                <option value="trimestriel">Trimestriel</option>
                <option value="annuel">Annuel</option>
                <option value="ponctuel">Ponctuel</option>
              </select>
              <p v-if="errors.periodicite" class="text-red-500 text-xs mt-1">{{ errors.periodicite[0] }}</p>
            </div>
            
            <!-- Champ compte -->
            <div class="form-group">
              <label for="compte_id" class="block text-sm font-medium text-gray-700 mb-1">Compte de destination *</label>
              <select
                id="compte_id"
                v-model="form.compte_id"
                class="w-full p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500"
                :class="{ 'border-red-500': errors.compte_id }"
                required
              >
                <option value="" disabled>Sélectionnez un compte</option>
                <option v-for="compte in comptes" :key="compte.id" :value="compte.id">
                  {{ compte.nom }} ({{ formatCurrency(compte.solde) }})
                </option>
              </select>
              <p v-if="errors.compte_id" class="text-red-500 text-xs mt-1">{{ errors.compte_id[0] }}</p>
            </div>
            
            <!-- Description (optionnel) -->
            <div class="form-group md:col-span-2">
              <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (optionnel)</label>
              <textarea
                id="description"
                v-model="form.description"
                class="w-full p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500"
                :class="{ 'border-red-500': errors.description }"
                rows="3"
                placeholder="Détails supplémentaires sur ce revenu..."
              ></textarea>
              <p v-if="errors.description" class="text-red-500 text-xs mt-1">{{ errors.description[0] }}</p>
            </div>
          </div>
          
          <!-- Revenu récurrent -->
          <div class="form-group mt-4">
            <div class="flex items-center">
              <input
                type="checkbox"
                id="recurrent"
                v-model="form.recurrent"
                class="h-4 w-4 text-blue-600 focus:ring focus:ring-blue-200 focus:ring-opacity-50 border-gray-300 rounded"
              >
              <label for="recurrent" class="ml-2 block text-sm text-gray-700">
                Revenu récurrent (sera automatiquement ajouté à chaque période)
              </label>
            </div>
          </div>
          
          <!-- Options pour revenus récurrents -->
          <div v-if="form.recurrent" class="mt-4 p-4 bg-gray-50 rounded border border-gray-200">
            <h3 class="text-md font-medium mb-3">Options de récurrence</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="form-group">
                <label for="date_fin_recurrence" class="block text-sm font-medium text-gray-700 mb-1">
                  Date de fin de récurrence (optionnel)
                </label>
                <input
                  type="date"
                  id="date_fin_recurrence"
                  v-model="form.date_fin_recurrence"
                  class="w-full p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500"
                >
                <p class="text-xs text-gray-500 mt-1">Laissez vide pour une récurrence sans fin</p>
              </div>
              
              <div class="form-group">
                <label for="jour_perception" class="block text-sm font-medium text-gray-700 mb-1">
                  Jour de perception dans le mois
                </label>
                <select
                  id="jour_perception"
                  v-model="form.jour_perception"
                  class="w-full p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500"
                >
                  <option v-for="jour in 31" :key="jour" :value="jour">{{ jour }}</option>
                </select>
                <p class="text-xs text-gray-500 mt-1">Pour les revenus mensuels</p>
              </div>
            </div>
          </div>
          
          <!-- Mise à jour des revenus futurs -->
          <div v-if="incomeHasFutureOccurrences" class="mt-6 p-4 bg-yellow-50 rounded border border-yellow-200">
            <h3 class="text-yellow-800 font-medium mb-2">Mise à jour des occurrences futures</h3>
            <div class="flex items-center mb-3">
              <input
                type="checkbox"
                id="update_future"
                v-model="form.update_future_occurrences"
                class="h-4 w-4 text-blue-600 focus:ring focus:ring-blue-200 focus:ring-opacity-50 border-gray-300 rounded"
              >
              <label for="update_future" class="ml-2 block text-sm text-yellow-800">
                Appliquer ces modifications à toutes les occurrences futures de ce revenu récurrent
              </label>
            </div>
            <p class="text-xs text-yellow-700">
              Si cette option est cochée, les modifications seront appliquées à toutes les occurrences futures de ce revenu.
              Dans le cas contraire, seule cette occurrence spécifique sera modifiée.
            </p>
          </div>
          
          <!-- Boutons de formulaire -->
          <div class="form-actions mt-8 flex justify-between">
            <button
              type="button"
              @click="confirmDelete"
              class="px-4 py-2 border border-red-300 rounded shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Supprimer
            </button>
            
            <div class="space-x-3">
              <router-link 
                :to="{ name: 'user.incomes.show', params: { id: incomeId } }" 
                class="px-4 py-2 border border-gray-300 rounded shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Annuler
              </router-link>
              
              <button 
                type="submit" 
                class="px-4 py-2 border border-transparent rounded shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                :disabled="submitting"
              >
                <span v-if="submitting">Enregistrement...</span>
                <span v-else>Enregistrer les modifications</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
    
    <!-- Modal de confirmation de suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-semibold mb-4">Confirmer la suppression</h3>
        
        <p class="mb-4">Êtes-vous sûr de vouloir supprimer ce revenu ? Cette action est irréversible.</p>
        
        <div v-if="incomeIsRecurrent" class="mb-4 p-3 bg-yellow-50 rounded border border-yellow-200">
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
  </div>
</template>

<script>
export default {
  data() {
    return {
      incomeId: null,
      form: {
        source: '',
        montant: '',
        date_perception: '',
        periodicite: 'mensuel',
        compte_id: '',
        description: '',
        recurrent: false,
        date_fin_recurrence: null,
        jour_perception: 1,
        update_future_occurrences: false
      },
      comptes: [],
      errors: {},
      loading: true,
      submitting: false,
      showDeleteModal: false,
      incomeIsRecurrent: false,
      incomeHasFutureOccurrences: false,
      deleteFutureOccurrences: false,
      originalIncome: null
    }
  },
  
  created() {
    this.incomeId = this.$route.params.id;
    this.fetchData();
  },
  
  methods: {
    async fetchData() {
      try {
        this.loading = true;
        
        // Récupérer les comptes et le revenu en parallèle
        const [comptesResponse, incomeResponse] = await Promise.all([
          axios.get('/api/accounts'),
          axios.get(`/api/incomes/${this.incomeId}`)
        ]);
        
        this.comptes = comptesResponse.data.data;
        const income = incomeResponse.data.data;
        this.originalIncome = {...income};
        
        // Mise à jour du formulaire avec les données du revenu
        this.form = {
          source: income.source,
          montant: income.montant,
          date_perception: this.formatDateForInput(income.date_perception),
          periodicite: income.periodicite,
          compte_id: income.compte_id,
          description: income.description || '',
          recurrent: income.recurrent || false,
          date_fin_recurrence: income.date_fin_recurrence ? this.formatDateForInput(income.date_fin_recurrence) : null,
          jour_perception: income.jour_perception || this.getDayFromDate(income.date_perception),
          update_future_occurrences: false
        };
        
        // Vérifier si le revenu est récurrent et a des occurrences futures
        this.incomeIsRecurrent = income.recurrent || false;
        
        // Vérifier si le revenu a des occurrences futures (API fictive pour l'exemple)
        if (this.incomeIsRecurrent) {
          try {
            const futureOccurrencesResponse = await axios.get(`/api/incomes/${this.incomeId}/future-occurrences`);
            this.incomeHasFutureOccurrences = futureOccurrencesResponse.data.data.length > 0;
          } catch (error) {
            console.error('Erreur lors de la vérification des occurrences futures:', error);
            // Par défaut, on suppose qu'il y a des occurrences futures si c'est récurrent
            this.incomeHasFutureOccurrences = this.incomeIsRecurrent;
          }
        }
      } catch (error) {
        console.error('Erreur lors de la récupération des données:', error);
        this.$toast.error('Impossible de récupérer les données du revenu. Veuillez réessayer.');
      } finally {
        this.loading = false;
      }
    },
    
    async submitForm() {
      this.errors = {};
      this.submitting = true;
      
      try {
        const response = await axios.put(`/api/incomes/${this.incomeId}`, this.form);
        
        this.$toast.success('Le revenu a été mis à jour avec succès!');
        
        // Redirection vers le détail du revenu
        this.$router.push({
          name: 'user.incomes.show',
          params: { id: this.incomeId }
        });
      } catch (error) {
        this.submitting = false;
        
        if (error.response && error.response.status === 422) {
          // Erreurs de validation
          this.errors = error.response.data.errors;
          this.$toast.error('Veuillez corriger les erreurs dans le formulaire.');
        } else {
          console.error('Erreur lors de la mise à jour du revenu:', error);
          this.$toast.error('Une erreur est survenue lors de la mise à jour du revenu. Veuillez réessayer.');
        }
      }
    },
    
    confirmDelete() {
      this.showDeleteModal = true;
    },
    
    async deleteIncome() {
      try {
        await axios.delete(`/api/incomes/${this.incomeId}`, {
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
    
    formatDateForInput(dateString) {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      
      return `${year}-${month}-${day}`;
    },
    
    getDayFromDate(dateString) {
      if (!dateString) return 1;
      
      const date = new Date(dateString);
      return date.getDate();
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
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