<template>
  <div class="expense-summary">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">
          <i class="fas fa-chart-line mr-2"></i>
          Évolution des dépenses
        </h5>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-3">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Chargement...</span>
          </div>
        </div>
        
        <div v-else-if="!monthlyExpenses || Object.keys(monthlyExpenses).length === 0" class="text-center py-3">
          <p>Aucune donnée disponible pour le moment.</p>
        </div>
        
        <div v-else>
          <div class="summary-stats mb-4">
            <div class="row">
              <div class="col-md-4">
                <div class="stat-card">
                  <div class="stat-title">Ce mois</div>
                  <div class="stat-value">{{ formatCurrency(currentMonthExpense) }}</div>
                  <div class="stat-trend" :class="currentMonthTrend.class">
                    <i :class="['fas', currentMonthTrend.icon]"></i>
                    {{ currentMonthTrend.value }}% vs mois précédent
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="stat-card">
                  <div class="stat-title">Moyenne</div>
                  <div class="stat-value">{{ formatCurrency(averageExpense) }}</div>
                  <div class="stat-text">sur les 6 derniers mois</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="stat-card">
                  <div class="stat-title">Total</div>
                  <div class="stat-value">{{ formatCurrency(totalExpense) }}</div>
                  <div class="stat-text">sur les 6 derniers mois</div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="chart-container">
            <expense-line-chart 
              :data="monthlyExpenses"
              :height="250"
              :title="''"
              :format-currency="formatCurrency"
            />
          </div>
          
          <div class="text-center mt-3">
            <router-link to="/expenses" class="btn btn-primary">
              <i class="fas fa-list"></i> Voir toutes les dépenses
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import ExpenseLineChart from '../Charts/ExpenseLineChart.vue';

export default {
  name: 'ExpenseSummary',
  components: {
    ExpenseLineChart
  },
  props: {
    monthlyExpenses: {
      type: Object,
      required: false,
      default: null
    }
  },
  setup(props) {
    const store = useStore();
    const loading = ref(false);
    const localMonthlyExpenses = ref(props.monthlyExpenses || null);
    
    // Charger les données si elles ne sont pas fournies par les props
    onMounted(async () => {
      if (!props.monthlyExpenses) {
        loading.value = true;
        try {
          await store.dispatch('expenses/fetchMonthlyExpenses', 6);
          localMonthlyExpenses.value = store.getters['expenses/monthlyExpenses'];
        } catch (error) {
          console.error('Erreur lors du chargement des dépenses mensuelles:', error);
        } finally {
          loading.value = false;
        }
      }
    });
    
    // Utiliser les données des props ou les données locales
    const activeMonthlyExpenses = computed(() => {
      return props.monthlyExpenses || localMonthlyExpenses.value || {};
    });
    
    // Calculer les dépenses du mois en cours
    const currentMonthExpense = computed(() => {
      const months = Object.keys(activeMonthlyExpenses.value);
      if (months.length === 0) return 0;
      
      return activeMonthlyExpenses.value[months[months.length - 1]] || 0;
    });
    
    // Calculer les dépenses du mois précédent
    const previousMonthExpense = computed(() => {
      const months = Object.keys(activeMonthlyExpenses.value);
      if (months.length < 2) return 0;
      
      return activeMonthlyExpenses.value[months[months.length - 2]] || 0;
    });
    
    // Calculer la tendance par rapport au mois précédent
    const currentMonthTrend = computed(() => {
      if (previousMonthExpense.value === 0) {
        return { value: 0, class: '', icon: 'fa-minus' };
      }
      
      const difference = currentMonthExpense.value - previousMonthExpense.value;
      const percentage = Math.round((difference / previousMonthExpense.value) * 100);
      
      if (percentage > 0) {
        return { value: percentage, class: 'text-danger', icon: 'fa-arrow-up' };
      } else if (percentage < 0) {
        return { value: Math.abs(percentage), class: 'text-success', icon: 'fa-arrow-down' };
      } else {
        return { value: 0, class: 'text-muted', icon: 'fa-minus' };
      }
    });
    
    // Calculer la moyenne des dépenses sur les 6 derniers mois
    const averageExpense = computed(() => {
      const values = Object.values(activeMonthlyExpenses.value);
      if (values.length === 0) return 0;
      
      const sum = values.reduce((total, value) => total + value, 0);
      return sum / values.length;
    });
    
    // Calculer le total des dépenses sur les 6 derniers mois
    const totalExpense = computed(() => {
      const values = Object.values(activeMonthlyExpenses.value);
      if (values.length === 0) return 0;
      
      return values.reduce((total, value) => total + value, 0);
    });
    
    // Formatage d'un montant en devise
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    };
    
    return {
      loading,
      monthlyExpenses: activeMonthlyExpenses,
      currentMonthExpense,
      previousMonthExpense,
      currentMonthTrend,
      averageExpense,
      totalExpense,
      formatCurrency
    };
  }
};
</script>

<style scoped>
.expense-summary {
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

.summary-stats {
  margin: 1rem 0;
}

.stat-card {
  background-color: #f8f9fa;
  border-radius: 5px;
  padding: 1rem;
  text-align: center;
  height: 100%;
}

.stat-title {
  font-size: 0.9rem;
  color: #6c757d;
  margin-bottom: 0.5rem;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.stat-trend {
  font-size: 0.8rem;
  font-weight: 500;
}

.stat-text {
  font-size: 0.8rem;
  color: #6c757d;
}

.chart-container {
  height: 250px;
  position: relative;
}
</style>