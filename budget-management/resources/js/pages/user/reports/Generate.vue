<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Générer un rapport</h1>
      <router-link :to="{ name: 'user.reports.index' }" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour aux rapports
      </router-link>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Paramètres du rapport</h6>
      </div>
      <div class="card-body">
        <form @submit.prevent="generateReport">
          <div v-if="errors.length" class="alert alert-danger">
            <ul class="mb-0">
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>

          <div class="mb-3">
            <label for="title" class="form-label">Titre du rapport</label>
            <input 
              type="text" 
              id="title" 
              v-model="report.title"
              class="form-control" 
              required
              placeholder="Ex: Rapport financier janvier 2025"
            />
          </div>

          <div class="mb-3">
            <label for="type" class="form-label">Type de rapport</label>
            <select id="type" v-model="report.type" class="form-select" required>
              <option value="expenses">Rapport de dépenses</option>
              <option value="budgets">Rapport de budget</option>
              <option value="categories">Rapport par catégorie</option>
              <option value="accounts">Rapport des comptes</option>
              <option value="complete">Rapport financier complet</option>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="date_start" class="form-label">Date de début</label>
              <input 
                type="date" 
                id="date_start" 
                v-model="report.date_start"
                class="form-control" 
                required
              />
            </div>
            <div class="col-md-6 mb-3">
              <label for="date_end" class="form-label">Date de fin</label>
              <input 
                type="date" 
                id="date_end" 
                v-model="report.date_end"
                class="form-control" 
                required
                :min="report.date_start"
              />
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label d-block">Inclure dans le rapport</label>
              <div class="form-check form-check-inline">
                <input 
                  type="checkbox" 
                  id="include_charts" 
                  v-model="report.include_charts"
                  class="form-check-input" 
                />
                <label class="form-check-label" for="include_charts">Graphiques</label>
              </div>
              <div class="form-check form-check-inline">
                <input 
                  type="checkbox" 
                  id="include_tables" 
                  v-model="report.include_tables"
                  class="form-check-input" 
                />
                <label class="form-check-label" for="include_tables">Tableaux détaillés</label>
              </div>
              <div class="form-check form-check-inline">
                <input 
                  type="checkbox" 
                  id="include_summary" 
                  v-model="report.include_summary"
                  class="form-check-input" 
                />
                <label class="form-check-label" for="include_summary">Résumé</label>
              </div>
            </div>
            <div class="col-md-6">
              <label for="format" class="form-label">Format du rapport</label>
              <select id="format" v-model="report.format" class="form-select">
                <option value="html">HTML (vue en ligne)</option>
                <option value="pdf">PDF (téléchargement)</option>
                <option value="excel">Excel (téléchargement)</option>
              </select>
            </div>
          </div>

          <div v-if="report.type === 'expenses' || report.type === 'complete'" class="mb-3">
            <label class="form-label">Filtrer par catégories (optionnel)</label>
            <select v-model="report.categories" class="form-select" multiple size="5">
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.nom }}
              </option>
            </select>
            <small class="form-text text-muted">
              Laissez vide pour inclure toutes les catégories. Utilisez CTRL+Clic pour sélectionner plusieurs catégories.
            </small>
          </div>

          <div v-if="report.type === 'accounts' || report.type === 'complete'" class="mb-3">
            <label class="form-label">Filtrer par comptes (optionnel)</label>
            <select v-model="report.accounts" class="form-select" multiple size="5">
              <option v-for="account in accounts" :key="account.id" :value="account.id">
                {{ account.nom }}
              </option>
            </select>
            <small class="form-text text-muted">
              Laissez vide pour inclure tous les comptes. Utilisez CTRL+Clic pour sélectionner plusieurs comptes.
            </small>
          </div>

          <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-outline-secondary" @click="resetForm">
              <i class="fas fa-undo me-1"></i> Réinitialiser
            </button>
            <button type="submit" class="btn btn-primary" :disabled="isGenerating">
              <i class="fas fa-file-alt me-1"></i> 
              <span v-if="isGenerating">
                <i class="fas fa-spinner fa-spin me-1"></i> Génération en cours...
              </span>
              <span v-else>Générer le rapport</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Rapports récents</h6>
      </div>
      <div class="card-body">
        <div v-if="loadingReports" class="text-center py-3">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
          </div>
        </div>
        <div v-else-if="recentReports.length === 0" class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          Vous n'avez pas encore généré de rapports.
        </div>
        <div v-else>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Titre</th>
                  <th>Type</th>
                  <th>Période</th>
                  <th>Format</th>
                  <th>Date de création</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="report in recentReports" :key="report.id">
                  <td>{{ report.title }}</td>
                  <td>{{ getReportTypeName(report.type) }}</td>
                  <td>{{ formatDate(report.date_start) }} - {{ formatDate(report.date_end) }}</td>
                  <td>{{ report.format.toUpperCase() }}</td>
                  <td>{{ formatDate(report.created_at) }}</td>
                  <td>
                    <div class="btn-group">
                      <router-link 
                        :to="{ name: 'user.reports.view', params: { id: report.id } }" 
                        class="btn btn-sm btn-info"
                        v-if="report.format === 'html'"
                      >
                        <i class="fas fa-eye"></i>
                      </router-link>
                      <a 
                        :href="report.file_url" 
                        class="btn btn-sm btn-success"
                        target="_blank"
                        download
                      >
                        <i class="fas fa-download"></i>
                      </a>
                      <button 
                        class="btn btn-sm btn-danger" 
                        @click="deleteReport(report)"
                      >
                        <i class="fas fa-trash"></i>
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
  </div>
</template>

<script>
export default {
  name: 'UserReportGenerate',
  data() {
    return {
      report: {
        title: '',
        type: 'expenses',
        date_start: this.getDefaultStartDate(),
        date_end: this.getDefaultEndDate(),
        include_charts: true,
        include_tables: true,
        include_summary: true,
        format: 'html',
        categories: [],
        accounts: []
      },
      categories: [],
      accounts: [],
      recentReports: [],
      loadingReports: true,
      isGenerating: false,
      errors: []
    };
  },
  methods: {
    getDefaultStartDate() {
      const date = new Date();
      date.setMonth(date.getMonth() - 1);
      return this.formatDateForInput(date);
    },
    getDefaultEndDate() {
      return this.formatDateForInput(new Date());
    },
    formatDateForInput(date) {
      const d = new Date(date);
      const year = d.getFullYear();
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    getReportTypeName(type) {
      switch (type) {
        case 'expenses':
          return 'Dépenses';
        case 'budgets':
          return 'Budget';
        case 'categories':
          return 'Catégories';
        case 'accounts':
          return 'Comptes';
        case 'complete':
          return 'Rapport complet';
        default:
          return 'Inconnu';
      }
    },
    fetchCategories() {
      axios.get('/api/categories')
        .then(response => {
          this.categories = response.data.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des catégories:', error);
        });
    },
    fetchAccounts() {
      axios.get('/api/user/accounts')
        .then(response => {
          this.accounts = response.data.data;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des comptes:', error);
        });
    },
    fetchRecentReports() {
      this.loadingReports = true;
      
      axios.get('/api/user/reports')
        .then(response => {
          this.recentReports = response.data.data;
          this.loadingReports = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement des rapports récents:', error);
          this.loadingReports = false;
        });
    },
    validateForm() {
      this.errors = [];

      if (!this.report.title) {
        this.errors.push('Le titre du rapport est requis.');
      }

      if (!this.report.date_start || !this.report.date_end) {
        this.errors.push('Les dates de début et de fin sont requises.');
      } else {
        const startDate = new Date(this.report.date_start);
        const endDate = new Date(this.report.date_end);
        
        if (endDate < startDate) {
          this.errors.push('La date de fin doit être postérieure à la date de début.');
        }
      }

      if (!this.report.include_charts && !this.report.include_tables && !this.report.include_summary) {
        this.errors.push('Veuillez sélectionner au moins un élément à inclure dans le rapport.');
      }

      return this.errors.length === 0;
    },
    resetForm() {
      this.report = {
        title: '',
        type: 'expenses',
        date_start: this.getDefaultStartDate(),
        date_end: this.getDefaultEndDate(),
        include_charts: true,
        include_tables: true,
        include_summary: true,
        format: 'html',
        categories: [],
        accounts: []
      };
    },
    generateReport() {
      if (!this.validateForm()) {
        return;
      }

      this.isGenerating = true;
      
      axios.post('/api/user/reports', this.report)
        .then(response => {
          this.isGenerating = false;
          this.$toasted.success('Rapport généré avec succès!');
          
          // Ajouter le nouveau rapport à la liste
          this.fetchRecentReports();
          
          // Rediriger vers la vue du rapport si c'est un rapport HTML
          if (this.report.format === 'html') {
            this.$router.push({ 
              name: 'user.reports.view', 
              params: { id: response.data.data.id } 
            });
          } else {
            // Sinon, ouvrir le lien de téléchargement
            window.open(response.data.data.file_url, '_blank');
          }
        })
        .catch(error => {
          this.isGenerating = false;
          
          if (error.response && error.response.data && error.response.data.errors) {
            const serverErrors = error.response.data.errors;
            this.errors = Object.values(serverErrors).flat();
          } else {
            this.errors = ['Une erreur est survenue lors de la génération du rapport.'];
          }
        });
    },
    deleteReport(report) {
      if (confirm(`Êtes-vous sûr de vouloir supprimer le rapport "${report.title}" ?`)) {
        axios.delete(`/api/user/reports/${report.id}`)
          .then(response => {
            this.$toasted.success('Rapport supprimé avec succès!');
            this.fetchRecentReports();
          })
          .catch(error => {
            console.error('Erreur lors de la suppression du rapport:', error);
            this.$toasted.error('Impossible de supprimer le rapport.');
          });
      }
    }
  },
  created() {
    this.fetchCategories();
    this.fetchAccounts();
    this.fetchRecentReports();
  },
  metaInfo() {
    return {
      title: 'Générer un rapport - Budget Manager'
    };
  }
};
</script>