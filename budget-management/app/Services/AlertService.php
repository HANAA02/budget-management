<?php

namespace App\Services;

use App\Models\CategoryBudget;
use App\Models\Alert;
use App\Notifications\BudgetLimitWarning;

class AlertService
{
    /**
     * Vérifie si des alertes doivent être déclenchées pour une catégorie de budget.
     *
     * @param CategoryBudget $categoryBudget
     * @return array
     */
    public function checkBudgetAlerts(CategoryBudget $categoryBudget)
    {
        // Récupérer le total des dépenses pour cette catégorie de budget
        $totalDepenses = $categoryBudget->depenses()->sum('montant');
        
        // Calculer le pourcentage utilisé
        $montantAlloue = $categoryBudget->montant_alloue;
        $pourcentageUtilise = ($montantAlloue > 0) ? ($totalDepenses / $montantAlloue) * 100 : 0;
        
        // Récupérer les alertes configurées pour cette catégorie
        $alertes = $categoryBudget->alertes()->where('active', true)->get();
        
        $alertesDeclechees = [];
        
        foreach ($alertes as $alerte) {
            // Vérifier si le seuil est atteint
            if ($alerte->type === 'pourcentage' && $pourcentageUtilise >= $alerte->seuil) {
                $alertesDeclechees[] = $alerte;
            } elseif ($alerte->type === 'montant' && $totalDepenses >= $alerte->seuil) {
                $alertesDeclechees[] = $alerte;
            }
        }
        
        // Envoyer des notifications pour les alertes déclenchées
        if (!empty($alertesDeclechees)) {
            $this->sendAlertNotifications($categoryBudget, $alertesDeclechees, $pourcentageUtilise, $totalDepenses);
        }
        
        return $alertesDeclechees;
    }
    
    /**
     * Envoie des notifications pour les alertes déclenchées.
     *
     * @param CategoryBudget $categoryBudget
     * @param array $alertes
     * @param float $pourcentageUtilise
     * @param float $totalDepenses
     * @return void
     */
    private function sendAlertNotifications(CategoryBudget $categoryBudget, array $alertes, float $pourcentageUtilise, float $totalDepenses)
    {
        $budget = $categoryBudget->budget;
        $user = $budget->utilisateur;
        $categorie = $categoryBudget->categorie;
        
        foreach ($alertes as $alerte) {
            $user->notify(new BudgetLimitWarning(
                $budget,
                $categorie,
                $alerte,
                $pourcentageUtilise,
                $totalDepenses,
                $categoryBudget->montant_alloue
            ));
        }
    }
    
    /**
     * Crée une nouvelle alerte pour une catégorie de budget.
     *
     * @param CategoryBudget $categoryBudget
     * @param string $type
     * @param float $seuil
     * @return Alert
     */
    public function createAlert(CategoryBudget $categoryBudget, string $type, float $seuil)
    {
        return Alert::create([
            'categorie_budget_id' => $categoryBudget->id,
            'type' => $type,
            'seuil' => $seuil,
            'active' => true,
        ]);
    }
    
    /**
     * Active ou désactive une alerte.
     *
     * @param Alert $alerte
     * @return Alert
     */
    public function toggleAlert(Alert $alerte)
    {
        $alerte->update([
            'active' => !$alerte->active,
        ]);
        
        return $alerte;
    }
}