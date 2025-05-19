<template>
  <div class="budget-summary">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">
          <i class="fas fa-money-bill-wave mr-2"></i>
          {{ budget ? budget.nom : 'Aucun budget actif' }}
        </h5>
      </div>
      <div class="card-body">
        <div v-if="!budget" class="text-center py-3">
          <p>Vous n'avez pas de budget actif pour la période en cours.</p>
          <router-link to="/budgets/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Créer un nouveau budget
          </router-link>
        </div>
        
        <div v-else>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <div>
                <strong>Période :</strong>
                {{ formatDate(budget.date_debut) }} - {{ formatDate(budget.date_fin) }}
              </div>
              <div>
                <strong>Montant total :</strong>
                {{ formatCurrency(budget.montant_total) }}
              </div>
            </div>
          </div>
          
          <div class="global-progress mb-4">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong>Budget dépensé :</strong>
                {{ formatCurrency(totalExpenses) }} / {{ formatCurrency(budget.montant_total) }}
              </div>
              <div>
                <span :class="getPercentageClass(percentageUsed)">{{ Math.round(percentageUsed) }}%</span>
              </div>
            </div>
            <div class="progress" style="height: 10px;">
              <div 
                class="progress-bar" 
                role="progressbar" 
                :style="{ width: percentageUsed + '%' }"
                :class="getProgressBarClass(percentageUsed)"
              ></div>
            </div>
            <div class="small text-muted mt-1">
              <strong>Reste à dépenser :</strong> {{ formatCurrency(budget.montant_total - totalExpenses) }}
            </div>
          </div>
          
          <div class="top-categories mb-4">
            <h6 class="mb-3">Principales catégories</h6>
            <div class="category-list">
              <div 
                v-for="category in sortedCategories.slice(0, 4)" 
                :key="category.id"
                class="category-item mb-2"
              >
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div>
                    <i :class="['fas', category.icone, 'mr-2']"></i>
                    {{ category.nom }}
                  </div>
                  <div>
                    <span :class="getPercentageClass(category.pourcentage_utilise)">
                      {{ Math.round(category.pourcentage_utilise) }}%
                    </span>
                  </div>
                </div>
                <div class="progress" style="height: 8px;">
                  <div 
                    class="progress-bar" 
                    role="progressbar" 
                    :style="{ width: category.pourcentage_utilise + '%' }"
                    :class="getProgressBarClass(category.pourcentage_utilise)"
                  ></div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="chart-container mb-3" v-if="pieChartData">
            <budget-pie-chart 
              :data="pieChartData" 
              :height="200"
              :title="''"
              :format-currency="formatCurrency"
            />
          </div>
          
          <div class="text-center mt-3">
            <router-link :to="'/budgets/' + budget.id" class="btn btn-primary">
              <i class="fas fa-eye"></i> Voir les détails
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue';
import BudgetPieChart from '../Charts/BudgetPieChart.vue';

export default {
  name: 'BudgetSummary',
  components: {
    BudgetPieChart
  },
  props: {
    budget: {
      type: Object,
      required: false,
      default: null
    },
    categories: {
      type: Array,
      required: false,
      default: () => []
    }
  },
  setup(props) {
    // Calcul du total des dépenses
    const totalExpenses = computed(() => {
      if (!props.categories || props.categories.length === 0) {
        return 0;
      }
      
      return props.categories.reduce((sum, category) => {
        return sum + (category.total_depenses || 0);
      }, 0);
    });
    
    // Calcul du pourcentage utilisé
    const percentageUsed = computed(() => {
      if (!props.budget || props.budget.montant_total === 0) {
        return 0;
      }
      
      return (totalExpenses.value / props.budget.montant_total) * 100;
    });
    
    // Tri des catégories par montant dépensé
    const sortedCategories = computed(() => {
      if (!props.categories) {
        return [];
      }
      
      return [...props.categories].sort((a, b) => b.total_depenses - a.total_depenses);
    });
    
    // Données pour le graphique en camembert
    const pieChartData = computed(() => {
      if (!props.categories || props.categories.length === 0) {
        return null;
      }
      
      return props.categories.map(category => ({
        label: category.nom,
        value: category.pourcentage,
        amount: category.total_depenses
      }));
    });
    
    // Classe CSS pour le pourcentage
    const getPercentageClass = (percentage) => {
      if (percentage >= 90) {
        return 'text-danger font-weight-bold';
      } else if (percentage >= 70) {
        return 'text-warning font-weight-bold';
      } else {
        return 'text-success font-weight-bold';
      }
    };
    
    // Classe CSS pour la barre de progression
    const getProgressBarClass = (percentage) => {
      if (percentage >= 90) {
        return 'bg-danger';
      } else if (percentage >= 70) {
        return 'bg-warning';
      } else {
        return 'bg-success';
      }
    };
    
    // Formatage d'une date
    const formatDate = (dateString) => {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
      });
    };
    
    // Formatage d'un montant en devise
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    };
    
    return {
      totalExpenses,
      percentageUsed,
      sortedCategories,
      pieChartData,
      getPercentageClass,
      getProgressBarClass,
      formatDate,
      formatCurrency
    };
  }
};
</script>

<style scoped>
.budget-summary {
  height: 100%;
}

.card {
  height: 100%;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.card-header {
  background-color: #f8f9fa;
}

.card-header h5 {
  font-size: 1rem;
  font-weight: 600;
  color: #2c3e50;
}

.category-item {
  padding: 0.5rem;
  border-radius: 5px;
  background-color: #f8f9fa;
}

.chart-container {
  height: 200px;
  position: relative;
}

h6 {
  font-weight: 600;
  color: #2c3e50;
  font-size: 0.9rem;
}
</style>