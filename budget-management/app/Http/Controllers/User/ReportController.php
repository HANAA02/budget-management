<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ExpenseAnalysisService;
use App\Services\ReportGenerationService;
use App\Services\ExportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;
    protected $expenseAnalysisService;
    protected $exportService;

    public function __construct(
        ReportGenerationService $reportService,
        ExpenseAnalysisService $expenseAnalysisService,
        ExportService $exportService
    ) {
        $this->reportService = $reportService;
        $this->expenseAnalysisService = $expenseAnalysisService;
        $this->exportService = $exportService;
    }

    /**
     * Affiche la page des rapports.
     */
    public function index()
    {
        return view('user.reports.index');
    }

    /**
     * Affiche le formulaire de génération de rapport.
     */
    public function create()
    {
        $user = auth()->user();
        $budgets = $user->budgets;
        $categories = \App\Models\Category::all();
        
        return view('user.reports.generate', compact('budgets', 'categories'));
    }

    /**
     * Génère un rapport personnalisé.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'type_rapport' => 'required|in:mensuel,trimestriel,annuel,personnalise',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'budgets' => 'nullable|array',
            'budgets.*' => 'exists:budgets,id',
            'format' => 'required|in:web,pdf,excel',
        ]);
        
        $user = auth()->user();
        
        // Générer les données du rapport
        $reportData = $this->reportService->generateReport(
            $user,
            $request->type_rapport,
            $request->date_debut,
            $request->date_fin,
            $request->categories ?? [],
            $request->budgets ?? []
        );
        
        // Si format web, afficher la vue
        if ($request->format === 'web') {
            return view('user.reports.view', compact('reportData'));
        }
        
        // Sinon, exporter le rapport
        $fileName = 'rapport_' . now()->format('Y-m-d') . '.' . ($request->format === 'excel' ? 'xlsx' : 'pdf');
        
        if ($request->format === 'excel') {
            return $this->exportService->exportToExcel($reportData, $fileName);
        } else {
            return $this->exportService->exportToPdf($reportData, $fileName);
        }
    }

    /**
     * Affiche un rapport spécifique.
     */
    public function show($reportId)
    {
        // Cette méthode serait utilisée si vous implémentez la sauvegarde des rapports générés
        $reportData = $this->reportService->getReport($reportId);
        
        return view('user.reports.view', compact('reportData'));
    }

    /**
     * Télécharge un rapport dans le format spécifié.
     */
    public function download(Request $request, $reportId)
    {
        $request->validate([
            'format' => 'required|in:pdf,excel',
        ]);
        
        // Récupérer les données du rapport
        $reportData = $this->reportService->getReport($reportId);
        
        $fileName = 'rapport_' . now()->format('Y-m-d') . '.' . ($request->format === 'excel' ? 'xlsx' : 'pdf');
        
        if ($request->format === 'excel') {
            return $this->exportService->exportToExcel($reportData, $fileName);
        } else {
            return $this->exportService->exportToPdf($reportData, $fileName);
        }
    }
}