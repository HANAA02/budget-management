<template>
  <div class="goal-form">
    <div class="card">
      <div class="card-header">
        <h3>{{ isEditing ? 'Modifier un objectif' : 'Créer un nouvel objectif' }}</h3>
      </div>
      <div class="card-body">
        <form @submit.prevent="submitForm">
          <div class="alert alert-danger" v-if="errors.length">
            <ul>
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="form-group mb-3">
            <label for="titre">Titre de l'objectif:</label>
            <input 
              type="text" 
              id="titre" 
              v-model="goal.titre" 
              class="form-control" 
              required
              placeholder="Ex: Économiser pour les vacances"
            />
          </div>

          <div class="form-group mb-3">
            <label for="categorie_id">Catégorie:</label>
            <select 
              id="categorie_id" 
              v-model="goal.categorie_id" 
              class="form-control" 
              required
            >
              <option value="" disabled>Sélectionnez une catégorie</option>
              <option 
                v-for="category in categories" 
                :key="category.id" 
                :value="category.id"
              >
                {{ category.nom }}
              </option>
            </select>
          </div>

          <div class="form-group mb-3">
            <label for="description">Description (optionnelle):</label>
            <textarea 
              id="description" 
              v-model="goal.description" 
              class="form-control" 
              rows="3"
              placeholder="Décrivez votre objectif plus en détail..."
            ></textarea>
          </div>

          <div class="form-group mb-3">
            <label for="montant_cible">Montant cible:</label>
            <div class="input-group">
              <span class="input-group-text">MAD</span>
              <input 
                type="number" 
                id="montant_cible" 
                v-model="goal.montant_cible" 
                class="form-control" 
                step="0.01" 
                min="0.01" 
                required
              />
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="date_debut">Date de début:</label>
                <input 
                  type="date" 
                  id="date_debut" 
                  v-model="goal.date_debut" 
                  class="form-control" 
                  required
                />
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="date_fin">Date de fin:</label>
                <input 
                  type="date" 
                  id="date_fin" 
                  v-model="goal.date_fin" 
                  class="form-control" 
                  required
                  :min="goal.date_debut"
                />
              </div>
            </div>
          </div>

          <div class="form-group mb-3">
            <label for="statut">Statut:</label>
            <select id="statut" v-model="goal.statut" class="form-control">
              <option value="en cours">En cours</option>
              <option value="atteint">Atteint</option>
              <option value="abandonné">Abandonné</option>
            </select>
          </div>

          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" @click="resetForm">Annuler</button>
            <button type="submit" class="btn btn-primary">
              {{ isEditing ? 'Mettre à jour' : 'Créer l\'objectif' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'GoalForm',
  props: {
    editingGoal: {
      type: Object,
      default: null
    }
  },
  data() {
    return {
      goal: {
        titre: '',
        categorie_id: '',
        description: '',
        montant_cible: '',
        date_debut: this.formatDateForInput(new Date()),
        date_fin: this.formatDateForInput(new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)), // +30 jours par défaut
        statut: 'en cours'
      },
      categories: [],
      errors: [],
      isEditing: false
    };
  },
  methods: {
    formatDateForInput(date) {
      const d = new Date(date);
      const year = d.getFullYear();
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    },
    resetForm() {
      this.goal = {
        titre: '',
        categorie_id: '',
        description: '',
        montant_cible: '',
        date_debut: this.formatDateForInput(new Date()),
        date_fin: this.formatDateForInput(new Date(Date.now() + 30 * 24 * 60 * 60 * 1000)),
        statut: 'en cours'
      };
      this.errors = [];
      this.isEditing = false;
      this.$emit('cancel');
    },
    loadCategories() {
      axios.get('/api/categories')
        .then(response => {
          this.categories = response.data.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des catégories:', error);
          this.errors.push('Impossible de charger les catégories.');
        });
    },
    validateForm() {
      this.errors = [];
      
      // Validation basique
      if (!this.goal.titre) {
        this.errors.push('Le titre est requis.');
      }
      if (!this.goal.categorie_id) {
        this.errors.push('Veuillez sélectionner une catégorie.');
      }
      if (!this.goal.montant_cible || this.goal.montant_cible <= 0) {
        this.errors.push('Veuillez entrer un montant cible valide.');
      }
      if (!this.goal.date_debut) {
        this.errors.push('La date de début est requise.');
      }
      if (!this.goal.date_fin) {
        this.errors.push('La date de fin est requise.');
      }
      
      // Vérifier que la date de fin est après la date de début
      if (this.goal.date_debut && this.goal.date_fin) {
        const debut = new Date(this.goal.date_debut);
        const fin = new Date(this.goal.date_fin);
        
        if (fin <= debut) {
          this.errors.push('La date de fin doit être postérieure à la date de début.');
        }
      }
      
      return this.errors.length === 0;
    },
    submitForm() {
      if (!this.validateForm()) {
        return;
      }
      
      const url = this.isEditing 
        ? `/api/goals/${this.goal.id}` 
        : '/api/goals';
      const method = this.isEditing ? 'put' : 'post';
      
      axios[method](url, this.goal)
        .then(response => {
          this.$emit('saved', response.data.data);
          this.resetForm();
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            Object.keys(serverErrors).forEach(key => {
              this.errors.push(serverErrors[key][0]);
            });
          } else {
            this.errors.push('Une erreur est survenue. Veuillez réessayer.');
          }
        });
    },
    initForm() {
      if (this.editingGoal) {
        this.goal = { ...this.editingGoal };
        this.isEditing = true;
      } else {
        this.resetForm();
      }
    }
  },
  created() {
    this.loadCategories();
    this.initForm();
  },
  watch: {
    editingGoal: {
      handler(newVal) {
        if (newVal) {
          this.initForm();
        }
      },
      deep: true
    }
  }
};
</script>

<style scoped>
.goal-form {
  max-width: 600px;
  margin: 0 auto;
}
</style>