<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Mes dépenses</h1>
      <router-link :to="{ name: 'user.expenses.create' }" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Ajouter une dépense
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
            <label for="budget_filter" class="form-label">Budget</label>
            <select id="budget_filter" v-model="filters.budget_id" class="form-select" @change="fetchData">
              <option value="">Tous les budgets</option>
              <option v-for="budget in budgets" :key="budget.id" :value="budget.id">
                {{ budget.nom }}
              </option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label for="category_filter" class="form-label">Catégorie</label>
            <select id="category_filter" v-model="filters.category_id" class="form-select" @change="fetchData">
              <option value="">Toutes les catégories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.nom }}
              </option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label for="account_filter" class="form-label">Compte</label>
            <select id="account_filter" v-model="filters.account_id" class="form-select" @change="fetchData">
              <option value="">Tous les comptes</option>
              <option v-for="account in accounts" :key="account.id" :value="account.id">
                {{ account.nom }}
              </option>
            </select>
          </div>
          <div class="col-md-4 mb-3">
            <label for="date_start" class="form-label">Date de début</label>
            <input type="date" id="date_start" v-model="filters.date_start" class="form-control" @change="fetchData" />
          </div>
          <div class="col-md-4 mb-3">
            <label for="date_end" class="form-label">Date de fin</label>
            <input type="date" id="date_end" v-model="filters.date_end" class="form-control" @change="fetchData" />
          </div>
          <div class="col-md-4 mb-3">
            <label for="status_filter" class="form-label">Statut</label>
            <select id="status_filter" v-model="filters.status" class="form-select" @change="fetchData">
              <option value="">Tous les statuts</option>
              <option value="validée">Validée</option>
              <option value="en attente">En attente</option>
              <option value="annulée">Annulée</option>
            </select>
          </div>
          <div class="col-md-8 mb-3">
            <label for="search" class="form-label">Recherche</label>
            <div class="input-group">
              <input 
                type="text" 
                id="search" 
                v-model="filters.search" 
                class="form-control" 
                placeholder="Rechercher une dépense..."
                @input="handleSearchInput"
              />
              <button 
                class="btn btn-outline-secondary" 
                type="button"
                @click="filters.search = ''; fetchData()"
                v-if="filters.search"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <label for="sort_by" class="form-label">Trier par</label>
            <select id="sort_by" v-model="filters.sort_by" class="form-select" @change="fetchData">
              <option value="date_desc">Date (récent → ancien)</option>
              <option value="date_asc">Date (ancien → récent)</option>
              <option value="amount_desc">Montant (décroissant)</option>
              <option value="amount_asc">Montant (croissant)</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Liste des dépenses -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Liste des dépenses</h6>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des dépenses...</p>
        </div>

        <div v-else-if="expenses.length === 0" class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Aucune dépense trouvée{{ hasFilters ? ' pour ces critères.' : '.' }} 
          <router-link :to="{ name: 'user.expenses.create' }" class="btn btn-sm btn-primary mt-2">
            <i class="fas fa-plus me-1"></i> Ajouter une dépense
          </router-link>
        </div>

        <div v-else>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Description</th>
                  <th>Catégorie</th>
                  <th>Montant</th>
                  <th>Compte</th>
                  <th>Statut</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="expense in expenses" :key="expense.id">
                  <td>{{ formatDate(expense.date_depense) }}</td>
                  <td>{{ expense.description }}</td>
                  <td>
                    <span 
                      class="badge rounded-pill" 
                      :style="{ 
                        backgroundColor: expense.categorie.couleur, 
                        color: getContrastColor(expense.categorie.couleur) 
                      }"
                    >
                      {{ expense.categorie.nom }}
                    </span>
                  </td>
                  <td>{{ formatAmount(expense.montant) }}</td>
                  <td>{{ expense.compte.nom }}</td>
                  <td>
                    <span 
                      class="badge" 
                      :class="getStatusBadgeClass(expense.statut)"
                    >
                      {{ expense.statut }}
                    </span>
                  </td>
                  <td>
                    <div class="btn-group">
                      <router-link 
                        :to="{ name: 'user.expenses.show', params: { id: expense.id } }" 
                        class="btn btn-sm btn-info"
                        title="Voir les détails"
                      >
                        <i class="fas fa-eye"></i>
                      </router-link>
                      <router-link 
                        :to="{ name: 'user.expenses.edit', params: { id: expense.id } }" 
                        class="btn btn-sm btn-primary"
                        title="Modifier"
                      >
                        <i class="fas fa-edit"></i>
                      </router-link>
                      <button 
                        class="btn btn-sm btn-danger" 
                        @click="confirmDelete(expense)"
                        title="Supprimer"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <nav aria-label="Pagination des dépenses">
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

    <!-- Résumé des dépenses -->
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Résumé des dépenses</h6>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-lg-6">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Répartition par catégorie</h6>
              </div>
              <div class="card-body">
                <div v-if="loading" class="text-center py-3">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                  </div>
                </div>
                <div v-else-if="expenses.length === 0" class="text-center py-3">
                  <p class="text-muted mb-0">Aucune donnée disponible</p>
                </div>
                <div v-else class="chart-container" style="height: 300px;">
                  <canvas ref="categoryChart"></canvas>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card mb-4">
              <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Dépenses par mois</h6>
              </div>
              <div class="card-body">
                <div v-if="loading" class="text-center py-3">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                  </div>
                </div>
                <div v-else-if="expenses.length === 0" class="text-center py-3">
                  <p class="text-muted mb-0">Aucune donnée disponible</p>
                </div>
                <div v-else class="chart-container" style="height: 300px;">
                  <canvas ref="monthlyChart"></canvas>
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
      id="deleteExpenseModal" 
      tabindex="-1" 
      aria-labelledby="deleteExpenseModalLabel" 
      aria-hidden="true"
      ref="deleteModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteExpenseModalLabel">Confirmer la suppression</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body" v-if="expenseToDelete">
            <p>Êtes-vous sûr de vouloir supprimer cette dépense ?</p>
            <div class="alert alert-info">
              <strong>Description:</strong> {{ expenseToDelete.description }}<br>
              <strong>Montant:</strong> {{ formatAmount(expenseToDelete.montant) }}<br>
              <strong>Date:</strong> {{ formatDate(expenseToDelete.date_depense) }}
            </div>
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Attention:</strong> Cette action est irréversible.
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
              @click="deleteExpense"
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
  name: 'UserExpensesIndex',
  data() {
    return {
      expenses: [],
      budgets: [],
      categories: [],
      accounts: [],
      loading: true,
      deleteModal: null,
      expenseToDelete: null,
      filters: {
        budget_id: '',
        category_id: '',
        account_id: '',
        date_start: '',
        date_end: '',
        status: '',
        search: '',
        sort_by: 'date_desc',
        page: 1
      },
      currentPage: 1,
      lastPage: 1,
      totalExpenses: 0,
      categoryChart: null,
      monthlyChart: null
    };
  },
  computed: {
    hasFilters() {
      return this.filters.budget_id || 
             this.filters.category_id || 
             this.filters.account_id || 
             this.filters.date_start || 
             this.filters.date_end || 
             this.filters.status || 
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
    fetchData() {
      this.loading = true;
      
      // Préparer les paramètres de recherche
      const params = {
        budget_id: this.filters.budget_id,
        category_id: this.filters.category_id,
        account_id: this.filters.account_id,
        date_start: this.filters.date_start,
        date_end: this.filters.date_end,
        status: this.filters.status,
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
      
      axios.get('/api/user/expenses', { params })
        .then(response => {
          this.expenses = response.data.data;
          this.currentPage = response.data.meta.current_page;
          this.lastPage = response.data.meta.last_page;
          this.totalExpenses = response.data.meta.total;
          this.loading = false;
          
          // Mettre à jour les graphiques
          this.$nextTick(() => {
            this.renderCharts();
          });
        })
        .catch(error => {
          console.error('Erreur lors du chargement des dépenses:', error);
          this.$toasted.error('Impossible de charger les dépenses.');
          this.loading = false;
        });
    },
    fetchFilterOptions() {
      // Charger les budgets
      axios.get('/api/user/budgets')
        .then(response => {
          this.budgets = response.data.data;
          
          // Charger les catégories
          return axios.get('/api/categories');
        })
        .then(response => {
          this.categories = response.data.data;
          
          // Charger les comptes
          return axios.get('/api/user/accounts');
        })
        .then(response => {
          this.accounts = response.data.data;
          
          // Vérifier si des filtres sont passés en query parameters
          this.applyQueryParams();
          
          // Charger les dépenses
          this.fetchData();
        })
        .catch(error => {
          console.error('Erreur lors du chargement des options de filtrage:', error);
          this.$toasted.error('Impossible de charger les options de filtrage.');
          
          // Charger quand même les dépenses
          this.fetchData();
        });
    },
    applyQueryParams() {
      const query = this.$route.query;
      
      if (query.budget_id) {
        this.filters.budget_id = query.budget_id;
      }
      
      if (query.category_id) {
        this.filters.category_id = query.category_id;
      }
      
      if (query.account_id) {
        this.filters.account_id = query.account_id;
      }
      
      if (query.date_start) {
        this.filters.date_start = query.date_start;
      }
      
      if (query.date_end) {
        this.filters.date_end = query.date_end;
      }
      
      if (query.status) {
        this.filters.status = query.status;
      }
      
      if (query.search) {
        this.filters.search = query.search;
      }
      
      if (query.sort_by) {
        this.filters.sort_by = query.sort_by;
      }
      
      if (query.page) {
        this.currentPage = parseInt(query.page);
      }
    },
    updateQueryParams() {
      // Préparer les paramètres à ajouter dans l'URL
      const query = {};
      
      if (this.filters.budget_id) {
        query.budget_id = this.filters.budget_id;
      }
      
      if (this.filters.category_id) {
        query.category_id = this.filters.category_id;
      }
      
      if (this.filters.account_id) {
        query.account_id = this.filters.account_id;
      }
      
      if (this.filters.date_start) {
        query.date_start = this.filters.date_start;
      }
      
      if (this.filters.date_end) {
        query.date_end = this.filters.date_end;
      }
      
      if (this.filters.status) {
        query.status = this.filters.status;
      }
      
      if (this.filters.search) {
        query.search = this.filters.search;
      }
      
      if (this.filters.sort_by) {
        query.sort_by = this.filters.sort_by;
      }
      
      if (this.currentPage > 1) {
        query.page = this.currentPage;
      }
      
      // Mettre à jour l'URL sans recharger la page
      this.$router.replace({ query }).catch(() => {});
    },
    resetFilters() {
      this.filters = {
        budget_id: '',
        category_id: '',
        account_id: '',
        date_start: '',
        date_end: '',
        status: '',
        search: '',
        sort_by: 'date_desc'
      };
      this.currentPage = 1;
      this.fetchData();
    },
    goToPage(page) {
      if (page < 1 || page > this.lastPage) {
        return;
      }
      
      this.currentPage = page;
      this.fetchData();
    },
    handleSearchInput: debounce(function() {
      this.currentPage = 1;
      this.fetchData();
    }, 500),
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatAmount(amount, currency = 'MAD') {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: currency
      }).format(amount || 0);
    },
    getStatusBadgeClass(status) {
      switch (status) {
        case 'validée':
          return 'bg-success';
        case 'en attente':
          return 'bg-warning text-dark';
        case 'annulée':
          return 'bg-danger';
        default:
          return 'bg-secondary';
      }
    },
    getContrastColor(hexColor) {
      if (!hexColor) return '#000000';
      
      // Convertir la couleur hex en RGB
      const r = parseInt(hexColor.substr(1, 2), 16);
      const g = parseInt(hexColor.substr(3, 2), 16);
      const b = parseInt(hexColor.substr(5, 2), 16);
      
      // Calculer la luminosité (formule de perception)
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
      
      // Retourner du texte blanc ou noir en fonction de la luminosité
      return luminance > 0.5 ? '#000000' : '#FFFFFF';
    },
    confirmDelete(expense) {
      this.expenseToDelete = expense;
      this.deleteModal.show();
    },
    deleteExpense() {
      if (!this.expenseToDelete) {
        return;
      }
      
      axios.delete(`/api/user/expenses/${this.expenseToDelete.id}`)
        .then(response => {
          this.$toasted.success('Dépense supprimée avec succès!');
          this.deleteModal.hide();
          this.expenseToDelete = null;
          this.fetchData();
        })
        .catch(error => {
          console.error('Erreur lors de la suppression de la dépense:', error);
          this.$toasted.error('Impossible de supprimer la dépense.');
          this.deleteModal.hide();
        });
    },
    renderCharts() {
      this.renderCategoryChart();
      this.renderMonthlyChart();
    },
    renderCategoryChart() {
      if (!this.$refs.categoryChart || this.expenses.length === 0) return;
      
      const ctx = this.$refs.categoryChart.getContext('2d');
      
      // Préparer les données
      const categoryCounts = {};
      const categoryColors = {};
      
      this.expenses.forEach(expense => {
        const categoryName = expense.categorie.nom;
        const categoryColor = expense.categorie.couleur || '#4e73df';
        
        if (!categoryCounts[categoryName]) {
          categoryCounts[categoryName] = 0;
          categoryColors[categoryName] = categoryColor;
        }
        
        categoryCounts[categoryName] += parseFloat(expense.montant);
      });
      
      const labels = Object.keys(categoryCounts);
      const data = Object.values(categoryCounts);
      const colors = labels.map(label => categoryColors[label]);
      
      // Détruire le graphique existant s'il y en a un
      if (this.categoryChart) {
        this.categoryChart.destroy();
      }
      
      this.categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: colors,
            hoverOffset: 4,
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'right',
              labels: {
                boxWidth: 12,
                font: {
                  size: 10
                }
              }
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  let label = context.label || '';
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
    },
    renderMonthlyChart() {
      if (!this.$refs.monthlyChart || this.expenses.length === 0) return;
      
      const ctx = this.$refs.monthlyChart.getContext('2d');
      
      // Préparer les données
      const monthlyExpenses = {};
      
      this.expenses.forEach(expense => {
        const date = new Date(expense.date_depense);
        const monthYear = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        
        if (!monthlyExpenses[monthYear]) {
          monthlyExpenses[monthYear] = 0;
        }
        
        monthlyExpenses[monthYear] += parseFloat(expense.montant);
      });
      
      // Trier les mois par ordre chronologique
      const sortedMonths = Object.keys(monthlyExpenses).sort();
      
      // Formater les labels pour l'affichage
      const monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
      const labels = sortedMonths.map(monthYear => {
        const [year, month] = monthYear.split('-');
        return `${monthNames[parseInt(month) - 1]} ${year}`;
      });
      
      const data = sortedMonths.map(monthYear => monthlyExpenses[monthYear]);
      
      // Détruire le graphique existant s'il y en a un
      if (this.monthlyChart) {
        this.monthlyChart.destroy();
      }
      
      this.monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: labels,
          datasets: [{
            label: 'Dépenses mensuelles',
            data: data,
            backgroundColor: '#4e73df',
            borderColor: '#2e59d9',
            borderWidth: 1
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
    this.fetchFilterOptions();
    this.deleteModal = new Modal(this.$refs.deleteModal);
  },
  beforeUnmount() {
    if (this.categoryChart) {
      this.categoryChart.destroy();
    }
    
    if (this.monthlyChart) {
      this.monthlyChart.destroy();
    }
  },
  watch: {
    currentPage() {
      this.updateQueryParams();
    }
  },
  metaInfo() {
    return {
      title: 'Mes dépenses - Budget Manager'
    };
  }
};
</script>

<style scoped>
/* Ajoutez vos styles personnalisés ici */
</style>