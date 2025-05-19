<template>
  <div class="goal-progress">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h4>{{ goal.titre }}</h4>
      </div>
      <div class="card-body">
        <div class="goal-details mb-4">
          <div class="row">
            <div class="col-md-6">
              <p class="mb-1">
                <strong>Catégorie:</strong> 
                <span class="badge rounded-pill" :style="{ backgroundColor: getCategoryColor() }">
                  {{ goal.categorie.nom }}
                </span>
              </p>
              <p class="mb-1">
                <strong>Objectif:</strong> {{ formatMontant(goal.montant_cible) }}
              </p>
              <p class="mb-1">
                <strong>Progression actuelle:</strong> {{ formatMontant(currentAmount) }}
              </p>
            </div>
            <div class="col-md-6">
              <p class="mb-1">
                <strong>Date de début:</strong> {{ formatDate(goal.date_debut) }}
              </p>
              <p class="mb-1">
                <strong>Date de fin:</strong> {{ formatDate(goal.date_fin) }}
              </p>
              <p class="mb-1">
                <strong>Statut:</strong> 
                <span class="badge" :class="getStatusBadgeClass(goal.statut)">
                  {{ goal.statut }}
                </span>
              </p>
            </div>
          </div>
          
          <div v-if="goal.description" class="mt-3">
            <p><strong>Description:</strong></p>
            <p class="description-text">{{ goal.description }}</p>
          </div>
        </div>
        
        <div class="progress-container">
          <div class="progress mb-2" style="height: 25px;">
            <div 
              class="progress-bar progress-bar-striped" 
              :class="getProgressBarClass()"
              role="progressbar" 
              :style="{ width: `${progressPercentage}%` }" 
              :aria-valuenow="progressPercentage" 
              aria-valuemin="0" 
              aria-valuemax="100"
            >
              {{ progressPercentage }}%
            </div>
          </div>
          
          <div class="progress-labels d-flex justify-content-between">
            <span>0</span>
            <span>{{ formatMontant(goal.montant_cible) }}</span>
          </div>
        </div>
        
        <div class="time-progress mt-4">
          <h5>Progression temporelle</h5>
          <div class="progress mb-2" style="height: 20px;">
            <div 
              class="progress-bar bg-info" 
              role="progressbar" 
              :style="{ width: `${timeProgressPercentage}%` }" 
              :aria-valuenow="timeProgressPercentage" 
              aria-valuemin="0" 
              aria-valuemax="100"
            >
              {{ timeProgressPercentage }}%
            </div>
          </div>
          
          <div class="progress-labels d-flex justify-content-between">
            <span>{{ formatDate(goal.date_debut) }}</span>
            <span>{{ formatDate(goal.date_fin) }}</span>
          </div>
          
          <div class="time-remaining mt-2 text-center">
            <span v-if="daysRemaining > 0" class="badge bg-info">
              {{ daysRemaining }} jour{{ daysRemaining > 1 ? 's' : '' }} restant{{ daysRemaining > 1 ? 's' : '' }}
            </span>
            <span v-else-if="daysRemaining === 0" class="badge bg-warning">
              Dernier jour
            </span>
            <span v-else class="badge bg-danger">
              Objectif expiré
            </span>
          </div>
        </div>
        
        <div class="goal-metrics mt-4">
          <div class="row">
            <div class="col-md-4">
              <div class="metric-card text-center p-3 border rounded">
                <h6>Montant restant</h6>
                <p class="metric-value">{{ formatMontant(remainingAmount) }}</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="metric-card text-center p-3 border rounded">
                <h6>Montant quotidien requis</h6>
                <p class="metric-value">{{ formatMontant(dailyAmountNeeded) }}</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="metric-card text-center p-3 border rounded">
                <h6>Prévision d'atteinte</h6>
                <p class="metric-value" :class="{'text-success': isOnTrack, 'text-danger': !isOnTrack}">
                  {{ isOnTrack ? 'En bonne voie' : 'En retard' }}
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="actions mt-4 d-flex justify-content-between">
          <button 
            class="btn btn-outline-primary" 
            @click="$emit('edit-goal', goal)"
          >
            <i class="fas fa-edit"></i> Modifier
          </button>
          <button 
            v-if="goal.statut === 'en cours'"
            class="btn btn-success" 
            @click="markAsAchieved"
          >
            <i class="fas fa-check-circle"></i> Marquer comme atteint
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GoalProgress',
  props: {
    goal: {
      type: Object,
      required: true
    },
    currentAmount: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      categoryColors: {
        1: '#ff6b6b', // Logement
        2: '#48dbfb', // Alimentation
        3: '#1dd1a1', // Santé
        4: '#feca57', // Transport
        5: '#5f27cd', // Loisirs
        6: '#ff9ff3', // Habillement
        7: '#54a0ff', // Éducation
        8: '#ff9f43', // Épargne
        9: '#00d2d3', // Autres
      }
    };
  },
  computed: {
    progressPercentage() {
      const percentage = (this.currentAmount / this.goal.montant_cible) * 100;
      return Math.min(Math.max(0, Math.round(percentage)), 100);
    },
    timeProgressPercentage() {
      const startDate = new Date(this.goal.date_debut);
      const endDate = new Date(this.goal.date_fin);
      const today = new Date();
      
      const totalDuration = endDate - startDate;
      const elapsedDuration = today - startDate;
      
      if (totalDuration <= 0) return 0;
      
      const percentage = (elapsedDuration / totalDuration) * 100;
      return Math.min(Math.max(0, Math.round(percentage)), 100);
    },
    remainingAmount() {
      return Math.max(0, this.goal.montant_cible - this.currentAmount);
    },
    daysRemaining() {
      const today = new Date();
      const endDate = new Date(this.goal.date_fin);
      
      // Différence en millisecondes
      const diffMs = endDate - today;
      
      // Convertir en jours et arrondir
      const diffDays = Math.ceil(diffMs / (1000 * 60 * 60 * 24));
      
      return diffDays;
    },
    dailyAmountNeeded() {
      if (this.daysRemaining <= 0) return 0;
      return this.remainingAmount / this.daysRemaining;
    },
    isOnTrack() {
      // Comparaison entre la progression financière et la progression temporelle
      return this.progressPercentage >= this.timeProgressPercentage;
    }
  },
  methods: {
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatMontant(montant) {
      return new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(montant);
    },
    getCategoryColor() {
      return this.categoryColors[this.goal.categorie_id] || '#aaa';
    },
    getStatusBadgeClass(status) {
      switch (status) {
        case 'en cours':
          return 'bg-primary';
        case 'atteint':
          return 'bg-success';
        case 'abandonné':
          return 'bg-danger';
        default:
          return 'bg-secondary';
      }
    },
    getProgressBarClass() {
      if (this.progressPercentage >= 100) {
        return 'bg-success';
      } else if (this.progressPercentage >= 75) {
        return 'bg-info';
      } else if (this.progressPercentage >= 50) {
        return 'bg-primary';
      } else if (this.progressPercentage >= 25) {
        return 'bg-warning';
      } else {
        return 'bg-danger';
      }
    },
    markAsAchieved() {
      axios.put(`/api/goals/${this.goal.id}/status`, { statut: 'atteint' })
        .then(response => {
          this.$emit('status-updated', response.data.data);
        })
        .catch(error => {
          console.error('Erreur lors de la mise à jour du statut:', error);
        });
    }
  }
};
</script>

<style scoped>
.goal-progress {
  max-width: 800px;
  margin: 0 auto;
}
.description-text {
  font-style: italic;
  color: #6c757d;
  background-color: #f8f9fa;
  padding: 10px;
  border-radius: 5px;
}
.progress-labels {
  font-size: 0.85rem;
  color: #6c757d;
}
.metric-card {
  background-color: #f8f9fa;
  transition: all 0.3s;
}
.metric-card:hover {
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  transform: translateY(-3px);
}
.metric-value {
  font-size: 1.2rem;
  font-weight: 600;
  margin-bottom: 0;
}
.badge {
  font-size: 0.85em;
  padding: 0.35em 0.65em;
}
</style>