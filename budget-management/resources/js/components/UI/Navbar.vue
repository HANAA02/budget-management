<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <router-link class="navbar-brand" to="/">
        <i class="fas fa-wallet mr-2"></i>
        BudgetManager
      </router-link>
      
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/dashboard">
              <i class="fas fa-tachometer-alt"></i> Tableau de bord
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/budgets">
              <i class="fas fa-money-bill-wave"></i> Budgets
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/expenses">
              <i class="fas fa-shopping-cart"></i> Dépenses
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/incomes">
              <i class="fas fa-coins"></i> Revenus
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/goals">
              <i class="fas fa-bullseye"></i> Objectifs
            </router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/reports">
              <i class="fas fa-chart-line"></i> Rapports
            </router-link>
          </li>
        </ul>
        
        <div class="navbar-nav ml-auto d-flex align-items-center">
          <notifications-dropdown />
          
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
              <i class="fas fa-user-circle"></i> {{ user.prenom }}
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <router-link class="dropdown-item" to="/profile">
                <i class="fas fa-user"></i> Profil
              </router-link>
              <router-link class="dropdown-item" to="/accounts">
                <i class="fas fa-university"></i> Mes comptes
              </router-link>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="#" @click.prevent="logout">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import NotificationsDropdown from './Notifications.vue';
import { computed, onMounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

export default {
  name: 'Navbar',
  components: {
    NotificationsDropdown
  },
  setup() {
    const store = useStore();
    const router = useRouter();
    
    // Récupérer les données de l'utilisateur depuis le store
    const user = computed(() => store.getters['auth/user']);
    
    // Charger les données de l'utilisateur au montage du composant
    onMounted(() => {
      if (!user.value && store.getters['auth/isAuthenticated']) {
        store.dispatch('auth/fetchUserProfile');
      }
    });
    
    // Méthode de déconnexion
    const logout = async () => {
      try {
        await store.dispatch('auth/logout');
        router.push('/login');
      } catch (error) {
        console.error('Erreur lors de la déconnexion:', error);
      }
    };
    
    return {
      user,
      logout
    };
  }
};
</script>

<style scoped>
.navbar {
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.navbar-brand {
  font-weight: 700;
  font-size: 1.3rem;
}

.nav-link {
  font-weight: 500;
  padding: 0.5rem 1rem;
  transition: all 0.2s;
}

.nav-link:hover {
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}

.nav-link i {
  margin-right: 5px;
}
</style>