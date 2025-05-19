<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Gestion des catégories</h1>
      <router-link :to="{ name: 'admin.categories.create' }" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nouvelle catégorie
      </router-link>
    </div>

    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-md-6">
            <h5 class="mb-0">Liste des catégories</h5>
          </div>
          <div class="col-md-6">
            <div class="input-group">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
              <input 
                type="text" 
                class="form-control" 
                v-model="searchQuery" 
                placeholder="Rechercher une catégorie..."
              />
              <button 
                class="btn btn-outline-secondary" 
                type="button"
                @click="searchQuery = ''"
                v-if="searchQuery"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des catégories...</p>
        </div>

        <div v-else-if="filteredCategories.length === 0" class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Aucune catégorie trouvée{{ searchQuery ? ' pour cette recherche.' : '.' }}
        </div>

        <div v-else class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>
                  <a href="#" @click.prevent="sortBy('id')">
                    ID 
                    <i v-if="sortKey === 'id'" 
                       :class="sortOrder === 1 ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                  </a>
                </th>
                <th>
                  <a href="#" @click.prevent="sortBy('nom')">
                    Nom 
                    <i v-if="sortKey === 'nom'" 
                       :class="sortOrder === 1 ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                  </a>
                </th>
                <th>Icône</th>
                <th>
                  <a href="#" @click.prevent="sortBy('pourcentage_defaut')">
                    Pourcentage par défaut
                    <i v-if="sortKey === 'pourcentage_defaut'" 
                       :class="sortOrder === 1 ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                  </a>
                </th>
                <th>
                  <a href="#" @click.prevent="sortBy('active')">
                    Statut
                    <i v-if="sortKey === 'active'" 
                       :class="sortOrder === 1 ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                  </a>
                </th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="category in paginatedCategories" :key="category.id">
                <td>{{ category.id }}</td>
                <td>
                  <span 
                    class="badge rounded-pill" 
                    :style="{ backgroundColor: category.couleur, color: getContrastColor(category.couleur) }"
                  >
                    {{ category.nom }}
                  </span>
                </td>
                <td>
                  <i :class="category.icone || 'fas fa-folder'" class="fa-lg"></i>
                </td>
                <td>{{ category.pourcentage_defaut }}%</td>
                <td>
                  <span 
                    class="badge" 
                    :class="category.active ? 'bg-success' : 'bg-danger'"
                  >
                    {{ category.active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group">
                    <router-link 
                      :to="{ name: 'admin.categories.show', params: { id: category.id } }" 
                      class="btn btn-sm btn-info"
                      title="Voir les détails"
                    >
                      <i class="fas fa-eye"></i>
                    </router-link>
                    <router-link 
                      :to="{ name: 'admin.categories.edit', params: { id: category.id } }" 
                      class="btn btn-sm btn-primary"
                      title="Modifier"
                    >
                      <i class="fas fa-edit"></i>
                    </router-link>
                    <button 
                      class="btn btn-sm btn-danger" 
                      @click="confirmDelete(category)"
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
          <nav aria-label="Pagination des catégories">
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
      </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div 
      class="modal fade" 
      id="deleteCategoryModal" 
      tabindex="-1" 
      aria-labelledby="deleteCategoryModalLabel" 
      aria-hidden="true"
      ref="deleteModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteCategoryModalLabel">Confirmer la suppression</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body" v-if="categoryToDelete">
            <p>
              Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ categoryToDelete.nom }}</strong> ?
            </p>
            <div class="alert alert-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              <strong>Attention :</strong> Cette action est irréversible et supprimera également toutes les dépenses associées à cette catégorie.
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
              @click="deleteCategory"
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

export default {
  name: 'AdminCategoriesIndex',
  data() {
    return {
      categories: [],
      loading: true,
      searchQuery: '',
      sortKey: 'id',
      sortOrder: 1, // 1 = ascending, -1 = descending
      currentPage: 1,
      itemsPerPage: 10,
      deleteModal: null,
      categoryToDelete: null
    };
  },
  computed: {
    filteredCategories() {
      if (!this.searchQuery) {
        return this.sortedCategories;
      }
      
      const query = this.searchQuery.toLowerCase();
      return this.sortedCategories.filter(category => 
        category.nom.toLowerCase().includes(query) || 
        (category.description && category.description.toLowerCase().includes(query))
      );
    },
    sortedCategories() {
      const key = this.sortKey;
      const order = this.sortOrder;
      
      return [...this.categories].sort((a, b) => {
        let valueA = a[key];
        let valueB = b[key];
        
        // Handle string comparison
        if (typeof valueA === 'string') {
          valueA = valueA.toLowerCase();
          valueB = valueB.toLowerCase();
        }
        
        if (valueA < valueB) return -1 * order;
        if (valueA > valueB) return 1 * order;
        return 0;
      });
    },
    paginatedCategories() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredCategories.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredCategories.length / this.itemsPerPage) || 1;
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
    fetchCategories() {
      this.loading = true;
      axios.get('/api/admin/categories')
        .then(response => {
          this.categories = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des catégories:', error);
          this.$toasted.error('Impossible de charger les catégories.');
          this.loading = false;
        });
    },
    sortBy(key) {
      if (this.sortKey === key) {
        this.sortOrder = -this.sortOrder;
      } else {
        this.sortKey = key;
        this.sortOrder = 1;
      }
      this.currentPage = 1;
    },
    confirmDelete(category) {
      this.categoryToDelete = category;
      this.deleteModal.show();
    },
    deleteCategory() {
      if (!this.categoryToDelete) return;
      
      axios.delete(`/api/admin/categories/${this.categoryToDelete.id}`)
        .then(() => {
          this.$toasted.success('Catégorie supprimée avec succès!');
          this.fetchCategories();
          this.deleteModal.hide();
          this.categoryToDelete = null;
        })
        .catch(error => {
          console.error('Erreur lors de la suppression de la catégorie:', error);
          
          let errorMessage = 'Une erreur est survenue lors de la suppression.';
          
          if (error.response && error.response.data && error.response.data.message) {
            errorMessage = error.response.data.message;
          }
          
          this.$toasted.error(errorMessage);
          this.deleteModal.hide();
        });
    },
    getContrastColor(hexColor) {
      // Convertir la couleur hex en RGB
      const r = parseInt(hexColor.substr(1, 2), 16);
      const g = parseInt(hexColor.substr(3, 2), 16);
      const b = parseInt(hexColor.substr(5, 2), 16);
      
      // Calculer la luminosité (formule de perception)
      const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
      
      // Retourner du texte blanc ou noir en fonction de la luminosité
      return luminance > 0.5 ? '#000000' : '#FFFFFF';
    }
  },
  mounted() {
    this.fetchCategories();
    this.deleteModal = new Modal(this.$refs.deleteModal);
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
    }
  },
  metaInfo() {
    return {
      title: 'Gestion des catégories - Administration'
    };
  }
};
</script>