<template>
  <div class="category-percentages">
    <h6 class="mb-3">Répartition par catégorie</h6>
    
    <div class="row">
      <div 
        v-for="category in categoriesWithPercentage" 
        :key="category.id"
        class="col-md-6 mb-3"
      >
        <div 
          class="category-item" 
          :class="{ 'warning': category.percentage > category.pourcentage_defaut + 10 }"
        >
          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="category-name">
              <i :class="['fas', category.icone, 'mr-2']"></i>
              {{ category.nom }}
            </div>
            <div class="category-value">
              <strong>{{ formatCurrency(category.amount) }}</strong>
              <span class="badge ml-1" :class="getBadgeClass(category)">{{ category.percentage }}%</span>
            </div>
          </div>
          
          <div class="progress" style="height: 8px;">
            <div 
              class="progress-bar" 
              role="progressbar" 
              :style="{ width: category.percentage + '%' }"
              :class="getProgressBarClass(category)"
            ></div>
          </div>
          
          <div class="d-flex justify-content-between mt-2 small text-muted">
            <div>Défaut: {{ category.pourcentage_defaut }}%</div>
            <div>{{ category.percentage }}% du budget</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue';

export default {
  name: 'CategoryPercentages',
  props: {
    categories: {
      type: Array,
      required: true
    },
    budget: {
      type: Object,
      required: true
    },
    allocations: {
      type: Array,
      required: true
    }
  },
  setup(props) {
    // Combiner les catégories avec leurs pourcentages
    const categoriesWithPercentage = computed(() => {
      return props.categories.map(category => {
        const allocation = props.allocations.find(a => a.categorie_id === category.id);
        
        if (!allocation) {
          return {
            ...category,
            percentage: 0,
            amount: 0
          };
        }
        
        return {
          ...category,
          percentage: allocation.pourcentage,
          amount: (allocation.pourcentage / 100) * props.budget.montant_total
        };
      }).sort((a, b) => b.percentage - a.percentage); // Trier par pourcentage décroissant
    });
    
    // Déterminer la classe de badge en fonction du pourcentage
    const getBadgeClass = (category) => {
      if (category.percentage > category.pourcentage_defaut + 10) {
        return 'badge-warning';
      } else if (category.percentage < category.pourcentage_defaut - 10) {
        return 'badge-info';
      } else {
        return 'badge-primary';
      }
    };
    
    // Déterminer la classe de la barre de progression en fonction du pourcentage
    const getProgressBarClass = (category) => {
      if (category.percentage > category.pourcentage_defaut + 10) {
        return 'bg-warning';
      } else if (category.percentage < category.pourcentage_defaut - 10) {
        return 'bg-info';
      } else {
        return 'bg-primary';
      }
    };
    
    // Formater un montant en devise
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    };
    
    return {
      categoriesWithPercentage,
      getBadgeClass,
      getProgressBarClass,
      formatCurrency
    };
  }
};
</script>

<style scoped>
.category-percentages {
  margin-bottom: 2rem;
}

.category-item {
  padding: 1rem;
  border-radius: 5px;
  background-color: #fff;
  border: 1px solid #e9ecef;
  transition: all 0.2s;
}

.category-item:hover {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.category-item.warning {
  border-left: 3px solid #f39c12;
}

.category-name {
  font-weight: 500;
}

.category-value {
  white-space: nowrap;
}

h6 {
  font-weight: 600;
  color: #2c3e50;
}
</style>