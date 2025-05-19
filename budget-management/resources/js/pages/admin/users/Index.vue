<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Gestion des utilisateurs</h1>
      <router-link :to="{ name: 'admin.users.create' }" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Nouvel utilisateur
      </router-link>
    </div>

    <div class="card">
      <div class="card-header">
        <div class="row align-items-center">
          <div class="col-md-4">
            <h5 class="mb-0">Liste des utilisateurs</h5>
          </div>
          <div class="col-md-8">
            <div class="d-flex">
              <div class="input-group me-2">
                <span class="input-group-text">
                  <i class="fas fa-search"></i>
                </span>
                <input 
                  type="text" 
                  class="form-control" 
                  v-model="searchQuery" 
                  placeholder="Rechercher un utilisateur..."
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
              <select v-model="roleFilter" class="form-select" style="width: auto;">
                <option value="">Tous les rôles</option>
                <option value="admin">Administrateurs</option>
                <option value="user">Utilisateurs</option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
          <p class="mt-2">Chargement des utilisateurs...</p>
        </div>

        <div v-else-if="filteredUsers.length === 0" class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Aucun utilisateur trouvé{{ searchQuery || roleFilter ? ' pour ces critères.' : '.' }}
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
                    Nom complet
                    <i v-if="sortKey === 'nom'" 
                       :class="sortOrder === 1 ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                  </a>
                </th>
                <th>Email</th>
                <th>
                  <a href="#" @click.prevent="sortBy('role')">
                    Rôle
                    <i v-if="sortKey === 'role'" 
                       :class="sortOrder === 1 ? 'fas fa-sort-up' : 'fas fa-sort-down'"></i>
                  </a>
                </th>
                <th>
                  <a href="#" @click.prevent="sortBy('date_creation')">
                    Date d'inscription
                    <i v-if="sortKey === 'date_creation'" 
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
              <tr v-for="user in paginatedUsers" :key="user.id">
                <td>{{ user.id }}</td>
                <td>{{ user.prenom }} {{ user.nom }}</td>
                <td>{{ user.email }}</td>
                <td>
                  <span 
                    class="badge" 
                    :class="user.role === 'admin' ? 'bg-danger' : 'bg-primary'"
                  >
                    {{ user.role === 'admin' ? 'Administrateur' : 'Utilisateur' }}
                  </span>
                </td>
                <td>{{ formatDate(user.date_creation) }}</td>
                <td>
                  <span 
                    class="badge" 
                    :class="user.active ? 'bg-success' : 'bg-danger'"
                  >
                    {{ user.active ? 'Actif' : 'Inactif' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group">
                    <router-link 
                      :to="{ name: 'admin.users.show', params: { id: user.id } }" 
                      class="btn btn-sm btn-info"
                      title="Voir les détails"
                    >
                      <i class="fas fa-eye"></i>
                    </router-link>
                    <router-link 
                      :to="{ name: 'admin.users.edit', params: { id: user.id } }" 
                      class="btn btn-sm btn-primary"
                      title="Modifier"
                    >
                      <i class="fas fa-edit"></i>
                    </router-link>
                    <button 
                      class="btn btn-sm" 
                      :class="user.active ? 'btn-warning' : 'btn-success'"
                      @click="toggleUserStatus(user)"
                      :title="user.active ? 'Désactiver' : 'Activer'"
                    >
                      <i :class="user.active ? 'fas fa-ban' : 'fas fa-check'"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <nav aria-label="Pagination des utilisateurs">
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

    <!-- Modal de confirmation de changement de statut -->
    <div 
      class="modal fade" 
      id="statusChangeModal" 
      tabindex="-1" 
      aria-labelledby="statusChangeModalLabel" 
      aria-hidden="true"
      ref="statusModal"
    >
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="statusChangeModalLabel">Confirmer le changement de statut</h5>
            <button 
              type="button" 
              class="btn-close" 
              data-bs-dismiss="modal" 
              aria-label="Fermer"
            ></button>
          </div>
          <div class="modal-body" v-if="userToUpdate">
            <p v-if="userToUpdate.active">
              Êtes-vous sûr de vouloir <strong>désactiver</strong> le compte de <strong>{{ userToUpdate.prenom }} {{ userToUpdate.nom }}</strong> ?
            </p>
            <p v-else>
              Êtes-vous sûr de vouloir <strong>activer</strong> le compte de <strong>{{ userToUpdate.prenom }} {{ userToUpdate.nom }}</strong> ?
            </p>
            <div class="alert" :class="userToUpdate.active ? 'alert-warning' : 'alert-info'">
              <i :class="userToUpdate.active ? 'fas fa-exclamation-triangle me-2' : 'fas fa-info-circle me-2'"></i>
              <span v-if="userToUpdate.active">
                <strong>Attention :</strong> L'utilisateur ne pourra plus se connecter à l'application tant que son compte sera désactivé.
              </span>
              <span v-else>
                <strong>Information :</strong> L'utilisateur pourra à nouveau se connecter à l'application.
              </span>
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
              :class="userToUpdate && userToUpdate.active ? 'btn btn-warning' : 'btn btn-success'"
              @click="updateUserStatus"
            >
              <span v-if="userToUpdate && userToUpdate.active">
                <i class="fas fa-ban me-1"></i> Désactiver
              </span>
              <span v-else>
                <i class="fas fa-check me-1"></i> Activer
              </span>
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
  name: 'AdminUsersIndex',
  data() {
    return {
      users: [],
      loading: true,
      searchQuery: '',
      roleFilter: '',
      sortKey: 'id',
      sortOrder: 1, // 1 = ascending, -1 = descending
      currentPage: 1,
      itemsPerPage: 10,
      statusModal: null,
      userToUpdate: null
    };
  },
  computed: {
    filteredUsers() {
      let result = [...this.users];
      
      // Filtre par rôle
      if (this.roleFilter) {
        result = result.filter(user => user.role === this.roleFilter);
      }
      
      // Filtre par recherche
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        result = result.filter(user => 
          user.nom.toLowerCase().includes(query) || 
          user.prenom.toLowerCase().includes(query) || 
          user.email.toLowerCase().includes(query) ||
          `${user.prenom} ${user.nom}`.toLowerCase().includes(query)
        );
      }
      
      return this.sortUsers(result);
    },
    sortUsers() {
      return (users) => {
        const key = this.sortKey;
        const order = this.sortOrder;
        
        return [...users].sort((a, b) => {
          let valueA, valueB;
          
          // Cas spécial pour le tri par nom complet
          if (key === 'nom') {
            valueA = `${a.prenom} ${a.nom}`.toLowerCase();
            valueB = `${b.prenom} ${b.nom}`.toLowerCase();
          } else {
            valueA = a[key];
            valueB = b[key];
            
            // Handle string comparison
            if (typeof valueA === 'string') {
              valueA = valueA.toLowerCase();
              valueB = valueB.toLowerCase();
            }
          }
          
          if (valueA < valueB) return -1 * order;
          if (valueA > valueB) return 1 * order;
          return 0;
        });
      };
    },
    paginatedUsers() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredUsers.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredUsers.length / this.itemsPerPage) || 1;
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
    fetchUsers() {
      this.loading = true;
      axios.get('/api/admin/users')
        .then(response => {
          this.users = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des utilisateurs:', error);
          this.$toasted.error('Impossible de charger les utilisateurs.');
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
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    toggleUserStatus(user) {
      this.userToUpdate = { ...user };
      this.statusModal.show();
    },
    updateUserStatus() {
      if (!this.userToUpdate) return;
      
      const newStatus = !this.userToUpdate.active;
      
      axios.patch(`/api/admin/users/${this.userToUpdate.id}/status`, { active: newStatus })
        .then(() => {
          // Mettre à jour l'utilisateur dans la liste
          const index = this.users.findIndex(u => u.id === this.userToUpdate.id);
          if (index !== -1) {
            this.users[index].active = newStatus;
          }
          
          const action = newStatus ? 'activé' : 'désactivé';
          this.$toasted.success(`Compte ${action} avec succès!`);
          
          this.statusModal.hide();
          this.userToUpdate = null;
        })
        .catch(error => {
          console.error('Erreur lors de la mise à jour du statut:', error);
          
          let errorMessage = 'Une erreur est survenue lors de la mise à jour du statut.';
          
          if (error.response && error.response.data && error.response.data.message) {
            errorMessage = error.response.data.message;
          }
          
          this.$toasted.error(errorMessage);
          this.statusModal.hide();
        });
    }
  },
  mounted() {
    this.fetchUsers();
    this.statusModal = new Modal(this.$refs.statusModal);
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
    },
    roleFilter() {
      this.currentPage = 1;
    }
  },
  metaInfo() {
    return {
      title: 'Gestion des utilisateurs - Administration'
    };
  }
};
</script>

<style scoped>
/* Add any custom styles here */
</style>