<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\MonthlyReportNotification;
use App\Services\ReportGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateMonthlyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:monthly {--notify : Envoyer une notification par email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les rapports mensuels pour tous les utilisateurs';

    /**
     * The report generation service.
     *
     * @var \App\Services\ReportGenerationService
     */
    protected $reportService;

    /**
     * Create a new command instance.
     *
     * @param \App\Services\ReportGenerationService $reportService
     * @return void
     */
    public function __construct(ReportGenerationService $reportService)
    {
        parent::__construct();
        $this->reportService = $reportService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Génération des rapports mensuels...');

        // Obtenir le mois précédent
        $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Parcourir tous les utilisateurs actifs
        $users = User::where('role', 'user')->get();
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        $reportsGenerated = 0;
        foreach ($users as $user) {
            // Générer le rapport pour le mois précédent
            $report = $this->reportService->generateMonthlyReport(
                $user,
                $lastMonthStart,
                $lastMonthEnd
            );

            // Si l'option notify est activée, envoyer une notification
            if ($this->option('notify') && $report) {
                $user->notify(new MonthlyReportNotification($report));
            }

            $reportsGenerated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("$reportsGenerated rapports mensuels ont été générés avec succès.");

        return 0;
    }
}