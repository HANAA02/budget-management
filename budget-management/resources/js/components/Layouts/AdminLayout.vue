<template>
  <div class="admin-layout">
    <sidebar />
    
    <div class="content-wrapper" :class="{ 'sidebar-collapsed': isSidebarCollapsed }">
      <header class="admin-header">
        <div class="container-fluid">
          <div class="row align-items-center">
            <div class="col">
              <h1 class="page-title">{{ pageTitle }}</h1>
            </div>
            <div class="col-auto">
              <notifications-dropdown />
              <div class="user-dropdown">
                <div class="dropdown">
                  <button class="btn dropdown-toggle" type="button" id="userMenu" data-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ user.prenom }} {{ user.nom }}</span>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <router-link to="/profile" class="dropdown-item">
                      <i class="fas fa-user"></i> Profil
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
      
      <main class="admin-main">
        <div class="container-fluid">
          <router-view></router-view>
        </div>
      </main>
      
      <footer class="admin-footer">
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
  name: 'AdminLayout',
  components: {
    Sidebar,
    NotificationsDropdown
  },
  setup() {
    const store = useStore();
    const router = useRouter();
    const route = useRoute();
    
    // État de la sidebar (collapsed ou non)
    const isSidebarCollapsed = ref(false);
    
    // Récupérer l'année courante pour le footer
    const currentYear = new Date().getFullYear();
    
    // Récupérer les données de l'utilisateur depuis le store
    const user = computed(() => store.getters['auth/user']);
    
    // Définir le titre de la page en fonction de la route
    const pageTitle = ref('Administration');
    
    // Fonction pour mettre à jour le titre de la page en fonction de la route
    const updatePageTitle = () => {
      switch(route.name) {
        case 'admin-dashboard':
          pageTitle.value = 'Tableau de bord administrateur';
          break;
        case 'admin-users':
          pageTitle.value = 'Gestion des utilisateurs';
          break;
        case 'admin-categories':
          pageTitle.value = 'Gestion des catégories';
          break;
        case 'admin-settings':
          pageTitle.value = 'Paramètres système';
          break;
        default:
          pageTitle.value = 'Administration';
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
    
    // Vérifier que l'utilisateur est un administrateur
    onMounted(() => {
      if (!user.value) {
        store.dispatch('auth/fetchUserProfile').then(() => {
          const currentUser = store.getters['auth/user'];
          if (!currentUser || currentUser.role !== 'admin') {
            router.push('/dashboard');
          }
        });
      } else if (user.value.role !== 'admin') {
        router.push('/dashboard');
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
.admin-layout {
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

.admin-header {
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

.user-dropdown {
  margin-left: 1rem;
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

.admin-main {
  flex: 1;
  padding: 2rem;
  background-color: #f8f9fa;
}

.admin-footer {
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
}
</style>