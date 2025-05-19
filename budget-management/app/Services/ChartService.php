<?php

namespace App\Services;

class ChartService
{
    /**
     * Génère les données pour un graphique en camembert de répartition du budget.
     *
     * @param array $categoriesData
     * @return array
     */
    public function generateBudgetPieChart(array $categoriesData)
    {
        $labels = [];
        $data = [];
        $colors = [];
        
        foreach ($categoriesData as $category) {
            $labels[] = $category['nom'];
            $data[] = $category['pourcentage'];
            $colors[] = $this->getRandomColor();
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => $colors,
        ];
    }
    
    /**
     * Génère les données pour un graphique en barres des dépenses par catégorie.
     *
     * @param array $categoriesData
     * @return array
     */
    public function generateExpenseBarChart(array $categoriesData)
    {
        $labels = [];
        $dataAlloue = [];
        $dataDepense = [];
        
        foreach ($categoriesData as $category) {
            $labels[] = $category['nom'];
            $dataAlloue[] = $category['montant_alloue'];
            $dataDepense[] = $category['total_depenses'];
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Budget alloué',
                    'data' => $dataAlloue,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Dépenses',
                    'data' => $dataDepense,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ]
            ]
        ];
    }
    
    /**
     * Génère les données pour un graphique linéaire des dépenses mensuelles.
     *
     * @param array $depensesMensuelles
     * @return array
     */
    public function generateMonthlyExpenseChart(array $depensesMensuelles)
    {
        $labels = [];
        $data = [];
        
        foreach ($depensesMensuelles as $mois => $montant) {
            $labels[] = $mois;
            $data[] = $montant;
        }
        
        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Dépenses mensuelles',
                    'data' => $data,
                    'fill' => false,
                    'borderColor' => 'rgb(75, 192, 192)',
                    'tension' => 0.1
                ]
            ]
        ];
    }
    
    /**
     * Génère une couleur aléatoire pour les graphiques.
     *
     * @return string
     */
    private function getRandomColor()
    {
        $r = rand(0, 255);
        $g = rand(0, 255);
        $b = rand(0, 255);
        
        return "rgb($r, $g, $b)";
    }
}