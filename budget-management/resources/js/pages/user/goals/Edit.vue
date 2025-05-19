<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Modifier l'objectif</h1>
      <div>
        <router-link :to="{ name: 'user.goals.show', params: { id: $route.params.id } }" class="btn btn-info me-2">
          <i class="fas fa-eye me-1"></i> Voir les détails
        </router-link>
        <router-link :to="{ name: 'user.goals.index' }" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <div v-else class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Détails de l'objectif</h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="updateGoal">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="mb-3">
            <label for="titre" class="form-label">Titre de l'objectif</label>
            <input 
              type="text" 
              id="titre" 
              v-model="goal.titre"
              class="form-control" 
              required
            />
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description (optionnelle)</label>
            <textarea 
              id="description" 
              v-model="goal.description"
              class="form-control" 
              rows="3"
            ></textarea>
          </div>

          <div class="mb-3">
            <label for="type" class="form-label">Type d'objectif</label>
            <select id="type" v-model="goal.type" class="form-select" required>
              <option value="epargne">Épargne</option>
              <option value="dette">Remboursement de dette</option>
              <option value="achat">Achat important</option>
              <option value="depense">Réduction de dépense</option>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="montant_cible" class="form-label">Montant cible</label>
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
              <small class="form-text text-muted">
                {{ getGoalTargetDescription() }}
              </small>
            </div>
            <div class="col-md-6 mb-3">
              <label for="montant_actuel" class="form-label">Montant actuel</label>
              <div class="input-group">
                <span class="input-group-text">MAD</span>
                <input 
                  type="number" 
                  id="montant_actuel" 
                  v-model="goal.montant_actuel"
                  class="form-control" 
                  step="0.01" 
                  min="0"
                />
              </div>
              <small class="form-text text-muted">
                Progression actuelle de l'objectif
              </small>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="date_debut" class="form-label">Date de début</label>
              <input 
                type="date" 
                id="date_debut" 
                v-model="goal.date_debut"
                class="form-control" 
                required
              />
            </div>
            <div class="col-md-6 mb-3">
              <label for="date_fin" class="form-label">Date d'échéance</label>
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

          <div class="mb-3">
            <label for="categorie_id" class="form-label">Catégorie associée</label>
            <select id="categorie_id" v-model="goal.categorie_id" class="form-select">
              <option value="">Aucune catégorie</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.nom }}
              </option>
            </select>
          </div>

          <div class="mb-3">
            <label for="statut" class="form-label">Statut</label>
            <select id="statut" v-model="goal.statut" class="form-select" required>
              <option value="en cours">En cours</option>
              <option value="atteint">Atteint</option>
              <option value="abandonné">Abandonné</option>
              <option value="terminé">Terminé</option>
            </select>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="public" 
              v-model="goal.public"
              class="form-check-input" 
            />
            <label for="public" class="form-check-label">
              Objectif public
            </label>
            <small class="form-text text-muted d-block">
              Les objectifs publics peuvent être partagés avec d'autres utilisateurs
            </small>
          </div>

          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              id="notifications" 
              v-model="goal.notifications"
              class="form-check-input" 
            />
            <label for="notifications" class="form-check-label">
              Activer les notifications
            </label>
            <small class="form-text text-muted d-block">
              Recevez des notifications sur la progression de votre objectif
            </small>
          </div>

          <div class="mb-3">
            <label for="couleur" class="form-label">Couleur de l'objectif</label>
            <input 
              type="color" 
              id="couleur" 
              v-model="goal.couleur"
              class="form-control form-control-color" 
            />
          </div>

          <div class="d-flex justify-content-between">
            <router-link :to="{ name: 'user.goals.show', params: { id: goal.id } }" class="btn btn-secondary">
              Annuler
            </router-link>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save me-1"></i> Mettre à jour
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserGoalEdit',
  data() {
    return {
      goal: {
        id: null,
        titre: '',
        description: '',
        type: '',
        montant_cible: '',
        montant_actuel: 0,
        date_debut: '',
        date_fin: '',
        categorie_id: '',
        statut: '',
        public: false,
        notifications: true,
        couleur: ''
      },
      categories: [],
      loading: true,
      errors: []
    };
  },
  methods: {
    fetchGoal() {
      this.loading = true;
      
      axios.get(`/api/user/goals/${this.$route.params.id}`)
        .then(response => {
          this.goal = response.data.data;
          return axios.get('/api/categories');
        })
        .then(response => {
          this.categories = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement de l\'objectif:', error);
          this.$toasted.error('Impossible de charger les détails de l\'objectif.');
          this.loading = false;
          this.$router.push({ name: 'user.goals.index' });
        });
    },
    getGoalTargetDescription() {
      switch (this.goal.type) {
        case 'epargne':
          return 'Montant total à épargner';
        case 'dette':
          return 'Montant total de la dette à rembourser';
        case 'achat':
          return 'Coût de l\'achat prévu';
        case 'depense':
          return 'Montant de réduction de dépense visé';
        default:
          return 'Montant à atteindre';
      }
    },
    validateForm() {
      this.errors = [];

      if (!this.goal.titre) {
        this.errors.push('Le titre de l\'objectif est requis.');
      }

      if (!this.goal.montant_cible) {
        this.errors.push('Le montant cible est requis.');
      } else if (parseFloat(this.goal.montant_cible) <= 0) {
        this.errors.push('Le montant cible doit être supérieur à 0.');
      }

      if (this.goal.montant_actuel && parseFloat(this.goal.montant_actuel) < 0) {
        this.errors.push('Le montant actuel ne peut pas être négatif.');
      }

      if (!this.goal.date_debut || !this.goal.date_fin) {
        this.errors.push('Les dates de début et d\'échéance sont requises.');
      } else {
        const startDate = new Date(this.goal.date_debut);
        const endDate = new Date(this.goal.date_fin);
        
        if (endDate <= startDate) {
          this.errors.push('La date d\'échéance doit être postérieure à la date de début.');
        }
      }

      return this.errors.length === 0;
    },
    updateGoal() {
      if (!this.validateForm()) {
        return;
      }

      axios.put(`/api/user/goals/${this.goal.id}`, this.goal)
        .then(response => {
          this.$toasted.success('Objectif mis à jour avec succès!');
          this.$router.push({ name: 'user.goals.show', params: { id: this.goal.id } });
        })
        .catch(error => {
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.errors = Object.values(serverErrors).flat();
          } else {
            this.errors = ['Une erreur est survenue lors de la mise à jour de l\'objectif.'];
          }
        });
    }
  },
  created() {
    this.fetchGoal();
  },
  metaInfo() {
    return {
      title: 'Modifier l\'objectif - Budget Manager'
    };
  }
};
</script>