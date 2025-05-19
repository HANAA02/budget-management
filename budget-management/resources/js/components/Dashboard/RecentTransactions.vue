<template>
  <div class="recent-transactions">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
          <i class="fas fa-receipt mr-2"></i>
          Transactions récentes
        </h5>
        <div class="card-header-actions">
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="transactionTypeDropdown" data-toggle="dropdown">
              {{ transactionType === 'all' ? 'Tout' : transactionType === 'expenses' ? 'Dépenses' : 'Revenus' }}
            </button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="#" @click.prevent="transactionType = 'all'">Tout</a>
              <a class="dropdown-item" href="#" @click.prevent="transactionType = 'expenses'">Dépenses</a>
              <a class="dropdown-item" href="#" @click.prevent="transactionType = 'incomes'">Revenus</a>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div v-if="loading" class="text-center py-3">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Chargement...</span>
          </div>
        </div>
        
        <div v-else-if="filteredTransactions.length === 0" class="text-center py-3">
          <p>Aucune transaction récente à afficher.</p>
        </div>
        
        <div v-else class="transaction-list">
          <div 
            v-for="transaction in filteredTransactions" 
            :key="transaction.id"
            class="transaction-item"
            :class="{ 'income': transaction.type === 'income', 'expense': transaction.type === 'expense' }"
          >
            <div class="transaction-icon" :class="{ 'income': transaction.type === 'income', 'expense': transaction.type === 'expense' }">
              <i :class="['fas', transaction.type === 'income' ? 'fa-coins' : 'fa-shopping-cart']"></i>
            </div>
            <div class="transaction-details">
              <div class="transaction-description">
                {{ transaction.description }}
              </div>
              <div class="transaction-meta">
                <span class="transaction-category">
                  <i :class="['fas', transaction.icon, 'mr-1']" v-if="transaction.icon"></i>
                  {{ transaction.category }}
                </span>
                <span class="transaction-date">
                  {{ formatDate(transaction.date) }}
                </span>
              </div>
            </div>
            <div class="transaction-amount" :class="{ 'income': transaction.type === 'income', 'expense': transaction.type === 'expense' }">
              {{ transaction.type === 'income' ? '+' : '-' }} {{ formatCurrency(transaction.amount) }}
            </div>
          </div>
        </div>
      </div>
      <div class="card-footer text-center">
        <router-link :to="transactionType === 'incomes' ? '/incomes' : '/expenses'" class="btn btn-sm btn-primary">
          <i class="fas fa-list"></i> Voir toutes les transactions
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue';
import { useStore } from 'vuex';

export default {
  name: 'RecentTransactions',
  props: {
    limit: {
      type: Number,
      default: 5
    }
  },
  setup(props) {
    const store = useStore();
    const loading = ref(true);
    const transactionType = ref('all'); // 'all', 'expenses', 'incomes'
    
    // Charger les transactions récentes
    onMounted(async () => {
      try {
        // Charger les dépenses récentes
        await store.dispatch('expenses/fetchRecentExpenses', props.limit);
        
        // Charger les revenus récents
        await store.dispatch('incomes/fetchRecentIncomes', props.limit);
      } catch (error) {
        console.error('Erreur lors du chargement des transactions récentes:', error);
      } finally {
        loading.value = false;
      }
    });
    
    // Observer les changements du type de transaction
    watch(transactionType, () => {
      // Recharger les données si nécessaire
      if (transactionType.value === 'expenses' || transactionType.value === 'all') {
        if (!store.getters['expenses/recentExpenses'] || store.getters['expenses/recentExpenses'].length === 0) {
          store.dispatch('expenses/fetchRecentExpenses', props.limit);
        }
      }
      
      if (transactionType.value === 'incomes' || transactionType.value === 'all') {
        if (!store.getters['incomes/recentIncomes'] || store.getters['incomes/recentIncomes'].length === 0) {
          store.dispatch('incomes/fetchRecentIncomes', props.limit);
        }
      }
    });
    
    // Récupérer les dépenses récentes
    const recentExpenses = computed(() => {
      const expenses = store.getters['expenses/recentExpenses'] || [];
      
      return expenses.map(expense => ({
        id: 'expense-' + expense.id,
        type: 'expense',
        description: expense.description,
        category: expense.categorieBudget?.categorie?.nom || 'Non catégorisé',
        icon: expense.categorieBudget?.categorie?.icone || 'fa-question',
        amount: expense.montant,
        date: expense.date_depense,
        budgetId: expense.categorieBudget?.budget_id,
        budgetName: expense.categorieBudget?.budget?.nom
      }));
    });
    
    // Récupérer les revenus récents
    const recentIncomes = computed(() => {
      const incomes = store.getters['incomes/recentIncomes'] || [];
      
      return incomes.map(income => ({
        id: 'income-' + income.id,
        type: 'income',
        description: income.source,
        category: income.compte?.nom || 'Non spécifié',
        icon: 'fa-university',
        amount: income.montant,
        date: income.date_perception,
        accountId: income.compte_id,
        accountName: income.compte?.nom
      }));
    });
    
    // Combiner et filtrer les transactions
    const filteredTransactions = computed(() => {
      let result = [];
      
      if (transactionType.value === 'all' || transactionType.value === 'expenses') {
        result = result.concat(recentExpenses.value);
      }
      
      if (transactionType.value === 'all' || transactionType.value === 'incomes') {
        result = result.concat(recentIncomes.value);
      }
      
      // Trier par date décroissante
      return result.sort((a, b) => new Date(b.date) - new Date(a.date)).slice(0, props.limit);
    });
    
    // Formatage d'une date
    const formatDate = (dateString) => {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short'
      });
    };
    
    // Formatage d'un montant en devise
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      }).format(amount);
    };
    
    return {
      loading,
      transactionType,
      filteredTransactions,
      formatDate,
      formatCurrency
    };
  }
};
</script>

<style scoped>
.recent-transactions {
  height: 100%;
}

.card {
  height: 100%;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
}

.card-header {
  background-color: #f8f9fa;
  padding: 0.75rem 1rem;
}

.card-header h5 {
  font-size: 1rem;
  font-weight: 600;
  color: #2c3e50;
}

.card-body {
  padding: 0;
  overflow-y: auto;
  flex: 1;
}

.transaction-list {
  max-height: 100%;
}

.transaction-item {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #e9ecef;
  transition: background-color 0.2s;
}

.transaction-item:hover {
  background-color: #f8f9fa;
}

.transaction-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background-color: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.75rem;
  color: #6c757d;
}

.transaction-icon.income {
  background-color: rgba(40, 167, 69, 0.2);
  color: #28a745;
}

.transaction-icon.expense {
  background-color: rgba(220, 53, 69, 0.2);
  color: #dc3545;
}

.transaction-details {
  flex: 1;
  min-width: 0;
}

.transaction-description {
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  margin-bottom: 0.25rem;
}

.transaction-meta {
  display: flex;
  font-size: 0.75rem;
  color: #6c757d;
}

.transaction-category {
  margin-right: 0.5rem;
  padding-right: 0.5rem;
  border-right: 1px solid #dee2e6;
  max-width: 50%;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.transaction-date {
  white-space: nowrap;
}

.transaction-amount {
  font-weight: 600;
  white-space: nowrap;
  margin-left: 0.75rem;
  color: #dc3545;
}

.transaction-amount.income {
  color: #28a745;
}

.card-footer {
  background-color: #f8f9fa;
  border-top: 1px solid #e9ecef;
  padding: 0.5rem 1rem;
}
</style>