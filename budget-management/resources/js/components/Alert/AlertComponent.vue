<template>
  <div class="alert-component">
    <div v-if="showForm" class="card mb-4">
      <div class="card-header">
        <h5 class="mb-0">{{ isEditing ? 'Modifier alerte' : 'Nouvelle alerte' }}</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="form-group">
            <label for="categorie_budget_id">Catégorie budgétaire</label>
            <select 
              class="form-control" 
              id="categorie_budget_id" 
              v-model="alertData.categorie_budget_id"
              required
              :disabled="isEditing"
            >
              <option value="">Sélectionner une catégorie</option>
              <optgroup 
                v-for="budget in budgets" 
                :key="budget.id" 
                :label="budget.nom"
              >
                <option 
                  v-for="categoryBudget in budget.categoriesBudget" 
                  :key="categoryBudget.id" 
                  :value="categoryBudget.id"
                >
                  {{ categoryBudget.categorie.nom }}
                </option>
              </optgroup>
            </select>
          </div>
          
          <div class="form-group">
            <label for="type">Type d'alerte</label>
            <select 
              class="form-control" 
              id="type" 
              v-model="alertData.type" 
              required
            >
              <option value="pourcentage">Pourcentage du budget</option>
              <option value="montant">Montant spécifique</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="seuil">Seuil</label>
            <div class="input-group">
              <input 
                type="number" 
                class="form-control" 
                id="seuil" 
                v-model="alertData.seuil" 
                :min="alertData.type === 'pourcentage' ? 1 : 0" 
                :max="alertData.type === 'pourcentage' ? 100 : null" 
                step="1"
                required
              >
              <div class="input-group-append">
                <span class="input-group-text">
                  {{ alertData.type === 'pourcentage' ? '%' : 'MAD' }}
                </span>
              </div>
            </div>
            <small class="form-text text-muted" v-if="alertData.type === 'pourcentage'">
              Vous serez alerté lorsque les dépenses atteindront ce pourcentage du budget alloué.
            </small>
            <small class="form-text text-muted" v-else>
              Vous serez alerté lorsque les dépenses atteindront ce montant.
            </small>
          </div>
          
          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" @click="cancelForm">
              <i class="fas fa-times"></i> Annuler
            </button>
            <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
              <i class="fas fa-save"></i> {{ isEditing ? 'Mettre à jour' : 'Créer l\'alerte' }}
            </button>
          </div>
        </form>
      </div>
    </div>
    
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Alertes budgétaires</h5>
        <button 
          v-if="!showForm" 
          class="btn btn-sm btn-primary" 
          @click="showForm = true"
        >
          <i class="fas fa-plus"></i> Nouvelle alerte
        </button>
      </div>
      <div class="card-body">
        <div v-if="loading" class="text-center py-3">
          <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Chargement...</span>
          </div>
        </div>
        
        <div v-else-if="alerts.length === 0" class="text-center py-3">
          <p>Aucune alerte configurée.</p>
          <button class="btn btn-primary" @click="showForm = true">
            <i class="fas fa-plus"></i> Créer une alerte
          </button>
        </div>
        
        <div v-else class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Catégorie</th>
                <th>Budget</th>
                <th>Type</th>
                <th>Seuil</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="alert in alerts" :key="alert.id">
                <td>
                  <i :class="['fas', getCategoryIcon(alert), 'mr-2']"></i>
                  {{ getCategoryName(alert) }}
                </td>
                <td>{{ getBudgetName(alert) }}</td>
                <td>{{ alert.type === 'pourcentage' ? 'Pourcentage' : 'Montant' }}</td>
                <td>
                  <span class="badge badge-primary">
                    {{ alert.type === 'pourcentage' ? alert.seuil + '%' : formatCurrency(alert.seuil) }}
                  </span>
                </td>
                <td>
                  <span 
                    class="badge" 
                    :class="alert.active ? 'badge-success' : 'badge-secondary'"
                  >
                    {{ alert.active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button 
                      class="btn btn-outline-primary" 
                      @click="editAlert(alert)"
                      title="Modifier"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button 
                      class="btn btn-outline-warning" 
                      @click="toggleAlert(alert)"
                      title="Activer/Désactiver"
                    >
                      <i :class="['fas', alert.active ? 'fa-bell-slash' : 'fa-bell']"></i>
                    </button>
                    <button 
                      class="btn btn-outline-danger" 
                      @click="deleteAlert(alert)"
                      title="Supprimer"
                    >
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue';
import { useStore } from 'vuex';

export default {
  name: 'AlertComponent',
  props: {
    categoryBudgetId: {
      type: [Number, String],
      required: false,
      default: null
    }
  },
  setup(props) {
    const store = useStore();
    
    // État du composant
    const showForm = ref(false);
    const isEditing = ref(false);
    const alerts = ref([]);
    const budgets = ref([]);
    const loading = ref(true);
    const isSubmitting = ref(false);
    
    // Données du formulaire
    const alertData = reactive({
      id: null,
      categorie_budget_id: props.categoryBudgetId || '',
      type: 'pourcentage',
      seuil: 80,
      active: true
    });
    
    // Au montage du composant, charger les alertes et les budgets
    onMounted(async () => {
      try {
        // Charger les budgets avec les catégories
        await store.dispatch('budgets/fetchBudgetsWithCategories');
        budgets.value = store.getters['budgets/budgetsWithCategories'];
        
        // Charger les alertes
        await loadAlerts();
      } catch (error) {
        console.error('Erreur lors du chargement des données:', error);
      } finally {
        loading.value = false;
      }
    });
    
    // Charger les alertes depuis l'API
    const loadAlerts = async () => {
      try {
        await store.dispatch('alerts/fetchAlerts');
        alerts.value = store.getters['alerts/alerts'];
      } catch (error) {
        console.error('Erreur lors du chargement des alertes:', error);
      }
    };
    
    // Récupérer le nom de la catégorie pour une alerte
    const getCategoryName = (alert) => {
      if (!alert.categorieBudget || !alert.categorieBudget.categorie) {
        return 'Non disponible';
      }
      
      return alert.categorieBudget.categorie.nom;
    };
    
    // Récupérer l'icône de la catégorie pour une alerte
    const getCategoryIcon = (alert) => {
      if (!alert.categorieBudget || !alert.categorieBudget.categorie) {
        return 'fa-question';
      }
      
      return alert.categorieBudget.categorie.icone;
    };
    
    // Récupérer le nom du budget pour une alerte
    const getBudgetName = (alert) => {
      if (!alert.categorieBudget || !alert.categorieBudget.budget) {
        return 'Non disponible';
      }
      
      return alert.categorieBudget.budget.nom;
    };
    
    // Formater un montant en devise
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    };
    
    // Éditer une alerte
    const editAlert = (alert) => {
      isEditing.value = true;
      showForm.value = true;
      
      alertData.id = alert.id;
      alertData.categorie_budget_id = alert.categorie_budget_id;
      alertData.type = alert.type;
      alertData.seuil = alert.seuil;
      alertData.active = alert.active;
    };
    
    // Activer/désactiver une alerte
    const toggleAlert = async (alert) => {
      try {
        await store.dispatch('alerts/toggleAlert', alert.id);
        await loadAlerts();
      } catch (error) {
        console.error('Erreur lors du changement de statut de l\'alerte:', error);
      }
    };
    
    // Supprimer une alerte
    const deleteAlert = async (alert) => {
      if (!confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')) {
        return;
      }
      
      try {
        await store.dispatch('alerts/deleteAlert', alert.id);
        await loadAlerts();
      } catch (error) {
        console.error('Erreur lors de la suppression de l\'alerte:', error);
      }
    };
    
    // Soumettre le formulaire
    const submitForm = async () => {
      isSubmitting.value = true;
      
      try {
        if (isEditing.value) {
          await store.dispatch('alerts/updateAlert', alertData);
        } else {
          await store.dispatch('alerts/createAlert', alertData);
        }
        
        // Réinitialiser le formulaire
        resetForm();
        
        // Recharger les alertes
        await loadAlerts();
      } catch (error) {
        console.error('Erreur lors de l\'enregistrement de l\'alerte:', error);
      } finally {
        isSubmitting.value = false;
      }
    };
    
    // Annuler le formulaire
    const cancelForm = () => {
      resetForm();
    };
    
    // Réinitialiser le formulaire
    const resetForm = () => {
      isEditing.value = false;
      showForm.value = false;
      
      alertData.id = null;
      alertData.categorie_budget_id = props.categoryBudgetId || '';
      alertData.type = 'pourcentage';
      alertData.seuil = 80;
      alertData.active = true;
    };
    
    return {
      showForm,
      isEditing,
      alerts,
      budgets,
      loading,
      isSubmitting,
      alertData,
      getCategoryName,
      getCategoryIcon,
      getBudgetName,
      formatCurrency,
      editAlert,
      toggleAlert,
      deleteAlert,
      submitForm,
      cancelForm
    };
  }
};
</script>

<style scoped>
.alert-component {
  margin-bottom: 2rem;
}

.card {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.card-header h5 {
  font-size: 1rem;
  font-weight: 600;
}
</style>