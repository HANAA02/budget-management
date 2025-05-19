<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>{{ report.title }}</h1>
      <div>
        <a 
          v-if="report.file_url" 
          :href="report.file_url" 
          class="btn btn-success me-2"
          target="_blank"
          download
        >
          <i class="fas fa-download me-1"></i> Télécharger
        </a>
        <router-link :to="{ name: 'user.reports.generate' }" class="btn btn-primary me-2">
          <i class="fas fa-file-alt me-1"></i> Nouveau rapport
        </router-link>
        <router-link :to="{ name: 'user.reports.index' }" class="btn btn-secondary">
          <i class="fas fa-arrow-left me-1"></i> Retour aux rapports
        </router-link>
      </div>
    </div>

    <div v-if="loading" class="d-flex justify-content-center my-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>

    <div v-else class="card shadow mb-4">
      <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">{{ getReportTypeName(report.type) }}</h6>
        <span class="badge bg-primary">{{ formatDate(report.date_start) }} - {{ formatDate(report.date_end) }}</span>
      </div>
      <div class="card-body">
        <!-- Contenu du rapport -->
        <div v-html="report.content" class="report-content"></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UserReportView',
  data() {
    return {
      report: {
        id: null,
        title: '',
        type: '',
        date_start: null,
        date_end: null,
        content: '',
        file_url: null,
        created_at: null
      },
      loading: true
    };
  },
  methods: {
    fetchReport() {
      this.loading = true;
      
      axios.get(`/api/user/reports/${this.$route.params.id}`)
        .then(response => {
          this.report = response.data.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Erreur lors du chargement du rapport:', error);
          this.$toasted.error('Impossible de charger le rapport.');
          this.loading = false;
          this.$router.push({ name: 'user.reports.index' });
        });
    },
    formatDate(dateString) {
      if (!dateString) return 'N/A';
      
      const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },
    getReportTypeName(type) {
      switch (type) {
        case 'expenses':
          return 'Rapport de dépenses';
        case 'budgets':
          return 'Rapport de budget';
        case 'categories':
          return 'Rapport par catégorie';
        case 'accounts':
          return 'Rapport des comptes';
        case 'complete':
          return 'Rapport financier complet';
        default:
          return 'Rapport';
      }
    }
  },
  mounted() {
    this.fetchReport();
  },
  metaInfo() {
    return {
      title: this.report.title ? `${this.report.title} - Budget Manager` : 'Rapport - Budget Manager'
    };
  }
};
</script>

<style>
.report-content table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
}

.report-content table, .report-content th, .report-content td {
  border: 1px solid #dee2e6;
}

.report-content th, .report-content td {
  padding: 0.75rem;
}

.report-content th {
  background-color: #f8f9fa;
  font-weight: bold;
}

.report-content h1, .report-content h2, .report-content h3, .report-content h4, .report-content h5, .report-content h6 {
  margin-top: 1rem;
  margin-bottom: 0.5rem;
}

.report-content .chart-container {
  width: 100%;
  height: 300px;
  margin-bottom: 1.5rem;
}

.report-content hr {
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
}

.report-content .summary-box {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 0.25rem;
  background-color: #f8f9fa;
  border-left: 5px solid #4e73df;
}

.report-content .text-success {
  color: #1cc88a !important;
}

.report-content .text-danger {
  color: #e74a3b !important;
}

.report-content .text-info {
  color: #36b9cc !important;
}

.report-content .text-warning {
  color: #f6c23e !important;
}
</style>