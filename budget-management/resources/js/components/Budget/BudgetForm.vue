<template>
  <div class="budget-form">
    <form @submit.prevent="submitForm">
      <div class="form-group">
        <label for="nom">Nom du budget</label>
        <input 
          type="text" 
          class="form-control" 
          id="nom" 
          v-model="budget.nom" 
          required
          :class="{ 'is-invalid': errors.nom }"
        >
        <div class="invalid-feedback" v-if="errors.nom">{{ errors.nom }}</div>
      </div>
      
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label for="date_debut">Date de début</label>
            <input 
              type="date" 
              class="form-control" 
              id="date_debut" 
              v-model="budget.date_debut" 
              required
              :class="{ 'is-invalid': errors.date_debut }"
            >
            <div class="invalid-feedback" v-if="errors.date_debut">{{ errors.date_debut }}</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label for="date_fin">Date de fin</label>
            <input 
              type="date" 
              class="form-control" 
              id="date_fin" 
              v-model="budget.date_fin" 
              required
              :class="{ 'is-invalid': errors.date_fin }"
            >
            <div class="invalid-feedback" v-if="errors.date_fin">{{ errors.date_fin }}</div>
          </div>
        </div>
      </div>
      
      <div class="form-group">
        <label for="montant_total">Montant total</label>
        <div class="input-group">
          <input 
            type="number" 
            class="form-control" 
            id="montant_total" 
            v-model="budget.montant_total" 
            step="0.01" 
            min="0" 
            required
            :class="{ 'is-invalid': errors.montant_total }"
          >
          <div class="input-group-append">
            <span class="input-group-text">MAD</span>
          </div>
          <div class="invalid-feedback" v-if="errors.montant_total">{{ errors.montant_total }}</div>
        </div>
        <small class="form-text text-muted">
          Montant disponible pour ce mois: {{ formatCurrency(availableAmount) }}
        </small>
      </div>
      
      <div class="card mt-4" v-if="!isEditing">
        <div class="card-header bg-light">
          <h5 class="mb-0">Répartition automatique</h5>
        </div>
        <div class="card-body">
          <p class="card-text">
            Une fois le budget créé, la répartition sera faite automatiquement selon les pourcentages par défaut des catégories.
            Vous pourrez ensuite personnaliser cette répartition.
          </p>
          
          <div class="card bg-light mt-3">
            <div class="card-body">
              <h6 class="card-title">Répartition par défaut</h6>
              <div class="row">
                <div class="col-md-6" v-for="(category, index) in categories" :key="category.id">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                      <i :class="['fas', category.icone]"></i>
                      {{ category.nom }}
                    </div>
                    <div>
                      <span class="badge badge-primary">{{ category.pourcentage_defaut }}%</span>
                      <span class="ml-2">
                        {{ formatCurrency((category.pourcentage_defaut / 100) * budget.montant_total) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="form-group mt-4">
        <div class="d-flex justify-content-between">
          <button type="button" class="btn btn-secondary" @click="cancelForm">
            <i class="fas fa-times"></i> Annuler
          </button>
          <button type="submit" class="btn btn-primary" :disabled="isSubmitting">
            <i class="fas fa-save"></i> {{ isEditing ? 'Mettre à jour' : 'Créer le budget' }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';

export default {
  name: 'BudgetForm',
  props: {
    budgetData: {
      type: Object,
      required: false,
      default: null
    }
  },
  setup(props) {
    const store = useStore();
    const router = useRouter();
    
    // État pour le formulaire
    const budget = reactive({
      nom: '',
      date_debut: '',
      date_fin: '',
      montant_total: 0
    });
    
    const errors = reactive({});
    const isSubmitting = ref(false);
    const isEditing = computed(() => !!props.budgetData);
    
    // Récupérer le montant disponible pour ce mois
    const availableAmount = computed(() => {
      return store.getters['budgets/availableAmount'] || 0;
    });
    
    // Récupérer les catégories
    const categories = computed(() => {
      return store.getters['categories/categories'] || [];
    });
    
    // Au montage du composant
    onMounted(async () => {
      // Charger les catégories s'il n'y en a pas
      if (categories.value.length === 0) {
        await store.dispatch('categories/fetchCategories');
      }
      
      // Charger le montant disponible
      await store.dispatch('budgets/fetchAvailableAmount');
      
      // Si on est en mode édition, remplir le formulaire
      if (props.budgetData) {
        budget.nom = props.budgetData.nom;
        budget.date_debut = formatDateForInput(props.budgetData.date_debut);
        budget.date_fin = formatDateForInput(props.budgetData.date_fin);
        budget.montant_total = props.budgetData.montant_total;
      } else {
        // Sinon, initialiser avec le mois courant
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        
        budget.date_debut = formatDateForInput(firstDay);
        budget.date_fin = formatDateForInput(lastDay);
        budget.nom = `Budget ${getMonthName(today.getMonth())} ${today.getFullYear()}`;
      }
    });
    
    // Formater une date pour l'input de type date
    const formatDateForInput = (date) => {
      if (!date) return '';
      
      if (typeof date === 'string') {
        const parts = date.split('-');
        if (parts.length === 3) {
          return date;
        }
        return '';
      }
      
      const d = new Date(date);
      return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    };
    
    // Obtenir le nom du mois
    const getMonthName = (monthIndex) => {
      const months = [
        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
      ];
      return months[monthIndex];
    };
    
    // Formater un montant en devise
    const formatCurrency = (amount) => {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    };
    
    // Valider le formulaire
    const validateForm = () => {
      errors.nom = budget.nom ? '' : 'Le nom du budget est requis';
      errors.date_debut = budget.date_debut ? '' : 'La date de début est requise';
      errors.date_fin = budget.date_fin ? '' : 'La date de fin est requise';
      errors.montant_total = budget.montant_total > 0 ? '' : 'Le montant total doit être supérieur à 0';
      
      if (budget.date_debut && budget.date_fin) {
        const start = new Date(budget.date_debut);
        const end = new Date(budget.date_fin);
        
        if (start > end) {
          errors.date_fin = 'La date de fin doit être postérieure à la date de début';
        }
      }
      
      return !Object.values(errors).some(error => error);
    };
    
    // Soumettre le formulaire
    const submitForm = async () => {
      if (!validateForm()) {
        return;
      }
      
      isSubmitting.value = true;
      
      try {
        if (isEditing.value) {
          // Mise à jour d'un budget existant
          await store.dispatch('budgets/updateBudget', {
            id: props.budgetData.id,
            ...budget
          });
          
          router.push(`/budgets/${props.budgetData.id}`);
        } else {
          // Création d'un nouveau budget
          const newBudget = await store.dispatch('budgets/createBudget', budget);
          
          router.push(`/budgets/${newBudget.id}/allocate`);
        }
      } catch (error) {
        console.error('Erreur lors de la création du budget:', error);
        
        // Gérer les erreurs de validation du serveur
        if (error.response && error.response.data && error.response.data.errors) {
          Object.assign(errors, error.response.data.errors);
        }
      } finally {
        isSubmitting.value = false;
      }
    };
    
    // Annuler le formulaire
    const cancelForm = () => {
      router.back();
    };
    
    return {
      budget,
      errors,
      isSubmitting,
      isEditing,
      categories,
      availableAmount,
      formatCurrency,
      submitForm,
      cancelForm
    };
  }
};
</script>

<style scoped>
.budget-form {
  background-color: #fff;
  border-radius: 5px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  padding: 1.5rem;
}

.card-header h5 {
  font-size: 1rem;
  font-weight: 600;
}
</style>