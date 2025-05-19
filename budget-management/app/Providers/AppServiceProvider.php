<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
{
    $this->app->singleton(AlertService::class);
    $this->app->singleton(BudgetAllocationService::class);
    $this->app->singleton(BudgetCalculationService::class);
    $this->app->singleton(ChartService::class);
    $this->app->singleton(ExpenseAnalysisService::class);
    $this->app->singleton(ExportService::class);
    $this->app->singleton(GoalTrackingService::class);
    $this->app->singleton(ReportGenerationService::class);
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
