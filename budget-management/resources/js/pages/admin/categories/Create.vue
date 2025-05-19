<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Créer une nouvelle catégorie</h1>
      <router-link :to="{ name: 'admin.categories.index' }" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
      </router-link>
    </div>

    <div class="card">
      <div class="card-body">
        <form @submit.prevent="saveCategory">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="mb-3">
            <label for="nom" class="form-label">Nom de la catégorie</label>
            <input 
              type="text" 
              id="nom" 
              v-model="category.nom"
              class="form-control" 
              required
              placeholder="Ex: Logement, Alimentation, Transport..."
            />
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea 
              id="description" 
              v-model="category.description"
              class="form-control" 
              rows="3"
              placeholder="Description détaillée de cette catégorie de dépenses..."
            ></textarea>
          </div>

          <div class="mb-3">
            <label for="pourcentage_defaut" class="form-label">Pourcentage par défaut (%)</label>
            <div class="input-group">
              <input 
                type="number" 
                id="pourcentage_defaut" 
                v-model="category.pourcentage_defaut"
                class="form-control" 
                min="0" 
                max="100" 
                step="0.01"
                required
                placeholder="Ex: 25.5"
              />
              <span class="input-group-text">%</span>
            </div>
            <small class="form-text text-muted">
              Ce pourcentage sera appliqué par défaut lors de la création d'un nouveau budget.
            </small>
          </div>

          <div class="mb-3">
            <label for="icone" class="form-label">Icône</label>
            <div class="input-group">
              <span class="input-group-text">
                <i :class="category.icone || 'fas fa-question-circle'"></i>
              </span>
              <input 
                type="text" 
                id="icone" 
                v-model="category.icone"
                class="form-control" 
                placeholder="Ex: fas fa-home, fas fa-utensils..."
              />
            </div>
            <small class="form-text text-muted">
              Entrez une classe d'icône FontAwesome (ex: fas fa-home).
            </small>
          </div>

          <div class="mb-3">
            <label class="form-label">Couleur</label>
            <div class="d-flex align-items-center">
              <input 
                type="color" 
                v-model="category.couleur"
                class="form-control form-control-color me-2"
              />
              <span>{{ category.couleur }}</span>
            </div>
          </div>

          <hr class="my-4">

          <div class="d-flex justify-content-between">
            <button 
              type="button" 
              class="btn btn-secondary"
              @click="$router.push({ name: 'admin.categories.index' })"
            >
              Annuler
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Créer la catégorie
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminCategoryCreate',
  data() {
    return {
      category: {
        nom: '',
        description: '',
        pourcentage_defaut: 0,
        icone: 'fas fa-folder',
        couleur: '#3498db'
      },
      errors: []
    };
  },
  methods: {
    validateForm() {
      this.errors = [];

      if (!this.category.nom.trim()) {
        this.errors.push('Le nom de la catégorie est requis.');
      }

      if (this.category.pourcentage_defaut < 0 || this.category.pourcentage_defaut > 100) {
        this.errors.push('Le pourcentage doit être compris entre 0 et 100.');
      }

      return this.errors.length === 0;
    },
    saveCategory() {
      if (!this.validateForm()) {
        return;
      }

      axios.post('/api/admin/categories', this.category)
        .then(response => {
          this.$toasted.success('Catégorie créée avec succès!');
          this.$router.push({ name: 'admin.categories.show', params: { id: response.data.data.id } });
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            Object.keys(serverErrors).forEach(key => {
              this.errors.push(serverErrors[key][0]);
            });
          } else {
            this.errors.push('Une erreur est survenue lors de la création de la catégorie.');
          }
        });
    }
  },
  metaInfo() {
    return {
      title: 'Créer une catégorie - Administration'
    };
  }
};
</script>