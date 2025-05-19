<template>
  <div class="sidebar" :class="{ 'collapsed': isCollapsed }">
    <div class="logo-container">
      <router-link to="/dashboard" class="logo">
        <i class="fas fa-wallet"></i>
        <span v-if="!isCollapsed">BudgetManager</span>
      </router-link>
      <button class="toggle-btn" @click="toggleSidebar">
        <i :class="isCollapsed ? 'fas fa-angle-right' : 'fas fa-angle-left'"></i>
      </button>
    </div>
    
    <div class="user-info" v-if="!isCollapsed">
      <div class="user-avatar">
        <i class="fas fa-user-circle"></i>
      </div>
      <div class="user-details">
        <div class="user-name">{{ user.prenom }} {{ user.nom }}</div>
        <div class="user-role">{{ user.role === 'admin' ? 'Administrateur' : 'Utilisateur' }}</div>
      </div>
    </div>
    
    <div class="menu-container">
      <ul class="menu">
        <li class="menu-item" :class="{ 'active': isActive('/dashboard') }">
          <router-link to="/dashboard">
            <i class="fas fa-tachometer-alt"></i>
            <span v-if="!isCollapsed">Tableau de bord</span>
          </router-link>
        </li>
        
        <li class="menu-item" :class="{ 'active': isActive('/budgets') }">
          <router-link to="/budgets">
            <i class="fas fa-money-bill-wave"></i>
            <span v-if="!isCollapsed">Budgets</span>
          </router-link>
        </li>
        
        <li class="menu-item" :class="{ 'active': isActive('/expenses') }">
          <router-link to="/expenses">
            <i class="fas fa-shopping-cart"></i>
            <span v-if="!isCollapsed">Dépenses</span>
          </router-link>
        </li>
        
        <li class="menu-item" :class="{ 'active': isActive('/incomes') }">
          <router-link to="/incomes">
            <i class="fas fa-coins"></i>
            <span v-if="!isCollapsed">Revenus</span>
          </router-link>
        </li>
        
        <li class="menu-item" :class="{ 'active': isActive('/accounts') }">
          <router-link to="/accounts">
            <i class="fas fa-university"></i>
            <span v-if="!isCollapsed">Comptes</span>
          </router-link>
        </li>
        
        <li class="menu-item" :class="{ 'active': isActive('/goals') }">
          <router-link to="/goals">
            <i class="fas fa-bullseye"></i>
            <span v-if="!isCollapsed">Objectifs</span>
          </router-link>
        </li>
        
        <li class="menu-item" :class="{ 'active': isActive('/reports') }">
          <router-link to="/reports">
            <i class="fas fa-chart-line"></i>
            <span v-if="!isCollapsed">Rapports</span>
          </router-link>
        </li>
        
        <li class="menu-item" v-if="user.role === 'admin'" :class="{ 'active': isActive('/admin') }">
          <router-link to="/admin/dashboard">
            <i class="fas fa-cog"></i>
            <span v-if="!isCollapsed">Administration</span>
          </router-link>
        </li>
      </ul>
    </div>
    
    <div class="bottom-menu">
      <ul class="menu">
        <li class="menu-item" :class="{ 'active': isActive('/profile') }">
          <router-link to="/profile">
            <i class="fas fa-user"></i>
            <span v-if="!isCollapsed">Profil</span>
          </router-link>
        </li>
        
        <li class="menu-item">
          <a href="#" @click.prevent="logout">
            <i class="fas fa-sign-out-alt"></i>
            <span v-if="!isCollapsed">Déconnexion</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter, useRoute } from 'vue-router';

export default {
  name: 'Sidebar',
  setup() {
    const store = useStore();
    const router = useRouter();
    const route = useRoute();
    
    // État de la sidebar (collapse ou non)
    const isCollapsed = ref(false);
    
    // Récupérer les données de l'utilisateur depuis le store
    const user = computed(() => store.getters['auth/user']);
    
    // Fonction pour vérifier si un lien est actif
    const isActive = (path) => {
      return route.path.startsWith(path);
    };
    
    // Fonction pour ouvrir/fermer la sidebar
    const toggleSidebar = () => {
      isCollapsed.value = !isCollapsed.value;
      // Stocker l'état dans le localStorage pour le garder après refresh
      localStorage.setItem('sidebarCollapsed', isCollapsed.value);
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
    
    // Au montage du composant, récupérer l'état de la sidebar du localStorage
    onMounted(() => {
      const savedState = localStorage.getItem('sidebarCollapsed');
      if (savedState !== null) {
        isCollapsed.value = savedState === 'true';
      }
      
      // Charger les données de l'utilisateur si nécessaire
      if (!user.value && store.getters['auth/isAuthenticated']) {
        store.dispatch('auth/fetchUserProfile');
      }
    });
    
    return {
      isCollapsed,
      user,
      isActive,
      toggleSidebar,
      logout
    };
  }
};
</script>

<style scoped>
.sidebar {
  width: 250px;
  height: 100vh;
  background-color: #2c3e50;
  color: #ecf0f1;
  position: fixed;
  left: 0;
  top: 0;
  transition: all 0.3s;
  z-index: 1000;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
}

.sidebar.collapsed {
  width: 70px;
}

.logo-container {
  padding: 1rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
  color: #ecf0f1;
  font-weight: 700;
  font-size: 1.3rem;
  text-decoration: none;
  display: flex;
  align-items: center;
}

.logo i {
  margin-right: 10px;
  font-size: 1.5rem;
}

.toggle-btn {
  background: none;
  border: none;
  color: #ecf0f1;
  cursor: pointer;
  padding: 5px;
  border-radius: 50%;
  transition: all 0.2s;
}

.toggle-btn:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.user-info {
  padding: 1rem;
  display: flex;
  align-items: center;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background-color: #3498db;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
  font-size: 1.5rem;
}

.user-name {
  font-weight: 600;
  font-size: 0.9rem;
}

.user-role {
  font-size: 0.8rem;
  opacity: 0.7;
}

.menu-container {
  flex: 1;
  overflow-y: auto;
  padding-top: 1rem;
}

.menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.menu-item {
  margin-bottom: 5px;
}

.menu-item a {
  display: flex;
  align-items: center;
  padding: 0.75rem 1rem;
  color: #ecf0f1;
  text-decoration: none;
  transition: all 0.2s;
}

.menu-item a:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.menu-item.active a {
  background-color: #3498db;
  border-left: 4px solid #ecf0f1;
}

.menu-item i {
  margin-right: 10px;
  width: 20px;
  text-align: center;
  font-size: 1.1rem;
}

.bottom-menu {
  margin-top: auto;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 1rem 0;
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    transform: translateX(-100%);
  }
  
  .sidebar.collapsed {
    transform: translateX(0);
    width: 70px;
  }
}
</style>