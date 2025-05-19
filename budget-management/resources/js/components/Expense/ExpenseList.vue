<template>
  <div class="expense-list">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Liste des dépenses</h3>
        <button class="btn btn-primary" @click="$emit('add-expense')">
          <i class="fas fa-plus"></i> Nouvelle dépense
        </button>
      </div>
      <div class="card-body">
        <div class="filters mb-4">
          <div class="row">
            <div class="col-md-4 mb-2">
              <div class="input-group">
                <span class="input-group-text">Recherche</span>
                <input 
                  type="text" 
                  class="form-control" 
                  v-model="filters.search" 
                  placeholder="Description..."
                  @input="applyFilters"
                />
              </div>
            </div>
            <div class="col-md-3 mb-2">
              <div class="input-group">
                <span class="input-group-text">Catégorie</span>
                <select 
                  class="form-control" 
                  v-model="filters.category" 
                  @change="applyFilters"
                >
                  <option value="">Toutes</option>
                  <option 
                    v-for="category in categories" 
                    :key="category.id" 
                    :value="category.id"
                  >
                    {{ category.nom }}
                  </option>
                </select>
              </div>
            </div>
            <div class="col-md-3 mb-2">
              <div class="input-group">
                <span class="input-group-text">Statut</span>
                <select 
                  class="form-control" 
                  v-model="filters.status" 
                  @change="applyFilters"
                >
                  <option value="">Tous</option>
                  <option value="validée">Validée</option>
                  <option value="en attente">En attente</option>
                  <option value="annulée">Annulée</option>
                </select>
              </div>
            </div>
            <div class="col-md-2 mb-2">
              <button 
                class="btn btn-outline-secondary w-100" 
                @click="resetFilters"
              >
                Réinitialiser
              </button>
            </div>
          </div>
          <div class="row mt-2">
            <div class="col-md-4 mb-2">
              <div class="input-group">
                <span class="input-group-text">Du</span>
                <input 
                  type="date" 
                  class="form-control" 
                  v-model="filters.dateFrom" 
                  @change="applyFilters"
                />
              </div>
            </div>
            <div class="col-md-4 mb-2">
              <div class="input-group">
                <span class="input-group-text">Au</span>
                <input 
                  type="date" 
                  class="form-control" 
                  v-model="filters.dateTo" 
                  @change="applyFilters"
                />
              </div>
            </div>
            <div class="col-md-4 mb-2">
              <div class="input-group">
                <span class="input-group-text">Trier par</span>
                <select 
                  class="form-control" 
                  v-model="filters.sortBy" 
                  @change="applyFilters"
                >
                  <option value="date_desc">Date (récent)</option>
                  <option value="date_asc">Date (ancien)</option>
                  <option value="montant_desc">Montant (décroissant)</option>
                  <option value="montant_asc">Montant (croissant)</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </div>

        <div v-else-if="filteredExpenses.length === 0" class="alert alert-info">
          Aucune dépense trouvée pour les critères sélectionnés.
        </div>

        <div v-else class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Catégorie</th>
                <th>Montant</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="expense in paginatedExpenses" :key="expense.id">
                <td>{{ formatDate(expense.date_depense) }}</td>
                <td>{{ expense.description }}</td>
                <td>
                  <span 
                    class="badge rounded-pill" 
                    :style="{ backgroundColor: getCategoryColor(expense.categorie_budget.categorie_id) }"
                  >
                    {{ expense.categorie_budget.categorie.nom }}
                  </span>
                </td>
                <td>{{ formatMontant(expense.montant) }}</td>
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
                    <button 
                      class="btn btn-sm btn-outline-primary" 
                      @click="$emit('edit-expense', expense)"
                      title="Modifier"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button 
                      class="btn btn-sm btn-outline-danger" 
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

          <!-- Pagination -->
          <nav aria-label="Pagination des dépenses">
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="currentPage = 1">
                  <i class="fas fa-angle-double-left"></i>
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === 1 }">
                <a class="page-link" href="#" @click.prevent="currentPage--">
                  <i class="fas fa-angle-left"></i>
                </a>
              </li>
              <li 
                v-for="page in pageNumbers" 
                :key="page" 
                class="page-item"
                :class="{ active: currentPage === page }"
              >
                <a class="page-link" href="#" @click.prevent="currentPage = page">
                  {{ page }}
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click.prevent="currentPage++">
                  <i class="fas fa-angle-right"></i>
                </a>
              </li>
              <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                <a class="page-link" href="#" @click.prevent="currentPage = totalPages">
                  <i class="fas fa-angle-double-right"></i>
                </a>
              </li>
            </ul>
          </nav>
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
              <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette dépense ?
                <p class="text-danger mt-2">
                  Cette action est irréversible.
                </p>
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
                  Supprimer
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  name: 'ExpenseList',
  props: {
    budgetId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      expenses: [],
      categories: [],
      loading: true,
      deleteModal: null,
      expenseToDelete: null,
      currentPage: 1,
      itemsPerPage: 10,
      filters: {
        search: '',
        category: '',
        status: '',
        dateFrom: '',
        dateTo: '',
        sortBy: 'date_desc'
      },
      categoryColors: {
        // Couleurs par défaut pour les catégories courantes
        1: '#ff6b6b', // Logement
        2: '#48dbfb', // Alimentation
        3: '#1dd1a1', // Santé
        4: '#feca57', // Transport
        5: '#5f27cd', // Loisirs
        6: '#ff9ff3', // Habillement
        7: '#54a0ff', // Éducation
        8: '#ff9f43', // Épargne
        9: '#00d2d3', // Autres
      }
    };
  },
  computed: {
    filteredExpenses() {
      let result = [...this.expenses];
      
      // Recherche par texte
      if (this.filters.search) {
        const searchLower = this.filters.search.toLowerCase();
        result = result.filter(expense => 
          expense.description.toLowerCase().includes(searchLower)
        );
      }
      
      // Filtre par catégorie
      if (this.filters.category) {
        result = result.filter(expense => 
          expense.categorie_budget.categorie_id == this.filters.category
        );
      }
      
      // Filtre par statut
      if (this.filters.status) {
        result = result.filter(expense => 
          expense.statut === this.filters.status
        );
      }
      
      // Filtre par date de début
      if (this.filters.dateFrom) {
        result = result.filter(expense => 
          new Date(expense.date_depense) >= new Date(this.filters.dateFrom)
        );
      }
      
      // Filtre par date de fin
      if (this.filters.dateTo) {
        result = result.filter(expense => 
          new Date(expense.date_depense) <= new Date(this.filters.dateTo)
        );
      }
      
      // Tri
      result.sort((a, b) => {
        switch (this.filters.sortBy) {
          case 'date_asc':
            return new Date(a.date_depense) - new Date(b.date_depense);
          case 'date_desc':
            return new Date(b.date_depense) - new Date(a.date_depense);
          case 'montant_asc':
            return a.montant - b.montant;
          case 'montant_desc':
            return b.montant - a.montant;
          default:
            return new Date(b.date_depense) - new Date(a.date_depense);
        }
      });
      
      return result;
    },
    paginatedExpenses() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredExpenses.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredExpenses.length / this.itemsPerPage);
    },
    pageNumbers() {
      const pages = [];
      const maxPagesToShow = 5;
      
      if (this.totalPages <= maxPagesToShow) {
        // Afficher toutes les pages si leur nombre est inférieur au max
        for (let i = 1; i <= this.totalPages; i++) {
          pages.push(i);
        }
      } else {
        // Afficher un sous-ensemble de pages
        if (this.currentPage <= 3) {
          // Proche du début
          for (let i = 1; i <= 5; i++) {
            pages.push(i);
          }
        } else if (this.currentPage >= this.totalPages - 2) {
          // Proche de la fin
          for (let i = this.totalPages - 4; i <= this.totalPages; i++) {
            pages.push(i);
          }
        } else {
          // Au milieu
          for (let i = this.currentPage - 2; i <= this.currentPage + 2; i++) {
            pages.push(i);
          }
        }
      }
      
      return pages;
    }
  },
  methods: {
    loadExpenses() {
      this.loading = true;
      axios.get(`/api/budgets/${this.budgetId}/expenses`)
        .then(response => {
          this.expenses = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des dépenses:', error);
          this.loading = false;
        });
    },
    loadCategories() {
      axios.get('/api/categories')
        .then(response => {
          this.categories = response.data.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des catégories:', error);
        });
    },
    formatDate(dateString) {
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    formatMontant(montant) {
      return new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(montant);
    },
    applyFilters() {
      this.currentPage = 1;
    },
    resetFilters() {
      this.filters = {
        search: '',
        category: '',
        status: '',
        dateFrom: '',
        dateTo: '',
        sortBy: 'date_desc'
      };
      this.currentPage = 1;
    },
    getCategoryColor(categoryId) {
      return this.categoryColors[categoryId] || '#aaa';
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
    confirmDelete(expense) {
      this.expenseToDelete = expense;
      this.deleteModal.show();
    },
    deleteExpense() {
      if (!this.expenseToDelete) return;
      
      axios.delete(`/api/expenses/${this.expenseToDelete.id}`)
        .then(() => {
          this.loadExpenses();
          this.deleteModal.hide();
          this.expenseToDelete = null;
          this.$emit('expense-deleted');
        })
        .catch(error => {
          console.error('Erreur lors de la suppression de la dépense:', error);
        });
    }
  },
  mounted() {
    this.loadExpenses();
    this.loadCategories();
    this.deleteModal = new Modal(this.$refs.deleteModal);
  }
};
</script>

<style scoped>
.table th {
  font-weight: 600;
}
.badge {
  font-size: 0.85em;
  padding: 0.35em 0.65em;
}
.filters {
  background-color: #f8f9fa;
  padding: 15px;
  border-radius: 5px;
  margin-bottom: 20px;
}
</style>