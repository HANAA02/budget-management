<?php

// routes/console.php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\User;
use App\Models\Alert;
use App\Models\Goal;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('budget:check-alerts', function () {
    $this->info('Checking budget alerts...');
    
    $alerts = Alert::where('active', true)->get();
    $count = 0;
    
    foreach ($alerts as $alert) {
        $categoryBudget = $alert->categorieBudget;
        $budget = $categoryBudget->budget;
        $user = $budget->user;
        
        // Skip if budget period is not active
        if (Carbon::now()->lt($budget->date_debut) || Carbon::now()->gt($budget->date_fin)) {
            continue;
        }
        
        // Get total expenses for this category
        $expensesTotal = $categoryBudget->expenses()
                        ->where('statut', 'validée')
                        ->sum('montant');
        
        // Calculate percentage
        $percentage = ($categoryBudget->montant_alloue > 0) 
            ? ($expensesTotal / $categoryBudget->montant_alloue) * 100 
            : 0;
            
        // Check if alert threshold is reached
        $thresholdReached = false;
        
        if ($alert->type === 'pourcentage' && $percentage >= $alert->seuil) {
            $thresholdReached = true;
        } elseif ($alert->type === 'montant' && $expensesTotal >= $alert->seuil) {
            $thresholdReached = true;
        } elseif ($alert->type === 'depassement' && $expensesTotal > $categoryBudget->montant_alloue) {
            $thresholdReached = true;
        }
        
        if ($thresholdReached) {
            // Create notification
            $user->notify(new \App\Notifications\BudgetAlertNotification($alert, $budget, $categoryBudget, $expensesTotal, $percentage));
            $count++;
            
            // Mark alert as processed if it's one-time
            if ($alert->is_recurring == false) {
                $alert->active = false;
                $alert->save();
            }
        }
    }
    
    $this->info("Completed! {$count} alerts were triggered and notifications sent.");
})->purpose('Check budget alerts and send notifications');

Artisan::command('budget:check-goals', function () {
    $this->info('Checking financial goals progress...');
    
    $goals = Goal::where('statut', 'en cours')->get();
    $count = 0;
    
    foreach ($goals as $goal) {
        $user = $goal->user;
        $category = $goal->categorie;
        
        // Skip if goal end date is passed
        if (Carbon::now()->gt($goal->date_fin)) {
            continue;
        }
        
        // Calculate current amount
        $currentAmount = 0;
        
        if ($goal->type === 'epargne') {
            // For savings goals, sum all savings in this category
            $currentAmount = $user->savings()
                            ->where('categorie_id', $category->id)
                            ->whereBetween('date', [$goal->date_debut, $goal->date_fin])
                            ->sum('montant');
        } else {
            // For expense reduction goals, calculate expenses in this category
            $currentAmount = Expense::whereHas('categorieBudget', function ($query) use ($category, $user) {
                                $query->whereHas('categorie', function ($q) use ($category) {
                                    $q->where('id', $category->id);
                                })
                                ->whereHas('budget', function ($q) use ($user) {
                                    $q->where('utilisateur_id', $user->id);
                                });
                            })
                            ->whereBetween('date_depense', [$goal->date_debut, $goal->date_fin])
                            ->where('statut', 'validée')
                            ->sum('montant');
        }
        
        // Check if goal is achieved
        $goalAchieved = false;
        
        if ($goal->type === 'epargne' && $currentAmount >= $goal->montant_cible) {
            $goalAchieved = true;
        } elseif ($goal->type === 'reduction' && $currentAmount <= $goal->montant_cible) {
            $goalAchieved = true;
        }
        
        if ($goalAchieved) {
            // Update goal status
            $goal->statut = 'atteint';
            $goal->save();
            
            // Send notification
            $user->notify(new \App\Notifications\GoalAchievedNotification($goal));
            $count++;
        }
    }
    
    $this->info("Completed! {$count} goals were achieved and notifications sent.");
})->purpose('Check financial goals progress and send notifications');

Artisan::command('reports:generate-monthly', function () {
    $this->info('Generating monthly budget reports...');
    
    $users = User::where('notification_monthly_report', true)->get();
    $count = 0;
    
    foreach ($users as $user) {
        // Get active budgets for the previous month
        $lastMonth = Carbon::now()->subMonth();
        $startOfLastMonth = $lastMonth->copy()->startOfMonth();
        $endOfLastMonth = $lastMonth->copy()->endOfMonth();
        
        $budgets = $user->budgets()
                   ->where(function ($query) use ($startOfLastMonth, $endOfLastMonth) {
                       $query->where(function ($q) use ($startOfLastMonth, $endOfLastMonth) {
                           $q->where('date_debut', '<=', $endOfLastMonth)
                             ->where('date_fin', '>=', $startOfLastMonth);
                       });
                   })
                   ->get();
        
        foreach ($budgets as $budget) {
            // Generate report data
            $reportData = [
                'period' => $lastMonth->format('F Y'),
                'budget_id' => $budget->id,
                'type' => 'monthly'
            ];
            
            // Create report
            $report = $user->reports()->create([
                'titre' => 'Rapport mensuel - ' . $lastMonth->format('F Y'),
                'type' => 'budget',
                'periode' => 'mensuel',
                'date_debut' => $startOfLastMonth,
                'date_fin' => $endOfLastMonth,
                'format' => 'html',
                'data' => $reportData
            ]);
            
            // Queue email
            \Mail::to($user->email)->queue(new \App\Mail\BudgetSummary($user, $budget, $report));
            $count++;
        }
    }
    
    $this->info("Completed! {$count} monthly reports generated and sent.");
})->purpose('Generate and send monthly budget reports to users');

Artisan::command('accounts:sync-balances', function () {
    $this->info('Syncing account balances...');
    
    $accounts = \App\Models\Account::all();
    $updatedCount = 0;
    
    foreach ($accounts as $account) {
        // Calculate actual balance based on incomes and expenses
        $incomeTotal = $account->incomes()->sum('montant');
        $expenseTotal = 0;
        
        // Get expenses linked to this account through budget categories
        foreach ($account->user->budgets as $budget) {
            foreach ($budget->categorieBudgets as $categoryBudget) {
                $expenseTotal += $categoryBudget->expenses()
                              ->where('statut', 'validée')
                              ->sum('montant');
            }
        }
        
        $calculatedBalance = $incomeTotal - $expenseTotal;
        
        // Update account if there's a discrepancy
        if (abs($account->solde - $calculatedBalance) > 0.01) {
            $account->solde = $calculatedBalance;
            $account->save();
            $updatedCount++;
        }
    }
    
    $this->info("Completed! {$updatedCount} account balances updated.");
})->purpose('Synchronize account balances with actual income and expense data');