<?php

namespace App\Console\Commands;

use App\Models\Budget;
use App\Models\CategoryBudget;
use App\Notifications\BudgetLimitWarning;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendBudgetAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alerts:budget {--threshold=80 : Seuil de pourcentage pour déclencher les alertes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifie les budgets actifs et envoie des alertes quand le seuil est dépassé';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $threshold = (int) $this->option('threshold');
        $this->info("Vérification des budgets avec un seuil de $threshold%...");

        // Obtenir les budgets actifs
        $activeBudgets = Budget::where('date_debut', '<=', Carbon::now())
                               ->where('date_fin', '>=', Carbon::now())
                               ->get();

        $this->info(count($activeBudgets) . " budgets actifs trouvés.");

        $alertsCount = 0;

        foreach ($activeBudgets as $budget) {
            $this->info("Traitement du budget: {$budget->nom} (ID: {$budget->id})");
            
            // Parcourir les catégories du budget
            $categoriesBudget = $budget->categoriesBudget()->with('categorie', 'depenses')->get();

            foreach ($categoriesBudget as $categorieBudget) {
                // Calculer le pourcentage utilisé
                $totalDepenses = $categorieBudget->depenses->sum('montant');
                $montantAlloue = $categorieBudget->montant_alloue;
                $pourcentageUtilise = ($montantAlloue > 0) ? ($totalDepenses / $montantAlloue) * 100 : 0;

                // Si le pourcentage utilisé dépasse le seuil et qu'il n'y a pas déjà d'alerte
                if ($pourcentageUtilise >= $threshold) {
                    $alertExists = $categorieBudget->alertes()
                                                  ->where('type', 'pourcentage')
                                                  ->where('seuil', '<=', $pourcentageUtilise)
                                                  ->where('created_at', '>=', Carbon::now()->subDay())
                                                  ->exists();

                    if (!$alertExists) {
                        // Créer l'alerte
                        $alerte = $categorieBudget->alertes()->create([
                            'type' => 'pourcentage',
                            'seuil' => $threshold,
                            'active' => true,
                        ]);

                        // Envoyer la notification
                        $budget->utilisateur->notify(new BudgetLimitWarning(
                            $budget,
                            $categorieBudget->categorie,
                            $alerte,
                            $pourcentageUtilise,
                            $totalDepenses,
                            $montantAlloue
                        ));

                        $this->info("Alerte envoyée pour {$categorieBudget->categorie->nom}: {$pourcentageUtilise}% utilisé");
                        $alertsCount++;
                    }
                }
            }
        }

        $this->info("$alertsCount alertes ont été envoyées.");
        return 0;
    }
}