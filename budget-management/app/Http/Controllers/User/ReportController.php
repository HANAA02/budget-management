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
     * Display the reports page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('user.reports.index');
    }

    /**
     * Show the form for generating a report.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $user = auth()->user();
        $budgets = $user->budgets;
        $categories = \App\Models\Category::all();
        
        return view('user.reports.generate', compact('budgets', 'categories'));
    }

    /**
     * Generate a custom report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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
     * Display a specific report.
     *
     * @param  int  $reportId
     * @return \Illuminate\View\View
     */
    public function show($reportId)
    {
        // Cette méthode serait utilisée si vous implémentez la sauvegarde des rapports générés
        $reportData = $this->reportService->getReport($reportId);
        
        return view('user.reports.view', compact('reportData'));
    }

    /**
     * Download a report in the specified format.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $reportId
     * @return \Illuminate\Http\Response
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