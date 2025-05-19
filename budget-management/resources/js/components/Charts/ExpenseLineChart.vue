<template>
  <div class="expense-line-chart">
    <div class="chart-container">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, watch, computed } from 'vue';
import Chart from 'chart.js/auto';

export default {
  name: 'ExpenseLineChart',
  props: {
    data: {
      type: Object,
      required: true,
      default: () => ({ labels: [], datasets: [] })
    },
    title: {
      type: String,
      default: 'Évolution des dépenses'
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
    
    // Préparer les données pour le graphique
    const chartData = computed(() => {
      // Si les données sont déjà formatées correctement, les utiliser directement
      if (props.data.labels && props.data.datasets) {
        return props.data;
      }
      
      // Sinon, supposer que c'est un objet { month1: value1, month2: value2, ... }
      const labels = Object.keys(props.data);
      const values = Object.values(props.data);
      
      return {
        labels,
        datasets: [
          {
            label: 'Dépenses',
            data: values,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1,
            fill: true
          }
        ]
      };
    });
    
    // Initialiser le graphique
    const initChart = () => {
      if (!chartCanvas.value) return;
      
      const ctx = chartCanvas.value.getContext('2d');
      
      chart = new Chart(ctx, {
        type: 'line',
        data: chartData.value,
        options: {
          responsive: true,
          maintainAspectRatio: false,
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
      chartCanvas
    };
  }
};
</script>

<style scoped>
.expense-line-chart {
  width: 100%;
}

.chart-container {
  position: relative;
  height: v-bind(height + 'px');
}
</style>