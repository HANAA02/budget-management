<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Mes budgets</h1>
      <router-link :to="{ name: 'user.budgets.create' }" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Créer un budget
      </router-link>
    </div>

    <!-- Filtres -->
    <div class="card shadow mb-4">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        <button class="btn btn-sm btn-outline-secondary" @click="resetFilters">
          <i class="fas fa-undo me-1"></i> Réinitialiser
        </button>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="status_filter" class="form-label">Statut</label>
            <select id="status_filter" v-model="filters.status" class="form-select" @change="applyFilters">
              <option value="">Tous les statuts</option>
              <option value="active">Budgets actifs</option>
              <option value="future">Budgets futurs</option>
              <option value="past">Budgets passés</option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label for="date_start" class="form-label">Date de début</label>
            <input type="date" id="date_start" v-model="filters.date_start" class="form-control" @change="applyFilters" />
          </div>
          <div class="col-md-4 mb-3">
            <label for="date_end" class="form-label">Date de fin</label>
            <input type="date" id="date_end" v-model="filters.date_end" class="form-control" @change="applyFilters" />
          </div>
          <div class="col-md-8 mb-3">
            <label for="search" class="form-label">Recherche</label>
            <div class="input-group">
              <input 
                type="text" 
                id="search" 
                v-model="filters.search" 
                class="form-control" 
                placeholder="Rechercher un budget..."
                @input="handleSearchInput"
              />
              <button 
                class="btn btn-outline-secondary" 
                type="button"
                @click="filters.search = ''; applyFilters()"
                v-if="filters.search"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <label for="sort_by" class="form-label">Trier par</label>
            <select id="sort_by" v-model="filters.sort_by" class="form-select" @change="applyFilters">
              <option value="date_desc">Date (récent → ancien)</option>
              <option value="date_asc">Date (ancien → récent)</option>
              <option value="amount_desc">Montant (décroissant)</option>
              <option value="amount_asc">Montant (croissant)</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Liste des budgets -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Liste des budgets</h6>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des budgets...</p>
        </div>

        <div v-else-if="budgets.length === 0" class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Aucun budget trouvé{{ hasFilters ? ' pour ces critères.' : '.' }} 
          <router-link :to="{ name: 'user.budgets.create' }" class="btn btn-sm btn-primary mt-2">
            <i class="fas fa-plus me-1"></i> Créer un budget
          </router-link>
        </div>

        <div v-else>
          <div class="row">
            <div 
              v-for="budget in budgets" 
              :key="budget.id" 
              class="col-xl-4 col-md-6 mb-4"
            >
              <div class="card h-100">
                <div 
                  class="card-header py-2" 
                  :class="getBudgetStatusClass(budget)"
                >
                  <div class="d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold">{{ budget.nom }}</h5>
                    <span 
                      class="badge" 
                      :class="getBudgetStatusBadgeClass(budget)"
                    >
                      {{ getBudgetStatusText(budget) }}
                    </span>
                  </div>
                </div>
                <div class="card-body">
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Période</span>
                    <span>{{ formatDate(budget.date_debut) }} - {{ formatDate(budget.date_fin) }}</span>
                  </div>
                  
                  <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Budget total</span>
                    <span class="fw-bold">{{ formatAmount(budget.montant_total) }}</span>
                  </div>
                  
                  <div class="d-flex justify-content-between mb-2" v-if="budget.montant_depense !== undefined">
                    <span class="text-muted">Dépenses</span>
                    <span class="text-danger">{{ formatAmount(budget.montant_depense) }}</span>
                  </div>
                  
                  <div class="d-flex justify-content-between mb-2" v-if="budget.montant_restant !== undefined">
                    <span class="text-muted">Restant</span>
                    <span 
                      :class="budget.montant_restant >= 0 ? 'text-success' : 'text-danger'"
                    >
                      {{ formatAmount(budget.montant_restant) }}
                    </span>
                  </div>
                  
                  <div class="progress mb-1" v-if="budget.pourcentage_utilisation !== undefined">
                    <div 
                      class="progress-bar" 
                      role="progressbar" 
                      :style="{ width: `${budget.pourcentage_utilisation}%` }" 
                      :class="getBudgetProgressClass(budget.pourcentage_utilisation)"
                      :aria-valuenow="budget.pourcentage_utilisation" 
                      aria-valuemin="0" 
                      aria-valuemax="100"
                    >
                      {{ budget.pourcentage_utilisation }}%
                    </div>
                  </div>
                  
                  <div v-if="getCurrentBudgetDays(budget) > 0" class="mt-2 text-center">
                    <small class="text-muted">
                      {{ getCurrentBudgetDays(budget) }} jour{{ getCurrentBudgetDays(budget) > 1 ? 's' : '' }} restant{{ getCurrentBudgetDays(budget) > 1 ? 's' : '' }}
                    </small>
                  </div>
                </div>
                <div class="card-footer d-flex justify-content-between py-2">
                  <router-link 
                    :to="{ name: 'user.budgets.show', params: { id: budget.id } }" 
                    class="btn btn-sm btn-info"
                  >
                    <i class="fas fa-eye"></i> Détails
                  </router-link>
                  <div class="btn-group">
                    <router-link 
                      :to="{ name: 'user.budgets.edit', params: { id: budget.id } }" 
                      class="btn btn-sm btn-primary"
                    >
                      <i class="fas fa-edit"></i>
                    </router-link>
                    <button 
                      class="btn btn-sm btn-danger" 
                      @click="confirmDelete(budget)"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Pagination -->
          <nav aria-label="Pagination des budgets">
            <ul class="pagination justify-content-center mt-4">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="goToPage(1)">
                  <i class="fas fa-angle-double-left"></i>
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage - 1)">
                  <i class="fas fa-angle-left"></i>
                </a>
              </li>
              <li 
                v-for="page in pageNumbers" 
                :key="page" 
                class="page-item"
                :class="{ active: currentPage === page }"
              >
                <a class="page-link" href="#" @click.prevent="goToPage(page)">
                  {{ page }}
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                <a class="page-link" href="#" @click.prevent="goToPage(currentPage + 1)">
                  <i class="fas fa-angle-right"></i>
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === lastPage }">
                <a class="page-link" href="#" @click.prevent="goToPage(lastPage)">
                  <i class="fas fa-angle-double-right"></i>
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- Résumé budgétaire -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Résumé budgétaire</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-6">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Évolution du budget</h6>
              </div>
              <div class="card-body">
                <div v-if="loading" class="text-center py-3">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                  </div>
                </div>
                <div v-else-if="budgets.length === 0" class="text-center py-3">
                  <p class="text-muted mb-0">Aucune donnée disponible</p>
                </div>
                <div v-else class="chart-container" style="height: 300px;">
                  <canvas ref="budgetChart"></canvas>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Budget vs Dépenses</h6>
              </div>
              <div class="card-body">
                <div v-if="loading" class="text-center py-3">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                  </div>
                </div>
                <div v-else-if="budgets.length === 0" class="text-center py-3">
                  <p class="text-muted mb-0">Aucune donnée disponible</p>
                </div>
                <div v-else class="chart-container" style="height: 300px;">
                  <canvas ref="comparisonChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div 
      class="modal fade" 
      id="deleteBudgetModal" 
      tabindex="-1" 
      aria-labelledby="deleteBudgetModalLabel" 
      aria-hidden="true"
      ref="deleteModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteBudgetModalLabel">Confirmer la suppression</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body" v-if="budgetToDelete">
            <p>Êtes-vous sûr de vouloir supprimer le budget "{{ budgetToDelete.nom }}" ?</p>
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Attention:</strong> Cette action supprimera également toutes les dépenses associées à ce budget.
            </div>
          </div>
          <div class="modal-footer">
            <button 
              type="button" 
              class="btn btn-secondary" 
              data-bs-dismiss="modal"
            >
              Annuler
            </button>
            <button 
              type="button" 
              class="btn btn-danger"
              @click="deleteBudget"
            >
              Supprimer définitivement
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';
import Chart from 'chart.js/auto';
import debounce from 'lodash/debounce';

export default {
  name: 'UserBudgetsIndex',
  data() {
    return {
      budgets: [],
      loading: true,
      deleteModal: null,
      budgetToDelete: null,
      filters: {
        status: '',
        date_start: '',
        date_end: '',
        search: '',
        sort_by: 'date_desc'
      },
      currentPage: 1,
      lastPage: 1,
      totalBudgets: 0,
      budgetChart: null,
      comparisonChart: null
    };
  },
  computed: {
    hasFilters() {
      return this.filters.status || 
             this.filters.date_start || 
             this.filters.date_end || 
             this.filters.search;
    },
    pageNumbers() {
      const displayedPages = 5;
      const pages = [];
      
      if (this.lastPage <= displayedPages) {
        // Afficher toutes les pages si leur nombre est inférieur au max
        for (let i = 1; i <= this.lastPage; i++) {
          pages.push(i);
        }
      } else {
        // Afficher un sous-ensemble de pages
        let startPage = Math.max(1, this.currentPage - Math.floor(displayedPages / 2));
        let endPage = Math.min(this.lastPage, startPage + displayedPages - 1);
        
        // Ajuster si on est proche de la fin
        if (endPage - startPage + 1 < displayedPages) {
          startPage = Math.max(1, endPage - displayedPages + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
          pages.push(i);
        }
      }
      
      return pages;
    }
  },
  methods: {
    fetchBudgets() {
      this.loading = true;
      
      // Préparer les paramètres de recherche
      const params = {
        status: this.filters.status,
        date_start: this.filters.date_start,
        date_end: this.filters.date_end,
        search: this.filters.search,
        sort_by: this.filters.sort_by,
        page: this.currentPage
      };
      
      // Nettoyer les paramètres vides
      for (const key in params) {
        if (!params[key]) {
          delete params[key];
        }
      }
      
      axios.get('/api/user/budgets', { params })
        .then(response => {
          this.budgets = response.data.data;
          this.currentPage = response.data.meta.current_page;
          this.lastPage = response.data.meta.last_page;
          this.totalBudgets = response.data.meta.total;
          this.loading = false;
          
          // Mettre à jour les graphiques
          this.$nextTick(() => {
            this.renderCharts();
          });
        })
        .catch(error => {
          console.error('Erreur lors du chargement des budgets:', error);
          this.$toasted.error('Impossible de charger les budgets.');
          this.loading = false;
        });
    },
    applyFilters() {
      this.currentPage = 1;
      this.fetchBudgets();
    },
    resetFilters() {
      this.filters = {
        status: '',
        date_start: '',
        date_end: '',
        search: '',
        sort_by: 'date_desc'
      };
      this.currentPage = 1;
      this.fetchBudgets();
    },
    goToPage(page) {
      if (page < 1 || page > this.lastPage) {
        return;
      }
      
      this.currentPage = page;
      this.fetchBudgets();
    },
    handleSearchInput: debounce(function() {
      this.currentPage = 1;
      this.fetchBudgets();
    }, 500),
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatAmount(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount || 0);
    },
    getBudgetStatus(budget) {
      const today = new Date();
      const startDate = new Date(budget.date_debut);
      const endDate = new Date(budget.date_fin);
      
      if (today < startDate) {
        return 'future';
      } else if (today > endDate) {
        return 'past';
      } else {
        return 'active';
      }
    },
    getBudgetStatusText(budget) {
      const status = this.getBudgetStatus(budget);
      
      switch (status) {
        case 'future':
          return 'À venir';
        case 'active':
          return 'Actif';
        case 'past':
          return 'Terminé';
        default:
          return '';
      }
    },
    getBudgetStatusBadgeClass(budget) {
      const status = this.getBudgetStatus(budget);
      
      switch (status) {
        case 'future':
          return 'bg-info';
        case 'active':
          return 'bg-success';
        case 'past':
          return 'bg-secondary';
        default:
          return '';
      }
    },
    getBudgetStatusClass(budget) {
      const status = this.getBudgetStatus(budget);
      
      switch (status) {
        case 'future':
          return 'bg-light';
        case 'active':
          return 'bg-success text-white';
        case 'past':
          return 'bg-secondary text-white';
        default:
          return '';
      }
    },
    getBudgetProgressClass(percentage) {
      if (percentage < 50) {
        return 'bg-success';
      } else if (percentage < 75) {
        return 'bg-info';
      } else if (percentage < 90) {
        return 'bg-warning';
      } else {
        return 'bg-danger';
      }
    },
    getCurrentBudgetDays(budget) {
      const today = new Date();
      const endDate = new Date(budget.date_fin);
      
      if (today > endDate) {
        return 0;
      }
      
      const diffTime = Math.abs(endDate - today);
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      return diffDays;
    },
    confirmDelete(budget) {
      this.budgetToDelete = budget;
      this.deleteModal.show();
    },
    deleteBudget() {
      if (!this.budgetToDelete) {
        return;
      }
      
      axios.delete(`/api/user/budgets/${this.budgetToDelete.id}`)
        .then(response => {
          this.$toasted.success('Budget supprimé avec succès!');
          this.deleteModal.hide();
          this.budgetToDelete = null;
          this.fetchBudgets();
        })
        .catch(error => {
          console.error('Erreur lors de la suppression du budget:', error);
          this.$toasted.error('Impossible de supprimer le budget.');
          this.deleteModal.hide();
        });
    },
    renderCharts() {
      this.renderBudgetChart();
      this.renderComparisonChart();
    },
    renderBudgetChart() {
      if (!this.$refs.budgetChart || this.budgets.length === 0) return;
      
      const ctx = this.$refs.budgetChart.getContext('2d');
      
      // Trier les budgets par date
      const sortedBudgets = [...this.budgets].sort((a, b) => {
        return new Date(a.date_debut) - new Date(b.date_debut);
      });
      
      // Préparer les données
      const labels = sortedBudgets.map(budget => {
        // Formater la date pour l'affichage
        const date = new Date(budget.date_debut);
        return `${date.toLocaleString('fr-FR', { month: 'short' })} ${date.getFullYear()}`;
      });
      
      const data = sortedBudgets.map(budget => budget.montant_total);
      
      // Détruire le graphique existant s'il y en a un
      if (this.budgetChart) {
        this.budgetChart.destroy();
      }
      
      this.budgetChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Montant total du budget',
            data: data,
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            borderWidth: 2,
            pointBackgroundColor: '#4e73df',
            pointBorderColor: '#fff',
            pointHoverRadius: 5,
            pointHoverBackgroundColor: '#4e73df',
            pointHoverBorderColor: '#fff',
            fill: true,
            tension: 0.1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD',
                    maximumFractionDigits: 0
                  }).format(value);
                }
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  return 'Budget: ' + new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD'
                  }).format(context.raw);
                }
              }
            }
          }
        }
      });
    },
    renderComparisonChart() {
      if (!this.$refs.comparisonChart || this.budgets.length === 0) return;
      
      const ctx = this.$refs.comparisonChart.getContext('2d');
      
      // Filtrer les budgets avec des dépenses et les trier par date
      const budgetsWithExpenses = this.budgets
        .filter(budget => budget.montant_depense !== undefined)
        .sort((a, b) => new Date(a.date_debut) - new Date(b.date_debut));
      
      if (budgetsWithExpenses.length === 0) return;
      
      // Préparer les données
      const labels = budgetsWithExpenses.map(budget => {
        // Formater la date pour l'affichage
        const date = new Date(budget.date_debut);
        return `${date.toLocaleString('fr-FR', { month: 'short' })} ${date.getFullYear()}`;
      });
      
      const budgetData = budgetsWithExpenses.map(budget => budget.montant_total);
      const expenseData = budgetsWithExpenses.map(budget => budget.montant_depense);
      
      // Détruire le graphique existant s'il y en a un
      if (this.comparisonChart) {
        this.comparisonChart.destroy();
      }
      
      this.comparisonChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Budget',
              data: budgetData,
              backgroundColor: 'rgba(78, 115, 223, 0.7)',
              borderColor: 'rgba(78, 115, 223, 1)',
              borderWidth: 1
            },
            {
              label: 'Dépenses',
              data: expenseData,
              backgroundColor: 'rgba(231, 74, 59, 0.7)',
              borderColor: 'rgba(231, 74, 59, 1)',
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD',
                    maximumFractionDigits: 0
                  }).format(value);
                }
              }
            }
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.dataset.label || '';
                  if (label) {
                    label += ': ';
                  }
                  label += new Intl.NumberFormat('fr-MA', {
                    style: 'currency',
                    currency: 'MAD'
                  }).format(context.raw);
                  return label;
                }
              }
            }
          }
        }
      });
    }
  },
  mounted() {
    this.fetchBudgets();
    this.deleteModal = new Modal(this.$refs.deleteModal);
  },
  beforeUnmount() {
    if (this.budgetChart) {
      this.budgetChart.destroy();
    }
    
    if (this.comparisonChart) {
      this.comparisonChart.destroy();
    }
  },
  metaInfo() {
    return {
      title: 'Mes budgets - Budget Manager'
    };
  }
};
</script>