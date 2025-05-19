<template>
  <div class="expense-bar-chart">
    <div class="chart-container">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch, computed } from 'vue';
import Chart from 'chart.js/auto';

export default {
  name: 'ExpenseBarChart',
  props: {
    categoriesData: {
      type: Array,
      required: true,
      default: () => []
    },
    title: {
      type: String,
      default: 'Dépenses par catégorie'
    },
    height: {
      type: Number,
      default: 300
    },
    horizontal: {
      type: Boolean,
      default: false
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
    
    // Générer les données pour le graphique
    const chartData = computed(() => {
      return {
        labels: props.categoriesData.map(item => item.nom),
        datasets: [
          {
            label: 'Budget alloué',
            data: props.categoriesData.map(item => item.montant_alloue),
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          },
          {
            label: 'Dépenses',
            data: props.categoriesData.map(item => item.total_depenses),
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
          }
        ]
      };
    });
    
    // Initialiser le graphique
    const initChart = () => {
      if (!chartCanvas.value) return;
      
      const ctx = chartCanvas.value.getContext('2d');
      
      chart = new Chart(ctx, {
        type: props.horizontal ? 'horizontalBar' : 'bar',
        data: chartData.value,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          indexAxis: props.horizontal ? 'y' : 'x',
          plugins: {
            legend: {
              position: 'top'
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
                  const label = context.dataset.label || '';
                  const value = context.raw;
                  return `${label}: ${props.formatCurrency(value)}`;
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return props.formatCurrency(value);
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
      chart.options.indexAxis = props.horizontal ? 'y' : 'x';
      chart.update();
    };
    
    // Initialiser le graphique au montage du composant
    onMounted(() => {
      initChart();
    });
    
    // Observer les changements de données pour mettre à jour le graphique
    watch(() => props.categoriesData, () => {
      updateChart();
    }, { deep: true });
    
    // Observer les changements de titre et d'orientation pour mettre à jour le graphique
    watch([() => props.title, () => props.horizontal], () => {
      if (chart) {
        chart.options.plugins.title.text = props.title;
        chart.options.indexAxis = props.horizontal ? 'y' : 'x';
        chart.update();
      }
    });
    
    return {
      chartCanvas
    };
  }
};
</script>

<style scoped>
.expense-bar-chart {
  width: 100%;
}

.chart-container {
  position: relative;
  height: v-bind(height + 'px');
}
</style>