<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Category;
use App\Models\CategoryBudget;

class BudgetAllocationService
{
    /**
     * Crée la répartition initiale du budget basée sur les pourcentages par défaut.
     *
     * @param Budget $budget
     * @return bool
     */
    public function createInitialAllocation(Budget $budget)
    {
        $categories = Category::all();
        $montantTotal = $budget->montant_total;
        
        foreach ($categories as $category) {
            $pourcentage = $category->pourcentage_defaut;
            $montantAlloue = ($pourcentage / 100) * $montantTotal;
            
            CategoryBudget::create([
                'budget_id' => $budget->id,
                'categorie_id' => $category->id,
                'montant_alloue' => $montantAlloue,
                'pourcentage' => $pourcentage,
            ]);
        }
        
        return true;
    }
    
    /**
     * Met à jour la répartition du budget.
     *
     * @param Budget $budget
     * @param array $data
     * @return bool
     */
    public function updateAllocation(Budget $budget, array $data)
    {
        $montantTotal = $budget->montant_total;
        $categoriesBudget = $budget->categoriesBudget;
        
        // Mise à jour des catégories existantes
        foreach ($categoriesBudget as $categorieBudget) {
            $categoryId = $categorieBudget->categorie_id;
            
            // Si la catégorie est présente dans les données
            if (isset($data['categories'][$categoryId])) {
                $pourcentage = $data['categories'][$categoryId];
                $montantAlloue = ($pourcentage / 100) * $montantTotal;
                
                $categorieBudget->update([
                    'montant_alloue' => $montantAlloue,
                    'pourcentage' => $pourcentage,
                ]);
            }
        }
        
        // Ajout de nouvelles catégories si nécessaire
        if (isset($data['nouvelles_categories'])) {
            foreach ($data['nouvelles_categories'] as $categoryId => $pourcentage) {
                if (!$categoriesBudget->where('categorie_id', $categoryId)->count()) {
                    $montantAlloue = ($pourcentage / 100) * $montantTotal;
                    
                    CategoryBudget::create([
                        'budget_id' => $budget->id,
                        'categorie_id' => $categoryId,
                        'montant_alloue' => $montantAlloue,
                        'pourcentage' => $pourcentage,
                    ]);
                }
            }
        }
        
        return true;
    }
    
    /**
     * Recalcule les montants alloués si le montant total du budget change.
     *
     * @param Budget $budget
     * @param float $newTotal
     * @return bool
     */
    public function recalculateAllocation(Budget $budget, float $newTotal)
    {
        $categoriesBudget = $budget->categoriesBudget;
        
        foreach ($categoriesBudget as $categorieBudget) {
            $pourcentage = $categorieBudget->pourcentage;
            $montantAlloue = ($pourcentage / 100) * $newTotal;
            
            $categorieBudget->update([
                'montant_alloue' => $montantAlloue,
            ]);
        }
        
        return true;
    }
}