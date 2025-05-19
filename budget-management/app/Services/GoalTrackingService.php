<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\User;
use App\Repositories\Contracts\ExpenseRepositoryInterface;
use App\Repositories\Contracts\GoalRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GoalTrackingService
{
    /**
     * @var GoalRepositoryInterface
     */
    protected $goalRepository;

    /**
     * @var ExpenseRepositoryInterface
     */
    protected $expenseRepository;

    /**
     * Constructeur
     *
     * @param GoalRepositoryInterface $goalRepository
     * @param ExpenseRepositoryInterface $expenseRepository
     */
    public function __construct(
        GoalRepositoryInterface $goalRepository,
        ExpenseRepositoryInterface $expenseRepository
    ) {
        $this->goalRepository = $goalRepository;
        $this->expenseRepository = $expenseRepository;
    }

    /**
     * Calcule la progression d'un objectif.
     *
     * @param Goal $goal
     * @return float
     */
    public function calculateProgress(Goal $goal): float
    {
        // Si l'objectif est complété, retourner 100%
        if ($goal->statut === 'complété') {
            return 100;
        }
        
        // Si l'objectif est abandonné, retourner 0%
        if ($goal->statut === 'abandonné') {
            return 0;
        }
        
        // Récupérer les dépenses liées à la catégorie de l'objectif dans la période de l'objectif
        $depenses = $this->getExpensesForGoal($goal);
        
        // Calculer le total des dépenses
        $totalDepenses = $depenses->sum('montant');
        
        // Calculer le pourcentage de progression
        $progression = 0;
        
        if ($goal->montant_cible > 0) {
            $progression = ($totalDepenses / $goal->montant_cible) * 100;
        }
        
        // Limiter à 100%
        return min(100, $progression);
    }
    
    /**
     * Récupère les dépenses liées à un objectif.
     *
     * @param Goal $goal
     * @return Collection
     */
    public function getExpensesForGoal(Goal $goal): Collection
    {
        return $this->expenseRepository->findByUserAndCategory($goal->utilisateur, $goal->categorie_id)
            ->filter(function ($expense) use ($goal) {
                $expenseDate = Carbon::parse($expense->date_depense);
                return $expenseDate->between(
                    Carbon::parse($goal->date_debut),
                    Carbon::parse($goal->date_fin)
                );
            });
    }
    
    /**
     * Vérifie si un objectif a atteint sa cible.
     *
     * @param Goal $goal
     * @return bool
     */
    public function checkGoalCompletion(Goal $goal): bool
    {
        $progress = $this->calculateProgress($goal);
        
        if ($progress >= 100 && $goal->statut === 'en cours') {
            $this->goalRepository->changeStatus($goal, 'complété');
            return true;
        }
        
        return false;
    }
    
    /**
     * Vérifie tous les objectifs actifs d'un utilisateur.
     *
     * @param User $user
     * @return array
     */
    public function checkUserGoals(User $user): array
    {
        $goals = $this->goalRepository->findActiveByUser($user);
        $completedGoals = [];
        
        foreach ($goals as $goal) {
            if ($this->checkGoalCompletion($goal)) {
                $completedGoals[] = $goal;
            }
        }
        
        return $completedGoals;
    }
    
    /**
     * Génère les données de progression d'un objectif au fil du temps.
     *
     * @param Goal $goal
     * @return array
     */
    public function getProgressData(Goal $goal): array
    {
        $result = [];
        
        // Calculer le nombre de mois entre la date de début et la date de fin
        $dateDebut = Carbon::parse($goal->date_debut);
        $dateFin = Carbon::parse($goal->date_fin);
        $diffMonths = $dateDebut->diffInMonths($dateFin) + 1;
        
        // Limiter à 12 mois maximum
        $diffMonths = min(12, $diffMonths);
        
        // Récupérer les dépenses mensuelles
        for ($i = 0; $i < $diffMonths; $i++) {
            $date = Carbon::parse($goal->date_debut)->addMonths($i);
            $monthKey = $date->translatedFormat('F Y');
            
            // Récupérer les dépenses du mois
            $monthlyExpenses = $this->expenseRepository
                ->findByUserAndCategory($goal->utilisateur, $goal->categorie_id)
                ->filter(function ($expense) use ($date) {
                    $expenseDate = Carbon::parse($expense->date_depense);
                    return $expenseDate->year === $date->year &&
                           $expenseDate->month === $date->month;
                })
                ->sum('montant');
            
            $result[$monthKey] = $monthlyExpenses;
        }
        
        return $result;
    }
    
    /**
     * Calcule le montant restant pour atteindre l'objectif.
     *
     * @param Goal $goal
     * @return float
     */
    public function calculateRemainingAmount(Goal $goal): float
    {
        $depenses = $this->getExpensesForGoal($goal);
        $totalDepenses = $depenses->sum('montant');
        
        return max(0, $goal->montant_cible - $totalDepenses);
    }
    
    /**
     * Vérifie si un objectif est en retard sur son planning.
     *
     * @param Goal $goal
     * @return bool
     */
    public function isGoalBehindSchedule(Goal $goal): bool
    {
        if ($goal->statut !== 'en cours') {
            return false;
        }
        
        $now = Carbon::now();
        $dateDebut = Carbon::parse($goal->date_debut);
        $dateFin = Carbon::parse($goal->date_fin);
        
        // Si la date de fin est dépassée, l'objectif est en retard
        if ($now->isAfter($dateFin)) {
            return true;
        }
        
        // Calculer le pourcentage de temps écoulé
        $totalDays = $dateDebut->diffInDays($dateFin);
        $daysElapsed = $dateDebut->diffInDays($now);
        
        $timeElapsedPercent = ($totalDays > 0) ? ($daysElapsed / $totalDays) * 100 : 0;
        
        // Calculer le pourcentage de progression
        $progressPercent = $this->calculateProgress($goal);
        
        // Si le pourcentage de temps écoulé est supérieur au pourcentage de progression + 10%,
        // considérer l'objectif comme en retard
        return $timeElapsedPercent > ($progressPercent + 10);
    }
}