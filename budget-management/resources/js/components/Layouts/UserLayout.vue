<template>
  <div class="user-layout">
    <sidebar />
    
    <div class="content-wrapper" :class="{ 'sidebar-collapsed': isSidebarCollapsed }">
      <header class="user-header">
        <div class="container-fluid">
          <div class="row align-items-center">
            <div class="col">
              <div class="d-flex align-items-center">
                <h1 class="page-title">{{ pageTitle }}</h1>
                <div class="page-actions ml-3" v-if="showActions">
                  <slot name="page-actions"></slot>
                </div>
              </div>
            </div>
            <div class="col-auto">
              <div class="quick-action">
                <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                  <i class="fas fa-plus"></i> Action rapide
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <router-link to="/expenses/create" class="dropdown-item">
                    <i class="fas fa-shopping-cart"></i> Ajouter une dépense
                  </router-link>
                  <router-link to="/incomes/create" class="dropdown-item">
                    <i class="fas fa-coins"></i> Ajouter un revenu
                  </router-link>
                  <router-link to="/budgets/create" class="dropdown-item">
                    <i class="fas fa-money-bill-wave"></i> Créer un budget
                  </router-link>
                  <router-link to="/goals/create" class="dropdown-item">
                    <i class="fas fa-bullseye"></i> Définir un objectif
                  </router-link>
                </div>
              </div>
              
              <notifications-dropdown />
              
              <div class="user-dropdown">
                <div class="dropdown">
                  <button class="btn dropdown-toggle" type="button" id="userMenu" data-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ user.prenom }}</span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <router-link to="/profile" class="dropdown-item">
                      <i class="fas fa-user"></i> Profil
                    </router-link>
                    <router-link to="/accounts" class="dropdown-item">
                      <i class="fas fa-university"></i> Mes comptes
                    </router-link>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item" @click.prevent="logout">
                      <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>
      
      <main class="user-main">
        <div class="container-fluid">
          <slot></slot>
        </div>
      </main>
      
      <footer class="user-footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col">
              <p class="mb-0">&copy; {{ currentYear }} BudgetManager. Tous droits réservés.</p>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </div>
</template>

<script>
import Sidebar from '../UI/Sidebar.vue';
import NotificationsDropdown from '../UI/Notifications.vue';
import { computed, ref, watch, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

export default {
  name: 'UserLayout',
  components: {
    Sidebar,
    NotificationsDropdown
  },
  props: {
    title: {
      type: String,
      default: 'Dashboard'
    },
    showActions: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    const store = useStore();
    const router = useRouter();
    const route = useRoute();
    
    // État de la sidebar (collapsed ou non)
    const isSidebarCollapsed = ref(false);
    
    // Récupérer l'année courante pour le footer
    const currentYear = new Date().getFullYear();
    
    // Récupérer les données de l'utilisateur depuis le store
    const user = computed(() => store.getters['auth/user']);
    
    // Définir le titre de la page en fonction des props ou de la route
    const pageTitle = ref(props.title);
    
    // Fonction pour mettre à jour le titre de la page en fonction de la route
    const updatePageTitle = () => {
      if (props.title !== 'Dashboard') {
        pageTitle.value = props.title;
        return;
      }
      
      switch(route.name) {
        case 'dashboard':
          pageTitle.value = 'Tableau de bord';
          break;
        case 'budgets':
          pageTitle.value = 'Mes budgets';
          break;
        case 'expenses':
          pageTitle.value = 'Mes dépenses';
          break;
        case 'incomes':
          pageTitle.value = 'Mes revenus';
          break;
        case 'goals':
          pageTitle.value = 'Mes objectifs';
          break;
        case 'accounts':
          pageTitle.value = 'Mes comptes';
          break;
        case 'reports':
          pageTitle.value = 'Rapports financiers';
          break;
        case 'profile':
          pageTitle.value = 'Mon profil';
          break;
        default:
          pageTitle.value = route.meta.title || 'Dashboard';
      }
    };
    
    // Méthode de déconnexion
    const logout = async () => {
      try {
        await store.dispatch('auth/logout');
        router.push('/login');
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);
      }
    };
    
    onMounted(() => {
      // Charger les données de l'utilisateur si nécessaire
      if (!user.value && store.getters['auth/isAuthenticated']) {
        store.dispatch('auth/fetchUserProfile');
      }
      
      // Mettre à jour le titre de la page
      updatePageTitle();
      
      // Vérifier l'état de la sidebar
      const savedState = localStorage.getItem('sidebarCollapsed');
      if (savedState !== null) {
        isSidebarCollapsed.value = savedState === 'true';
      }
    });
    
    // Observer les changements de route pour mettre à jour le titre
    watch(() => route.name, updatePageTitle);
    watch(() => props.title, updatePageTitle);
    
    // Observer les changements de l'état de la sidebar
    watch(() => localStorage.getItem('sidebarCollapsed'), (newVal) => {
      isSidebarCollapsed.value = newVal === 'true';
    });
    
    return {
      user,
      currentYear,
      pageTitle,
      isSidebarCollapsed,
      logout
    };
  }
};
</script>

<style scoped>
.user-layout {
  min-height: 100vh;
  display: flex;
}

.content-wrapper {
  flex: 1;
  margin-left: 250px;
  transition: margin-left 0.3s;
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

.content-wrapper.sidebar-collapsed {
  margin-left: 70px;
}

.user-header {
  background-color: #fff;
  border-bottom: 1px solid #e9ecef;
  padding: 1rem;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.page-title {
  font-size: 1.5rem;
  font-weight: 600;
  margin: 0;
  color: #2c3e50;
}

.quick-action,
.user-dropdown,
.notifications-dropdown {
  display: inline-block;
  margin-left: 15px;
}

.user-dropdown button {
  display: flex;
  align-items: center;
  background: none;
  border: none;
  color: #2c3e50;
}

.user-dropdown i {
  font-size: 1.5rem;
  margin-right: 0.5rem;
}

.user-main {
  flex: 1;
  padding: 2rem;
  background-color: #f8f9fa;
}

.user-footer {
  background-color: #fff;
  border-top: 1px solid #e9ecef;
  padding: 1rem;
  font-size: 0.9rem;
  color: #6c757d;
}

/* Responsive */
@media (max-width: 768px) {
  .content-wrapper {
    margin-left: 0;
  }
  
  .content-wrapper.sidebar-collapsed {
    margin-left: 70px;
  }
  
  .page-title {
    font-size: 1.2rem;
  }
  
  .user-dropdown span {
    display: none;
  }
}
</style>