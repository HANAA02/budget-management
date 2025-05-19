<template>
  <div class="income-create-container">
    <div class="header-section mb-6">
      <div class="flex items-center justify-between">
        <div>
          <router-link :to="{ name: 'user.incomes.index' }" class="text-blue-600 flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Retour aux revenus
          </router-link>
          <h1 class="text-2xl font-semibold">Ajouter un nouveau revenu</h1>
          <p class="text-gray-600">Enregistrez vos sources de revenus pour une meilleure gestion de votre budget</p>
        </div>
      </div>
    </div>
    
    <div class="content-section">
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
                <option value="" disabled selected>Sélectionnez un compte</option>
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
          
          <!-- Boutons de formulaire -->
          <div class="form-actions mt-8 flex justify-end space-x-3">
            <router-link 
              :to="{ name: 'user.incomes.index' }" 
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
              <span v-else>Enregistrer le revenu</span>
            </button>
          </div>
        </form>
      </div>
      
      <!-- Conseils et informations -->
      <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-100">
        <h3 class="text-blue-800 font-medium mb-2">Conseils pour la gestion de vos revenus</h3>
        <ul class="text-sm text-blue-700 space-y-1">
          <li>• Enregistrez tous vos revenus, même les petits montants, pour un suivi précis.</li>
          <li>• Utilisez l'option de récurrence pour les revenus réguliers comme votre salaire.</li>
          <li>• Vérifiez régulièrement que vos revenus récurrents sont correctement enregistrés.</li>
          <li>• Catégorisez correctement vos revenus pour faciliter l'analyse de vos finances.</li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        source: '',
        montant: '',
        date_perception: this.getTodayDate(),
        periodicite: 'mensuel',
        compte_id: '',
        description: '',
        recurrent: false,
        date_fin_recurrence: null,
        jour_perception: this.getTodayDay()
      },
      comptes: [],
      errors: {},
      submitting: false
    }
  },
  
  created() {
    this.fetchComptes();
  },
  
  methods: {
    async fetchComptes() {
      try {
        const response = await axios.get('/api/accounts');
        this.comptes = response.data.data;
        
        // Si l'utilisateur n'a qu'un seul compte, on le sélectionne par défaut
        if (this.comptes.length === 1) {
          this.form.compte_id = this.comptes[0].id;
        }
      } catch (error) {
        console.error('Erreur lors de la récupération des comptes:', error);
        this.$toast.error('Impossible de récupérer vos comptes. Veuillez réessayer.');
      }
    },
    
    async submitForm() {
      this.errors = {};
      this.submitting = true;
      
      try {
        const response = await axios.post('/api/incomes', this.form);
        
        this.$toast.success('Le revenu a été ajouté avec succès!');
        
        // Redirection vers la liste des revenus ou affichage du détail du revenu créé
        this.$router.push({
          name: 'user.incomes.show',
          params: { id: response.data.data.id }
        });
      } catch (error) {
        this.submitting = false;
        
        if (error.response && error.response.status === 422) {
          // Erreurs de validation
          this.errors = error.response.data.errors;
          this.$toast.error('Veuillez corriger les erreurs dans le formulaire.');
        } else {
          console.error('Erreur lors de l\'ajout du revenu:', error);
          this.$toast.error('Une erreur est survenue lors de l\'ajout du revenu. Veuillez réessayer.');
        }
      }
    },
    
    getTodayDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const day = String(today.getDate()).padStart(2, '0');
      
      return `${year}-${month}-${day}`;
    },
    
    getTodayDay() {
      return new Date().getDate();
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
/* Styles spécifiques si nécessaire */
</style>