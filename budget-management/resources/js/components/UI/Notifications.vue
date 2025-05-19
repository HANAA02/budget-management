<template>
  <div class="notifications-dropdown">
    <div class="notifications-toggle" @click="toggleNotifications">
      <i class="fas fa-bell"></i>
      <span class="badge badge-danger" v-if="unreadCount > 0">{{ unreadCount }}</span>
    </div>
    
    <div class="notifications-menu" v-if="showNotifications">
      <div class="notifications-header">
        <h6>Notifications</h6>
        <button class="btn btn-sm btn-link" @click="markAllAsRead" v-if="unreadCount > 0">
          Tout marquer comme lu
        </button>
      </div>
      
      <div class="notifications-body">
        <div v-if="notifications.length === 0" class="text-center py-3">
          <p>Aucune notification pour le moment.</p>
        </div>
        
        <template v-else>
          <div 
            v-for="notification in notifications" 
            :key="notification.id" 
            class="notification-item"
            :class="{ 'unread': !notification.read_at }"
            @click="openNotification(notification)"
          >
            <div class="notification-icon" :class="getNotificationTypeClass(notification)">
              <i :class="getNotificationIcon(notification)"></i>
            </div>
            <div class="notification-content">
              <div class="notification-message">{{ notification.data.message }}</div>
              <div class="notification-time">{{ formatDate(notification.created_at) }}</div>
            </div>
            <div class="notification-actions">
              <button 
                class="btn btn-sm btn-link" 
                @click.stop="markAsRead(notification)"
                v-if="!notification.read_at"
              >
                <i class="fas fa-check"></i>
              </button>
            </div>
          </div>
        </template>
      </div>
      
      <div class="notifications-footer">
        <router-link to="/notifications" @click="showNotifications = false">
          Voir toutes les notifications
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/fr';

dayjs.extend(relativeTime);
dayjs.locale('fr');

export default {
  name: 'NotificationsDropdown',
  setup() {
    const store = useStore();
    const router = useRouter();
    
    const showNotifications = ref(false);
    
    // Récupérer les notifications depuis le store
    const notifications = computed(() => store.getters['notifications/notifications']);
    const unreadCount = computed(() => store.getters['notifications/unreadCount']);
    
    // Fonction pour ouvrir/fermer le dropdown de notifications
    const toggleNotifications = () => {
      showNotifications.value = !showNotifications.value;
    };
    
    // Fonction pour déterminer la classe CSS en fonction du type de notification
    const getNotificationTypeClass = (notification) => {
      const type = notification.data.type;
      
      switch(type) {
        case 'budget_limit_warning':
          return 'warning';
        case 'goal_achieved':
          return 'success';
        case 'new_expense':
          return 'info';
        default:
          return 'primary';
      }
    };
    
    // Fonction pour déterminer l'icône en fonction du type de notification
    const getNotificationIcon = (notification) => {
      const type = notification.data.type;
      
      switch(type) {
        case 'budget_limit_warning':
          return 'fas fa-exclamation-triangle';
        case 'goal_achieved':
          return 'fas fa-trophy';
        case 'new_expense':
          return 'fas fa-receipt';
        default:
          return 'fas fa-bell';
      }
    };
    
    // Fonction pour formater la date en format relatif (il y a 2 heures, etc.)
    const formatDate = (dateString) => {
      return dayjs(dateString).fromNow();
    };
    
    // Fonction pour marquer une notification comme lue
    const markAsRead = async (notification) => {
      if (!notification.read_at) {
        try {
          await store.dispatch('notifications/markAsRead', notification.id);
        } catch (error) {
          console.error('Erreur lors du marquage de la notification:', error);
        }
      }
    };
    
    // Fonction pour marquer toutes les notifications comme lues
    const markAllAsRead = async () => {
      try {
        await store.dispatch('notifications/markAllAsRead');
      } catch (error) {
        console.error('Erreur lors du marquage de toutes les notifications:', error);
      }
    };
    
    // Fonction pour ouvrir une notification et naviguer vers le lien correspondant
    const openNotification = (notification) => {
      markAsRead(notification);
      
      // Rediriger en fonction du type de notification
      const data = notification.data;
      
      switch(data.type) {
        case 'budget_limit_warning':
          router.push(`/budgets/${data.budget_id}`);
          break;
        case 'goal_achieved':
          router.push(`/goals/${data.goal_id}`);
          break;
        case 'new_expense':
          router.push(`/expenses/${data.expense_id}`);
          break;
        default:
          router.push('/notifications');
      }
      
      showNotifications.value = false;
    };
    
    // Fonction pour fermer le dropdown quand on clique en dehors
    const handleClickOutside = (event) => {
      const dropdown = document.querySelector('.notifications-dropdown');
      if (dropdown && !dropdown.contains(event.target)) {
        showNotifications.value = false;
      }
    };
    
    // Au montage du composant, charger les notifications et ajouter l'écouteur pour le clic extérieur
    onMounted(() => {
      store.dispatch('notifications/fetchNotifications');
      document.addEventListener('click', handleClickOutside);
      
      // Mettre en place le polling pour rafraîchir les notifications régulièrement
      const interval = setInterval(() => {
        store.dispatch('notifications/fetchNotifications');
      }, 60000); // Chaque minute
      
      onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
        clearInterval(interval);
      });
    });
    
    return {
      notifications,
      unreadCount,
      showNotifications,
      toggleNotifications,
      getNotificationTypeClass,
      getNotificationIcon,
      formatDate,
      markAsRead,
      markAllAsRead,
      openNotification
    };
  }
};
</script>

<style scoped>
.notifications-dropdown {
  position: relative;
  margin-right: 15px;
}

.notifications-toggle {
  position: relative;
  padding: 10px;
  cursor: pointer;
  color: #fff;
  font-size: 1.2rem;
}

.badge {
  position: absolute;
  top: 0;
  right: 0;
  font-size: 0.6rem;
}

.notifications-menu {
  position: absolute;
  top: 100%;
  right: 0;
  width: 350px;
  background-color: #fff;
  border-radius: 5px;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
  z-index: 1000;
  overflow: hidden;
}

.notifications-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  background-color: #f8f9fa;
}

.notifications-header h6 {
  margin: 0;
  font-weight: 600;
}

.notifications-body {
  max-height: 350px;
  overflow-y: auto;
}

.notification-item {
  display: flex;
  padding: 10px 15px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: background-color 0.2s;
}

.notification-item:hover {
  background-color: #f8f9fa;
}

.notification-item.unread {
  background-color: #e8f4fd;
}

.notification-icon {
  margin-right: 15px;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
}

.notification-icon.primary {
  background-color: #3498db;
}

.notification-icon.warning {
  background-color: #f39c12;
}

.notification-icon.success {
  background-color: #2ecc71;
}

.notification-icon.info {
  background-color: #3498db;
}

.notification-content {
  flex: 1;
}

.notification-message {
  font-size: 0.9rem;
  margin-bottom: 5px;
}

.notification-time {
  font-size: 0.8rem;
  color: #6c757d;
}

.notification-actions {
  align-self: flex-start;
}

.notifications-footer {
  padding: 10px 15px;
  text-align: center;
  border-top: 1px solid #eee;
  background-color: #f8f9fa;
}

.notifications-footer a {
  color: #3498db;
  font-size: 0.9rem;
  text-decoration: none;
}

.notifications-footer a:hover {
  text-decoration: underline;
}
</style>