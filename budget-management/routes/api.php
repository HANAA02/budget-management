<?php

// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\GoalController;
use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API v1
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        // User profile
        Route::get('user', [AuthController::class, 'user']);
        Route::put('user', [AuthController::class, 'update']);
        Route::post('logout', [AuthController::class, 'logout']);
        
        // Accounts
        Route::apiResource('accounts', AccountController::class);
        
        // Budgets
        Route::apiResource('budgets', BudgetController::class);
        Route::get('budgets/{budget}/categories', [BudgetController::class, 'categories']);
        Route::post('budgets/{budget}/allocate', [BudgetController::class, 'allocateCategories']);
        
        // Expenses
        Route::apiResource('expenses', ExpenseController::class);
        Route::get('expenses/by-category', [ExpenseController::class, 'byCategory']);
        Route::get('expenses/by-date', [ExpenseController::class, 'byDate']);
        
        // Incomes
        Route::apiResource('incomes', IncomeController::class);
        
        // Goals
        Route::apiResource('goals', GoalController::class);
        Route::put('goals/{goal}/progress', [GoalController::class, 'updateProgress']);
        
        // Alerts
        Route::apiResource('alerts', AlertController::class);
        
        // Categories
        Route::apiResource('categories', CategoryController::class);
        
        // Reports
        Route::get('reports/summary', [ReportController::class, 'summary']);
        Route::get('reports/expenses', [ReportController::class, 'expenses']);
        Route::get('reports/incomes', [ReportController::class, 'incomes']);
        Route::get('reports/budget-progress', [ReportController::class, 'budgetProgress']);
        Route::get('reports/goals-progress', [ReportController::class, 'goalsProgress']);
        Route::post('reports/generate', [ReportController::class, 'generate']);
        Route::get('reports/download/{id}', [ReportController::class, 'download']);
    });
});

// Fallback for undefined routes
Route::fallback(function(){
    return response()->json([
        'message' => 'Not Found. If error persists, contact info@budgetapp.com'
    ], 404);
});