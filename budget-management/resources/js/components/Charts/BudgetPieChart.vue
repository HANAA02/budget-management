<template>
  <div class="budget-pie-chart">
    <div class="chart-container">
      <canvas ref="chartCanvas"></canvas>
    </div>
    
    <div v-if="showLegend" class="chart-legend mt-3">
      <div 
        v-for="(item, index) in legendItems" 
        :key="index"
        class="legend-item"
      >
        <div 
          class="color-box" 
          :style="{ backgroundColor: item.color }"
        ></div>
        <div class="legend-text">
          <span class="legend-label">{{ item.label }}</span>
          <span class="legend-value">{{ item.value }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch, computed } from 'vue';
import Chart from 'chart.js/auto';

export default {
  name: 'BudgetPieChart',
  props: {
    data: {
      type: Array,
      required: true,
      default: () => []
    },
    title: {
      type: String,
      default: 'Répartition du budget'
    },
    showLegend: {
      type: Boolean,
      default: true
    },
    height: {
      type: Number,
      default: 300
    },
    formatCurrency: {
      type: Function,
      default: (value) => {
        return new Intl.NumberFormat('fr-MA', {
          style: 'currency',
          currency: 'MAD'
        }).format(value);
      }
    }
  },
  setup(props) {
    const chartCanvas = ref(null);
    let chart = null;
    
    // Générer les couleurs pour le graphique
    const generateColors = (count) => {
      const colors = [
        '#3498db', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6',
        '#1abc9c', '#d35400', '#34495e', '#16a085', '#c0392b',
        '#8e44ad', '#2980b9', '#27ae60', '#f1c40f', '#e67e22'
      ];
      
      return Array(count).fill().map((_, i) => colors[i % colors.length]);
    };
    
    // Préparer les données pour le graphique
    const chartData = computed(() => {
      return {
        labels: props.data.map(item => item.label || item.nom),
        datasets: [{
          data: props.data.map(item => item.value || item.percentage || item.amount),
          backgroundColor: generateColors(props.data.length),
          borderWidth: 1,
          borderColor: '#fff'
        }]
      };
    });
    
    // Préparer les éléments de légende
    const legendItems = computed(() => {
      if (!chartData.value || !chartData.value.datasets || chartData.value.datasets.length === 0) {
        return [];
      }
      
      return chartData.value.labels.map((label, index) => {
        const value = chartData.value.datasets[0].data[index];
        const color = chartData.value.datasets[0].backgroundColor[index];
        
        // Déterminer si la valeur est un montant ou un pourcentage
        let formattedValue;
        if (props.data[index].amount !== undefined) {
          formattedValue = props.formatCurrency(props.data[index].amount);
        } else if (props.data[index].percentage !== undefined) {
          formattedValue = `${props.data[index].percentage}%`;
        } else {
          formattedValue = value + (typeof value === 'number' && value <= 100 ? '%' : '');
        }
        
        return {
          label,
          value: formattedValue,
          color
        };
      });
    });
    
    // Initialiser le graphique
    const initChart = () => {
      if (!chartCanvas.value) return;
      
      const ctx = chartCanvas.value.getContext('2d');
      
      chart = new Chart(ctx, {
        type: 'pie',
        data: chartData.value,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: !props.showLegend, // Masquer la légende intégrée si on utilise notre propre légende
              position: 'bottom'
            },
            title: {
              display: !!props.title,
              text: props.title,
              font: {
                size: 16,
                weight: 'bold'
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const label = context.label || '';
                  const value = context.raw;
                  
                  // Déterminer si la valeur est un montant ou un pourcentage
                  if (props.data[context.dataIndex].amount !== undefined) {
                    return `${label}: ${props.formatCurrency(props.data[context.dataIndex].amount)}`;
                  } else if (props.data[context.dataIndex].percentage !== undefined) {
                    return `${label}: ${props.data[context.dataIndex].percentage}%`;
                  } else {
                    return `${label}: ${value}${typeof value === 'number' && value <= 100 ? '%' : ''}`;
                  }
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
      
      chart.data = chartData.value;
      chart.update();
    };
    
    // Initialiser le graphique au montage du composant
    onMounted(() => {
      initChart();
    });
    
    // Observer les changements de données pour mettre à jour le graphique
    watch(() => props.data, () => {
      updateChart();
    }, { deep: true });
    
    // Observer les changements de titre pour mettre à jour le graphique
    watch(() => props.title, () => {
      if (chart) {
        chart.options.plugins.title.text = props.title;
        chart.update();
      }
    });
    
    return {
      chartCanvas,
      legendItems
    };
  }
};
</script>

<style scoped>
.budget-pie-chart {
  width: 100%;
}

.chart-container {
  position: relative;
  height: v-bind(height + 'px');
}

.chart-legend {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
}

.legend-item {
  display: flex;
  align-items: center;
  margin-bottom: 5px;
  margin-right: 10px;
}

.color-box {
  width: 12px;
  height: 12px;
  margin-right: 5px;
  border-radius: 2px;
}

.legend-text {
  display: flex;
  flex-direction: column;
}

.legend-label {
  font-size: 0.8rem;
  font-weight: 500;
}

.legend-value {
  font-size: 0.75rem;
  color: #6c757d;
}

@media (max-width: 576px) {
  .chart-legend {
    justify-content: flex-start;
  }
  
  .legend-item {
    width: 100%;
  }
}
</style>