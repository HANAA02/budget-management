<template>
  <div class="budget-allocation">
    <div class="row mb-4">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Répartition du budget</h5>
          </div>
          <div class="card-body">
            <p class="card-text">
              Faites glisser les curseurs pour ajuster l'allocation de votre budget "{{ budget.nom }}" pour un montant total de {{ formatCurrency(budget.montant_total) }}.
            </p>
            
            <div class="alert alert-info">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <strong>Total alloué :</strong> {{ formatCurrency(totalAllocated) }} ({{ percentageAllocated }}%)
                </div>
                <div>
                  <strong>Reste à allouer :</strong> {{ formatCurrency(remainingAmount) }} ({{ remainingPercentage }}%)
                </div>
              </div>
              <div class="progress mt-2">
                <div 
                  class="progress-bar" 
                  role="progressbar" 
                  :style="{ width: percentageAllocated + '%' }"
                  :class="{
                    'bg-danger': percentageAllocated > 100,
                    'bg-warning': percentageAllocated === 100,
                    'bg-success': percentageAllocated < 100
                  }"
                >
                  {{ percentageAllocated }}%
                </div>
              </div>
            </div>
            
            <div class="category-list mt-4">
              <div 
                v-for="(category, index) in categoriesWithAllocation" 
                :key="category.id"
                class="category-item mb-4"
              >
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="category-info">
                    <i :class="['fas', category.icone, 'mr-2']"></i>
                    <strong>{{ category.nom }}</strong>
                  </div>
                  <div class="category-amounts">
                    <span class="badge badge-primary">{{ category.percentage }}%</span>
                    <span class="ml-2">{{ formatCurrency(category.amount) }}</span>
                  </div>
                </div>
                
                <div class="slider-container d-flex align-items-center">
                  <input 
                    type="range"
                    class="form-control-range flex-grow-1"
                    min="0"
                    max="100"
                    step="1"
                    :value="category.percentage"
                    @input="updateCategoryPercentage(index, $event.target.value)"
                  >
                  <div class="percentage-input ml-3">
                    <div class="input-group input-group-sm">
                      <input 
                        type="number"
                        class="form-control"
                        min="0"
                        max="100"
                        step="1"
                        :value="category.percentage"
                        @input="updateCategoryPercentage(index, $event.target.value)"
                      >
                      <div class="input-group-append">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="mt-4 border-top pt-4" v-if="unusedCategories.length > 0">
              <h6>Ajouter d'autres catégories</h6>
              <div class="row">
                <div class="col-md-6" v-for="category in unusedCategories" :key="category.id">
                  <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-2">
                    <div>
                      <i :class="['fas', category.icone, 'mr-2']"></i>
                      {{ category.nom }}
                    </div>
                    <button 
                      class="btn btn-sm btn-outline-primary"
                      @click="addCategory(category)"
                    >
                      Ajouter
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">Visualisation</h5>
          </div>
          <div class="card-body">
            <div class="chart-container">
              <canvas ref="pieChart"></canvas>
            </div>
          </div>
        </div>
        
        <div class="card">
          <div class="card-header bg-light">
            <h5 class="mb-0">Détails du budget</h5>
          </div>
          <div class="card-body">
            <ul class="list-group list-group-flush">
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>Budget</span>
                <strong>{{ budget.nom }}</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>Période</span>
                <strong>{{ formatDate(budget.date_debut) }} - {{ formatDate(budget.date_fin) }}</strong>
              </li>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>Montant total</span>
                <strong>{{ formatCurrency(budget.montant_total) }}</strong>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    
    <div class="d-flex justify-content-between">
      <button class="btn btn-secondary" @click="cancelAllocation">
        <i class="fas fa-times"></i> Annuler
      </button>
      <button 
        class="btn btn-primary" 
        @click="saveAllocation"
        :disabled="isSaving || percentageAllocated !== 100"
      >
        <i class="fas fa-save"></i> Enregistrer la répartition
      </button>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted, watch } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import Chart from 'chart.js/auto';

export default {
  name: 'BudgetAllocation',
  props: {
    budget: {
      type: Object,
      required: true
    },
    initialAllocations: {
      type: Array,
      default: () => []
    }
  },
  setup(props) {
    const store = useStore();
    const router = useRouter();
    
    // Références pour le graphique
    const pieChart = ref(null);
    let chart = null;
    
    // État local pour les catégories et leurs allocations
    const categories = reactive([]);
    const allocations = reactive([]);
    const isSaving = ref(false);
    
    // Récupérer toutes les catégories
    const allCategories = computed(() => {
      return store.getters['categories/categories'] || [];
    });
    
    // Catégories avec allocation
    const categoriesWithAllocation = computed(() => {
      return allocations.map(allocation => {
        const category = categories.find(c => c.id === allocation.categoryId);
        return {
          ...category,
          percentage: allocation.percentage,
          amount: (allocation.percentage / 100) * props.budget.montant_total
        };
      });
    });
    
    // Catégories non utilisées
    const unusedCategories = computed(() => {
      const usedCategoryIds = allocations.map(a => a.categoryId);
      return categories.filter(category => !usedCategoryIds.includes(category.id));
    });
    
    // Calculer le montant total alloué
    const totalAllocated = computed(() => {
      return allocations.reduce((sum, allocation) => {
        return sum + ((allocation.percentage / 100) * props.budget.montant_total);
      }, 0);
    });
    
    // Pourcentage alloué
    const percentageAllocated = computed(() => {
      return Math.round(allocations.reduce((sum, allocation) => sum + Number(allocation.percentage), 0));
    });
    
    // Montant restant à allouer
    const remainingAmount = computed(() => {
      return props.budget.montant_total - totalAllocated.value;
    });
    
    // Pourcentage restant
    const remainingPercentage = computed(() => {
      return Math.max(0, 100 - percentageAllocated.value);
    });
    
    // Charger les catégories et les allocations initiales
    onMounted(async () => {
      if (allCategories.value.length === 0) {
        await store.dispatch('categories/fetchCategories');
      }
      
      // Copier toutes les catégories dans l'état local
      categories.push(...allCategories.value);
      
      // Initialiser les allocations
      if (props.initialAllocations.length > 0) {
        props.initialAllocations.forEach(allocation => {
          allocations.push({
            categoryId: allocation.categorie_id,
            percentage: allocation.pourcentage
          });
        });
      } else {
        // Si pas d'allocation initiale, utiliser les pourcentages par défaut des catégories
        categories.forEach(category => {
          allocations.push({
            categoryId: category.id,
            percentage: category.pourcentage_defaut
          });
        });
      }
      
      // Initialiser le graphique
      initChart();
    });
    
    // Observer les changements des allocations pour mettre à jour le graphique
    watch(categoriesWithAllocation, () => {
      updateChart();
    }, { deep: true });
    
    // Initialiser le graphique en camembert
    const initChart = () => {
      const ctx = pieChart.value.getContext('2d');
      
      chart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: categoriesWithAllocation.value.map(c => c.nom),
          datasets: [{
            data: categoriesWithAllocation.value.map(c => c.percentage),
            backgroundColor: generateColors(categoriesWithAllocation.value.length),
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom'
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const value = context.raw;
                  const total = context.dataset.data.reduce((a, b) => a + b, 0);
                  const percentage = Math.round((value / total) * 100);
                  return `${percentage}% (${formatCurrency((percentage / 100) * props.budget.montant_total)})`;
                }
              }
            }
          }
        }
      });
    };
    
    // Mettre à jour le graphique
    const updateChart = () => {
      if (!chart) return;
      
      chart.data.labels = categoriesWithAllocation.value.map(c => c.nom);
      chart.data.datasets[0].data = categoriesWithAllocation.value.map(c => c.percentage);
      chart.data.datasets[0].backgroundColor = generateColors(categoriesWithAllocation.value.length);
      
      chart.update();
    };
    
    // Générer des couleurs pour le graphique
    const generateColors = (count) => {
      const colors = [
        '#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6',
        '#1abc9c', '#d35400', '#34495e', '#16a085', '#c0392b',
        '#8e44ad', '#2980b9', '#27ae60', '#f1c40f', '#e67e22'
      ];
      
      return Array(count).fill().map((_, i) => colors[i % colors.length]);
    };
    
    // Mettre à jour le pourcentage d'une catégorie
    const updateCategoryPercentage = (index, value) => {
      // Convertir en nombre
      const newValue = Math.max(0, Math.min(100, Number(value)));
      
      // Calculer la différence avec l'ancienne valeur
      const oldValue = allocations[index].percentage;
      const difference = newValue - oldValue;
      
      // Mettre à jour la valeur
      allocations[index].percentage = newValue;
      
      // Ajuster automatiquement les autres catégories si nécessaire
      if (percentageAllocated.value !== 100) {
        // Si on dépasse 100%, réduire proportionnellement les autres catégories
        if (percentageAllocated.value > 100) {
          const excess = percentageAllocated.value - 100;
          const otherSum = allocations.reduce((sum, a, i) => {
            return i !== index ? sum + Number(a.percentage) : sum;
          }, 0);
          
          if (otherSum > 0) {
            allocations.forEach((allocation, i) => {
              if (i !== index) {
                const proportion = allocation.percentage / otherSum;
                allocation.percentage = Math.max(0, allocation.percentage - excess * proportion);
              }
            });
          }
        }
        // Si on est en dessous de 100%, augmenter proportionnellement les autres catégories
        else if (percentageAllocated.value < 100) {
          const shortfall = 100 - percentageAllocated.value;
          const otherSum = allocations.reduce((sum, a, i) => {
            return i !== index ? sum + Number(a.percentage) : sum;
          }, 0);
          
          if (otherSum > 0) {
            allocations.forEach((allocation, i) => {
              if (i !== index) {
                const proportion = allocation.percentage / otherSum;
                allocation.percentage = allocation.percentage + shortfall * proportion;
              }
            });
          } else if (allocations.length === 1) {
            // S'il n'y a qu'une seule catégorie, elle prend 100%
            allocations[0].percentage = 100;
          }
        }
      }
      
      // Arrondir tous les pourcentages pour éviter les problèmes de précision
      allocations.forEach(allocation => {
        allocation.percentage = Math.round(allocation.percentage);
      });
      
      // S'assurer que la somme est exactement 100%
      const totalPercentage = allocations.reduce((sum, a) => sum + Number(a.percentage), 0);
      if (totalPercentage !== 100 && allocations.length > 0) {
        // Ajuster la dernière catégorie pour atteindre exactement 100%
        const lastIndex = allocations.length - 1;
        allocations[lastIndex].percentage += (100 - totalPercentage);
      }
    };
    
    // Ajouter une catégorie à la répartition
    const addCategory = (category) => {
      // Réduire les pourcentages des catégories existantes pour faire de la place
      const newCategoryPercentage = Math.min(10, remainingPercentage.value);
      
      if (allocations.length > 0 && newCategoryPercentage < 10) {
        // Réduire proportionnellement les autres catégories
        const reduction = 10 - newCategoryPercentage;
        const totalCurrentPercentage = allocations.reduce((sum, a) => sum + Number(a.percentage), 0);
        
        allocations.forEach(allocation => {
          const proportion = allocation.percentage / totalCurrentPercentage;
          allocation.percentage = Math.max(0, allocation.percentage - reduction * proportion);
        });
      }
      
      // Ajouter la nouvelle catégorie
      allocations.push({
        categoryId: category.id,
        percentage: newCategoryPercentage
      });
      
      // Arrondir tous les pourcentages
      allocations.forEach(allocation => {
        allocation.percentage = Math.round(allocation.percentage);
      });
      
      // Ajuster pour atteindre exactement 100%
      const totalPercentage = allocations.reduce((sum, a) => sum + Number(a.percentage), 0);
      if (totalPercentage !== 100) {
        allocations[allocations.length - 1].percentage += (100 - totalPercentage);
      }
    };
    
    // Enregistrer la répartition
    const saveAllocation = async () => {
      if (percentageAllocated.value !== 100) {
        return;
      }
      
      isSaving.value = true;
      
      try {
        // Préparer les données pour l'API
        const allocationData = {
          budget_id: props.budget.id,
          categories: {}
        };
        
        allocations.forEach(allocation => {
          allocationData.categories[allocation.categoryId] = allocation.percentage;
        });
        
        // Envoyer la requête à l'API
        await store.dispatch('budgets/saveAllocation', allocationData);
        
        // Rediriger vers la page du budget
        router.push(`/budgets/${props.budget.id}`);
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement de la répartition:', error);
      } finally {
        isSaving.value = false;
      }
    };
    
    // Annuler la répartition
    const cancelAllocation = () => {
      router.back();
    };
    
    // Formater un montant en devise
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    };
    
    // Formater une date
    const formatDate = (dateString) => {
      if (!dateString) return '';
      
      const date = new Date(dateString);
      return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
    };
    
    return {
      pieChart,
      categories,
      allocations,
      categoriesWithAllocation,
      unusedCategories,
      totalAllocated,
      percentageAllocated,
      remainingAmount,
      remainingPercentage,
      isSaving,
      updateCategoryPercentage,
      addCategory,
      saveAllocation,
      cancelAllocation,
      formatCurrency,
      formatDate
    };
  }
};
</script>

<style scoped>
.budget-allocation {
  background-color: #f8f9fa;
  padding: 1rem;
  border-radius: 5px;
}

.card {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.card-header h5 {
  font-size: 1rem;
  font-weight: 600;
}

.chart-container {
  height: 250px;
  position: relative;
}

.category-item {
  padding: 1rem;
  background-color: #f8f9fa;
  border-radius: 5px;
  border: 1px solid #e9ecef;
}

.slider-container {
  width: 100%;
}

.percentage-input {
  width: 100px;
}

.progress {
  height: 10px;
}
</style>