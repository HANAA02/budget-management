<template>
  <div class="goals-container">
    <div class="header-section mb-4">
      <h1 class="text-2xl font-semibold">Mes Objectifs Financiers</h1>
      <p class="text-gray-600">Suivez la progression de vos objectifs d'épargne et de dépenses</p>
      
      <div class="actions mt-4 flex justify-between items-center">
        <div class="filters">
          <select v-model="statusFilter" class="border rounded p-2 mr-2">
            <option value="all">Tous les statuts</option>
            <option value="en cours">En cours</option>
            <option value="complété">Complété</option>
            <option value="annulé">Annulé</option>
          </select>
          
          <select v-model="categoryFilter" class="border rounded p-2">
            <option value="all">Toutes les catégories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.nom }}
            </option>
          </select>
        </div>
        
        <router-link :to="{ name: 'user.goals.create' }" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
          Nouvel Objectif
        </router-link>
      </div>
    </div>
    
    <div class="goals-list">
      <div v-if="loading" class="text-center py-8">
        <div class="spinner"></div>
        <p class="mt-2">Chargement des objectifs...</p>
      </div>
      
      <div v-else-if="filteredGoals.length === 0" class="empty-state text-center py-10 bg-gray-50 rounded-lg">
        <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <h3 class="mt-2 text-lg font-medium">Aucun objectif trouvé</h3>
        <p class="mt-1 text-gray-500">Commencez par créer votre premier objectif financier</p>
        <router-link :to="{ name: 'user.goals.create' }" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
          Créer un objectif
        </router-link>
      </div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <div v-for="goal in filteredGoals" :key="goal.id" class="goal-card bg-white rounded-lg shadow-md overflow-hidden">
          <div class="goal-header p-4" :class="getStatusClass(goal.statut)">
            <h3 class="text-lg font-semibold text-white truncate">{{ goal.titre }}</h3>
            <span class="text-sm text-white opacity-90">{{ goal.categorie.nom }}</span>
          </div>
          
          <div class="goal-body p-4">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm text-gray-600">Progression</span>
              <span class="text-sm font-medium">{{ calculateProgress(goal) }}%</span>
            </div>
            
            <div class="progress-bar h-2 bg-gray-200 rounded-full mb-4">
              <div class="h-full rounded-full" :class="getProgressColorClass(goal)" :style="{ width: `${calculateProgress(goal)}%` }"></div>
            </div>
            
            <div class="flex justify-between items-center mb-4">
              <div>
                <span class="text-xs text-gray-500">Objectif</span>
                <p class="text-lg font-semibold">{{ formatCurrency(goal.montant_cible) }}</p>
              </div>
              <div class="text-right">
                <span class="text-xs text-gray-500">Date limite</span>
                <p class="text-md">{{ formatDate(goal.date_fin) }}</p>
              </div>
            </div>
            
            <div class="goal-actions mt-2">
              <router-link :to="{ name: 'user.goals.show', params: { id: goal.id } }" class="text-blue-600 hover:text-blue-800 mr-3">
                Détails
              </router-link>
              <router-link :to="{ name: 'user.goals.edit', params: { id: goal.id } }" class="text-gray-600 hover:text-gray-800">
                Modifier
              </router-link>
            </div>
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
      goals: [],
      categories: [],
      loading: true,
      statusFilter: 'all',
      categoryFilter: 'all',
      error: null
    }
  },
  
  computed: {
    filteredGoals() {
      return this.goals.filter(goal => {
        const matchesStatus = this.statusFilter === 'all' || goal.statut === this.statusFilter;
        const matchesCategory = this.categoryFilter === 'all' || goal.categorie_id.toString() === this.categoryFilter.toString();
        return matchesStatus && matchesCategory;
      });
    }
  },
  
  created() {
    this.fetchData();
  },
  
  methods: {
    async fetchData() {
      try {
        this.loading = true;
        const [goalsResponse, categoriesResponse] = await Promise.all([
          axios.get('/api/goals'),
          axios.get('/api/categories')
        ]);
        
        this.goals = goalsResponse.data.data;
        this.categories = categoriesResponse.data.data;
      } catch (error) {
        this.error = 'Une erreur est survenue lors du chargement des données.';
        console.error('Error fetching data:', error);
      } finally {
        this.loading = false;
      }
    },
    
    calculateProgress(goal) {
      // Cette méthode devrait être adaptée en fonction de la logique de votre application
      // Exemple simple basé sur les dates (pourcentage de temps écoulé)
      const startDate = new Date(goal.date_debut);
      const endDate = new Date(goal.date_fin);
      const today = new Date();
      
      if (goal.statut === 'complété') return 100;
      if (goal.statut === 'annulé') return 0;
      
      if (today >= endDate) return 100;
      if (today <= startDate) return 0;
      
      const totalDuration = endDate - startDate;
      const elapsedTime = today - startDate;
      
      return Math.round((elapsedTime / totalDuration) * 100);
    },
    
    getStatusClass(status) {
      switch (status) {
        case 'en cours': return 'bg-blue-600';
        case 'complété': return 'bg-green-600';
        case 'annulé': return 'bg-gray-600';
        default: return 'bg-blue-600';
      }
    },
    
    getProgressColorClass(goal) {
      const progress = this.calculateProgress(goal);
      
      if (goal.statut === 'complété') return 'bg-green-500';
      if (goal.statut === 'annulé') return 'bg-gray-500';
      
      if (progress < 30) return 'bg-red-500';
      if (progress < 70) return 'bg-yellow-500';
      return 'bg-green-500';
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('fr-FR');
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
  margin: 0 auto;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>