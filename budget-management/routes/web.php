<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\AlertController;
use App\Http\Controllers\User\BudgetController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ExpenseController;
use App\Http\Controllers\User\GoalController;
use App\Http\Controllers\User\IncomeController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;

// Routes publiques
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes d'authentification
// Auth::routes(['verify' => true]);
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');
// Routes pour utilisateurs authentifiés
Route::middleware(['auth', 'verified'])->group(function () {
    // Tableau de bord utilisateur
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Comptes
    Route::resource('accounts', AccountController::class);

    // Revenus
    Route::resource('incomes', IncomeController::class);

    // Budgets
    Route::resource('budgets', BudgetController::class);
    Route::get('budgets/{budget}/allocate', [BudgetController::class, 'showAllocation'])->name('budgets.allocate');
    Route::post('budgets/{budget}/allocate', [BudgetController::class, 'saveAllocation'])->name('budgets.saveAllocation');

    // Dépenses
    Route::resource('expenses', ExpenseController::class);
    Route::post('expenses/quick-add', [ExpenseController::class, 'quickAdd'])->name('expenses.quickAdd');

    // Objectifs
    Route::resource('goals', GoalController::class);
    Route::get('goals/{goal}/progress', [GoalController::class, 'progress'])->name('goals.progress');

    // Alertes
    Route::resource('alerts', AlertController::class);
    Route::post('alerts/{alert}/toggle', [AlertController::class, 'toggle'])->name('alerts.toggle');

    // Rapports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/generate', [ReportController::class, 'create'])->name('reports.create');
    Route::post('reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
});

// Routes pour administrateurs
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Tableau de bord admin
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Gestion des utilisateurs
    Route::resource('users', UserController::class);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.resetPassword');
    
    // Gestion des catégories
    Route::resource('categories', CategoryController::class);
    Route::post('categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');

    // Paramètres système
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
});



Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('dashboard', 'Admin\DashboardController@index')->name('dashboard');
    Route::resource('categories', 'Admin\CategoryController');
    Route::resource('users', 'Admin\UserController');
    Route::get('users/{user}/activities', 'Admin\UserController@activities')->name('users.activities');
    Route::get('password/reset/{user}', 'Admin\UserController@passwordReset')->name('password.reset');
});