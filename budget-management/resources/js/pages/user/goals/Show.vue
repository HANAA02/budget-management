<template>
  <div class="goal-details-container">
    <!-- En-tête avec information de navigation et actions -->
    <div class="header-section mb-6">
      <div class="flex justify-between items-center">
        <div>
          <router-link :to="{ name: 'user.goals.index' }" class="text-blue-600 flex items-center mb-2">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Retour aux objectifs
          </router-link>
          <h1 class="text-2xl font-semibold">{{ goal.titre }}</h1>
          <p class="text-gray-600">{{ goal.description }}</p>
        </div>
        
        <div class="actions">
          <button @click="updateGoalStatus('complété')" v-if="goal.statut === 'en cours'" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded mr-2">
            Marquer comme terminé
          </button>
          <button @click="updateGoalStatus('en cours')" v-if="goal.statut === 'annulé'" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded mr-2">
            Réactiver
          </button>
          <router-link :to="{ name: 'user.goals.edit', params: { id: goal.id } }" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded">
            Modifier
          </router-link>
        </div>
      </div>
      
      <!-- Statut de l'objectif -->
      <div class="status-badge mt-3">
        <span class="px-2 py-1 rounded text-sm font-medium" :class="getStatusClass(goal.statut)">
          {{ getStatusText(goal.statut) }}
        </span>
      </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="goal-content grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Panneau principal d'informations -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-xl font-medium mb-4">Progression de l'objectif</h2>
          
          <!-- Barre de progression -->
          <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
              <span class="text-sm text-gray-600">Progression actuelle</span>
              <span class="text-sm font-medium">{{ progress }}%</span>
            </div>
            
            <div class="h-4 bg-gray-200 rounded-full">
              <div class="h-full rounded-full transition-all duration-500" 
                   :class="getProgressColorClass(progress)" 
                   :style="{ width: `${progress}%` }"></div>
            </div>
          </div>
          
          <!-- Valeurs cibles et actuelles -->
          <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded">
              <span class="text-sm text-gray-500">Montant cible</span>
              <p class="text-2xl font-semibold">{{ formatCurrency(goal.montant_cible) }}</p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded">
              <span class="text-sm text-gray-500">Montant actuel</span>
              <p class="text-2xl font-semibold">{{ formatCurrency(currentAmount) }}</p>
            </div>
          </div>
          
          <!-- Informations temporelles -->
          <div class="grid grid-cols-3 gap-4 mb-4">
            <div>
              <span class="text-sm text-gray-500">Date de début</span>
              <p class="font-medium">{{ formatDate(goal.date_debut) }}</p>
            </div>
            
            <div>
              <span class="text-sm text-gray-500">Date de fin</span>
              <p class="font-medium">{{ formatDate(goal.date_fin) }}</p>
            </div>
            
            <div>
              <span class="text-sm text-gray-500">Jours restants</span>
              <p class="font-medium">{{ remainingDays }}</p>
            </div>
          </div>
        </div>
        
        <!-- Historique et activités -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-xl font-medium mb-4">Activités liées</h2>
          
          <div v-if="activities.length === 0" class="text-center py-6 text-gray-500">
            Aucune activité n'a encore été enregistrée pour cet objectif.
          </div>
          
          <div v-else class="activities-timeline">
            <div v-for="activity in activities" :key="activity.id" class="activity-item pb-4 mb-4 border-b border-gray-100 last:border-b-0">
              <div class="flex items-start">
                <div class="activity-icon mr-3 mt-1 w-8 h-8 rounded-full flex items-center justify-center"
                     :class="getActivityIconClass(activity.type)">
                  <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <!-- Icône différente selon le type d'activité -->
                    <path v-if="activity.type === 'creation'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    <path v-else-if="activity.type === 'update'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    <path v-else-if="activity.type === 'status_change'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                </div>
                
                <div class="activity-content flex-1">
                  <p class="text-sm mb-1">{{ activity.description }}</p>
                  <div class="flex justify-between">
                    <span class="text-xs text-gray-500">{{ formatDateTime(activity.date) }}</span>
                    <span v-if="activity.amount" class="text-xs font-medium">{{ formatCurrency(activity.amount) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Panneau latéral d'informations -->
      <div class="lg:col-span-1">
        <!-- Carte d'information catégorie -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-lg font-medium mb-3">Catégorie</h2>
          <div class="flex items-center">
            <div :class="`w-8 h-8 rounded-full mr-3 flex items-center justify-center ${getCategoryColor(goal.categorie)}`">
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
              </svg>
            </div>
            <span>{{ goal.categorie ? goal.categorie.nom : 'Non catégorisé' }}</span>
          </div>
        </div>
        
        <!-- Prévisions et analyses -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
          <h2 class="text-lg font-medium mb-3">Prévisions</h2>
          
          <div class="mb-4">
            <span class="text-sm text-gray-500">Tendance actuelle</span>
            <div class="flex items-center mt-1">
              <span :class="['font-medium', isOnTrack ? 'text-green-600' : 'text-red-600']">
                {{ isOnTrack ? 'Sur la bonne voie' : 'En retard' }}
              </span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                   :class="isOnTrack ? 'text-green-600' : 'text-red-600'">
                <path v-if="isOnTrack" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
              </svg>
            </div>
          </div>
          
          <div class="mb-4">
            <span class="text-sm text-gray-500">Estimation de réalisation</span>
            <p class="font-medium">{{ estimatedCompletionDate }}</p>
          </div>
          
          <div>
            <span class="text-sm text-gray-500">Montant à épargner par mois</span>
            <p class="font-medium">{{ formatCurrency(monthlyTargetAmount) }}</p>
          </div>
        </div>
        
        <!-- Actions et liens rapides -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-lg font-medium mb-3">Actions</h2>
          
          <div class="space-y-3">
            <button @click="showAddProgressModal = true" class="w-full flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Mettre à jour le progrès
            </button>
            
            <button @click="confirmDelete" class="w-full flex items-center justify-center bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
              Supprimer cet objectif
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Modal pour ajouter un progrès -->
    <div v-if="showAddProgressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-semibold mb-4">Mettre à jour le progrès</h3>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Montant</label>
          <input v-model.number="progressAmount" type="number" min="0" step="0.01" class="w-full p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500">
        </div>
        
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Description (optionnel)</label>
          <textarea v-model="progressDescription" rows="2" class="w-full p-2 border rounded focus:ring focus:ring-blue-200 focus:border-blue-500"></textarea>
        </div>
        
        <div class="flex justify-end space-x-3">
          <button @click="showAddProgressModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
            Annuler
          </button>
          <button @click="addProgress" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">
            Enregistrer
          </button>
        </div>
      </div>
    </div>
    
    <!-- Modal pour confirmer la suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-semibold mb-4">Confirmer la suppression</h3>
        
        <p class="mb-4">Êtes-vous sûr de vouloir supprimer cet objectif ? Cette action est irréversible.</p>
        
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">
            Annuler
          </button>
          <button @click="deleteGoal" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
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
      goal: {
        id: null,
        titre: '',
        description: '',
        montant_cible: 0,
        date_debut: null,
        date_fin: null,
        statut: 'en cours',
        categorie: null,
        categorie_id: null
      },
      currentAmount: 0,
      progress: 0,
      activities: [],
      showAddProgressModal: false,
      showDeleteModal: false,
      progressAmount: 0,
      progressDescription: '',
      loading: true,
      error: null
    }
  },
  
  computed: {
    remainingDays() {
      if (!this.goal.date_fin) return 0;
      
      const today = new Date();
      const endDate = new Date(this.goal.date_fin);
      
      if (today > endDate) return 0;
      
      const diffTime = Math.abs(endDate - today);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      return diffDays;
    },
    
    isOnTrack() {
      if (!this.goal.date_debut || !this.goal.date_fin || this.goal.statut !== 'en cours') {
        return false;
      }
      
      const startDate = new Date(this.goal.date_debut);
      const endDate = new Date(this.goal.date_fin);
      const today = new Date();
      
      const totalDuration = endDate - startDate;
      const elapsedTime = today - startDate;
      
      const expectedProgress = (elapsedTime / totalDuration) * 100;
      
      // Si notre progression est supérieure ou égale à celle attendue, nous sommes sur la bonne voie
      return this.progress >= expectedProgress;
    },
    
    estimatedCompletionDate() {
      if (this.progress <= 0 || this.goal.statut !== 'en cours') {
        return 'Non disponible';
      }
      
      const today = new Date();
      const startDate = new Date(this.goal.date_debut);
      const elapsedDays = Math.floor((today - startDate) / (1000 * 60 * 60 * 24));
      
      if (elapsedDays <= 0) {
        return 'Non disponible';
      }
      
      // Calculer le taux de progression quotidien
      const dailyProgressRate = this.progress / elapsedDays;
      
      if (dailyProgressRate <= 0) {
        return 'Non disponible';
      }
      
      // Estimer les jours restants
      const remainingProgress = 100 - this.progress;
      const estimatedRemainingDays = Math.ceil(remainingProgress / dailyProgressRate);
      
      // Calculer la date estimée
      const estimatedDate = new Date();
      estimatedDate.setDate(today.getDate() + estimatedRemainingDays);
      
      return this.formatDate(estimatedDate);
    },
    
    monthlyTargetAmount() {
      if (!this.goal.date_debut || !this.goal.date_fin || !this.goal.montant_cible) {
        return 0;
      }
      
      const startDate = new Date(this.goal.date_debut);
      const endDate = new Date(this.goal.date_fin);
      
      // Calculer le nombre de mois entre les dates
      const months = (endDate.getFullYear() - startDate.getFullYear()) * 12 + 
                     (endDate.getMonth() - startDate.getMonth());
      
      return months <= 0 ? this.goal.montant_cible : this.goal.montant_cible / months;
    }
  },
  
  created() {
    this.fetchGoalDetails();
  },
  
  methods: {
    async fetchGoalDetails() {
      try {
        this.loading = true;
        const goalId = this.$route.params.id;
        
        // Récupérer les détails de l'objectif
        const response = await axios.get(`/api/goals/${goalId}`);
        this.goal = response.data.data;
        
        // Récupérer les activités liées à cet objectif
        const activitiesResponse = await axios.get(`/api/goals/${goalId}/activities`);
        this.activities = activitiesResponse.data.data;
        
        // Calculer la progression actuelle
        this.calculateCurrentProgress();
      } catch (error) {
        this.error = 'Une erreur est survenue lors du chargement des données.';
        console.error('Error fetching goal details:', error);
      } finally {
        this.loading = false;
      }
    },
    
    calculateCurrentProgress() {
      // Dans un cas réel, cette valeur serait probablement retournée par l'API
      // ou calculée à partir des activités ou transactions liées à cet objectif
      
      // Exemple basique pour la démonstration
      let totalProgress = 0;
      this.activities.forEach(activity => {
        if (activity.type === 'progress' && activity.amount) {
          totalProgress += activity.amount;
        }
      });
      
      this.currentAmount = totalProgress;
      
      if (this.goal.montant_cible > 0) {
        this.progress = Math.min(100, Math.round((totalProgress / this.goal.montant_cible) * 100));
      } else {
        this.progress = 0;
      }
    },
    
    async updateGoalStatus(newStatus) {
      try {
        await axios.put(`/api/goals/${this.goal.id}/status`, {
          status: newStatus
        });
        
        this.goal.statut = newStatus;
        
        // Ajouter cette activité à la liste
        this.activities.unshift({
          id: Date.now(), // ID temporaire
          type: 'status_change',
          description: `Statut de l'objectif changé en "${this.getStatusText(newStatus)}"`,
          date: new Date(),
          amount: null
        });
        
        this.$toast.success('Le statut de l\'objectif a été mis à jour avec succès.');
      } catch (error) {
        this.$toast.error('Une erreur est survenue lors de la mise à jour du statut.');
        console.error('Error updating goal status:', error);
      }
    },
    
    async addProgress() {
      if (!this.progressAmount || this.progressAmount <= 0) {
        this.$toast.error('Veuillez entrer un montant valide.');
        return;
      }
      
      try {
        await axios.post(`/api/goals/${this.goal.id}/progress`, {
          amount: this.progressAmount,
          description: this.progressDescription
        });
        
        // Ajouter cette activité à la liste
        this.activities.unshift({
          id: Date.now(), // ID temporaire
          type: 'progress',
          description: this.progressDescription || 'Mise à jour du progrès',
          date: new Date(),
          amount: this.progressAmount
        });
        
        // Mettre à jour la progression
        this.currentAmount += this.progressAmount;
        this.progress = Math.min(100, Math.round((this.currentAmount / this.goal.montant_cible) * 100));
        
        // Si l'objectif est atteint, proposer de le marquer comme complété
        if (this.progress >= 100 && this.goal.statut === 'en cours') {
          if (confirm('Félicitations ! Vous avez atteint votre objectif. Voulez-vous le marquer comme terminé ?')) {
            this.updateGoalStatus('complété');
          }
        }
        
        this.showAddProgressModal = false;
        this.progressAmount = 0;
        this.progressDescription = '';
        
        this.$toast.success('Le progrès a été ajouté avec succès.');
      } catch (error) {
        this.$toast.error('Une erreur est survenue lors de l\'ajout du progrès.');
        console.error('Error adding progress:', error);
      }
    },
    
    confirmDelete() {
      this.showDeleteModal = true;
    },
    
    async deleteGoal() {
      try {
        await axios.delete(`/api/goals/${this.goal.id}`);
        this.showDeleteModal = false;
        this.$toast.success('L\'objectif a été supprimé avec succès.');
        this.$router.push({ name: 'user.goals.index' });
      } catch (error) {
        this.$toast.error('Une erreur est survenue lors de la suppression de l\'objectif.');
        console.error('Error deleting goal:', error);
      }
    },
    
    getStatusText(status) {
      switch (status) {
        case 'en cours': return 'En cours';
        case 'complété': return 'Terminé';
        case 'annulé': return 'Annulé';
        default: return status;
      }
    },
    
    getStatusClass(status) {
      switch (status) {
        case 'en cours': return 'bg-blue-100 text-blue-800';
        case 'complété': return 'bg-green-100 text-green-800';
        case 'annulé': return 'bg-gray-100 text-gray-800';
        default: return 'bg-blue-100 text-blue-800';
      }
    },
    
    getProgressColorClass(progress) {
      if (this.goal.statut === 'complété') return 'bg-green-500';
      if (this.goal.statut === 'annulé') return 'bg-gray-500';
      
      if (progress < 30) return 'bg-red-500';
      if (progress < 70) return 'bg-yellow-500';
      return 'bg-green-500';
    },
    
    getActivityIconClass(type) {
      switch (type) {
        case 'creation': return 'bg-green-500';
        case 'update': return 'bg-blue-500';
        case 'status_change': return 'bg-purple-500';
        case 'progress': return 'bg-yellow-500';
        default: return 'bg-gray-500';
      }
    },
    
    getCategoryColor(category) {
      if (!category) return 'bg-gray-500';
      
      // En réalité, cette couleur pourrait être stockée dans la base de données
      // Ici, nous utilisons une version simplifiée basée sur l'ID
      const colors = [
        'bg-red-500', 'bg-blue-500', 'bg-green-500', 'bg-yellow-500', 
        'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500'
      ];
      
      return colors[category.id % colors.length];
    },
    
    formatCurrency(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    },
    
    formatDate(date) {
      if (!date) return '';
      
      if (typeof date === 'string') {
        date = new Date(date);
      }
      
      return date.toLocaleDateString('fr-FR');
    },
    
    formatDateTime(date) {
      if (!date) return '';
      
      if (typeof date === 'string') {
        date = new Date(date);
      }
      
      return date.toLocaleDateString('fr-FR') + ' ' + date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }
  }
}
</script>

<style scoped>
/* Styles spécifiques si nécessaire */
</style>