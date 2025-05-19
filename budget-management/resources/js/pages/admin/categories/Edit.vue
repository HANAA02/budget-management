<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Modifier la catégorie</h1>
      <div>
        <router-link :to="{ name: 'admin.categories.show', params: { id: $route.params.id } }" class="btn btn-info me-2">
          <i class="fas fa-eye me-1"></i> Voir les détails
        </router-link>
        <router-link :to="{ name: 'admin.categories.index' }" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <div v-else class="card">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
          <i :class="category.icone || 'fas fa-folder'" class="me-2"></i>
          {{ category.nom }}
        </h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="updateCategory">
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
            />
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea 
              id="description" 
              v-model="category.description"
              class="form-control" 
              rows="3"
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

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="active" 
              v-model="category.active"
              class="form-check-input" 
            />
            <label for="active" class="form-check-label">Activer cette catégorie</label>
            <small class="form-text text-muted d-block">
              Les catégories inactives ne sont pas proposées aux utilisateurs lors de la création de budgets.
            </small>
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
              <i class="fas fa-save me-1"></i> Enregistrer les modifications
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Statistiques d'utilisation -->
    <div v-if="!loading" class="card mt-4">
      <div class="card-header">
        <h5 class="mb-0">Statistiques d'utilisation</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6 class="card-title">Nombre de budgets</h6>
                <p class="display-6">{{ categoryStats.budgets_count }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6 class="card-title">Nombre de dépenses</h6>
                <p class="display-6">{{ categoryStats.expenses_count }}</p>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h6 class="card-title">Montant moyen alloué</h6>
                <p class="display-6">{{ formatAmount(categoryStats.avg_amount) }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="alert alert-warning mt-3" v-if="categoryStats.budgets_count > 0">
          <i class="fas fa-exclamation-triangle me-2"></i>
          <strong>Attention :</strong> Cette catégorie est utilisée dans {{ categoryStats.budgets_count }} budget(s). La modification de son pourcentage par défaut n'affectera que les nouveaux budgets.
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminCategoryEdit',
  data() {
    return {
      category: {
        id: null,
        nom: '',
        description: '',
        pourcentage_defaut: 0,
        icone: '',
        couleur: '#3498db',
        active: true
      },
      categoryStats: {
        budgets_count: 0,
        expenses_count: 0,
        avg_amount: 0
      },
      loading: true,
      errors: []
    };
  },
  methods: {
    fetchCategory() {
      this.loading = true;
      axios.get(`/api/admin/categories/${this.$route.params.id}`)
        .then(response => {
          this.category = response.data.data;
          this.fetchCategoryStats();
        })
        .catch(error => {
          console.error('Erreur lors du chargement de la catégorie:', error);
          this.errors.push('Impossible de charger les détails de la catégorie.');
          this.loading = false;
        });
    },
    fetchCategoryStats() {
      axios.get(`/api/admin/categories/${this.$route.params.id}/stats`)
        .then(response => {
          this.categoryStats = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des statistiques:', error);
          this.loading = false;
        });
    },
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
    updateCategory() {
      if (!this.validateForm()) {
        return;
      }

      axios.put(`/api/admin/categories/${this.category.id}`, this.category)
        .then(response => {
          this.$toasted.success('Catégorie mise à jour avec succès!');
          this.$router.push({ name: 'admin.categories.show', params: { id: this.category.id } });
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            Object.keys(serverErrors).forEach(key => {
              this.errors.push(serverErrors[key][0]);
            });
          } else {
            this.errors.push('Une erreur est survenue lors de la mise à jour de la catégorie.');
          }
        });
    },
    formatAmount(amount) {
      return new Intl.NumberFormat('fr-MA', {
        style: 'currency',
        currency: 'MAD'
      }).format(amount);
    }
  },
  created() {
    this.fetchCategory();
  },
  metaInfo() {
    return {
      title: `Modifier la catégorie - Administration`
    };
  }
};
</script>