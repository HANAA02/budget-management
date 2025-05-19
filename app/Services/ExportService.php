<?php

namespace App\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ExportService
{
    /**
     * Exporte les données d'un rapport au format Excel.
     *
     * @param array $reportData
     * @param string $fileName
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportToExcel(array $reportData, string $fileName)
    {
        return Excel::download(new ReportExport($reportData), $fileName);
    }
    
    /**
     * Exporte les données d'un rapport au format PDF.
     *
     * @param array $reportData
     * @param string $fileName
     * @return \Illuminate\Http\Response
     */
    public function exportToPdf(array $reportData, string $fileName)
    {
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('exports.report_pdf', compact('reportData'));
        
        return $pdf->download($fileName);
    }
    
    /**
     * Exporte les données d'un rapport au format CSV.
     *
     * @param array $reportData
     * @param string $fileName
     * @return \Illuminate\Http\Response
     */
    public function exportToCsv(array $reportData, string $fileName)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        $callback = function () use ($reportData) {
            $file = fopen('php://output', 'w');
            
            // En-têtes
            fputcsv($file, ['Catégorie', 'Montant alloué', 'Dépenses', 'Montant restant', 'Pourcentage utilisé']);
            
            // Données
            foreach ($reportData['categories'] as $category) {
                fputcsv($file, [
                    $category['nom'],
                    $category['montant_alloue'],
                    $category['total_depenses'],
                    $category['montant_restant'],
                    $category['pourcentage_utilise'] . '%',
                ]);
            }
            
            // Totaux
            fputcsv($file, [
                'TOTAL',
                $reportData['totaux']['alloue'],
                $reportData['totaux']['depenses'],
                $reportData['totaux']['restant'],
                $reportData['totaux']['pourcentage_utilise'] . '%',
            ]);
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }
}