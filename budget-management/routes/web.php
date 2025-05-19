<?php

// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\BudgetController;
use App\Http\Controllers\User\ExpenseController;
use App\Http\Controllers\User\IncomeController;
use App\Http\Controllers\User\GoalController;
use App\Http\Controllers\User\AlertController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\CategoryBudgetController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('welcome');
});

// Static pages
Route::get('/features', function () {
    return view('pages.features');
})->name('features');

Route::get('/pricing', function () {
    return view('pages.pricing');
})->name('pricing');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::post('/contact', [HomeController::class, 'submitContact'])->name('contact.submit');

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

// Authentication routes (Laravel UI or custom)
Auth::routes(['verify' => true]);

// User authenticated routes
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Budgets
    Route::resource('budgets', BudgetController::class);
    Route::get('budgets/{budget}/categories', [BudgetController::class, 'categories'])->name('budgets.categories');
    
    // Category budgets
    Route::post('category-budgets', [CategoryBudgetController::class, 'store'])->name('category-budgets.store');
    Route::put('category-budgets/{categoryBudget}', [CategoryBudgetController::class, 'update'])->name('category-budgets.update');
    Route::delete('category-budgets/{categoryBudget}', [CategoryBudgetController::class, 'destroy'])->name('category-budgets.destroy');
    
    // Expenses
    Route::resource('expenses', ExpenseController::class);
    Route::get('expenses/export/{format}', [ExpenseController::class, 'export'])->name('expenses.export');
    
    // Incomes
    Route::resource('incomes', IncomeController::class);
    
    // Goals
    Route::resource('goals', GoalController::class);
    
    // Alerts
    Route::resource('alerts', AlertController::class);
    Route::post('alerts/{alert}/toggle', [AlertController::class, 'toggle'])->name('alerts.toggle');
    
    // Accounts
    Route::resource('accounts', AccountController::class);
    
    // Reports
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/generate', [ReportController::class, 'create'])->name('reports.generate');
    Route::post('reports', [ReportController::class, 'store'])->name('reports.store');
    Route::get('reports/{id}', [ReportController::class, 'show'])->name('reports.view');
    Route::get('reports/{id}/download/{format}', [ReportController::class, 'download'])->name('reports.download');
    
    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Users management
    Route::resource('users', UserController::class);
    Route::put('users/{user}/role', [UserController::class, 'updateRole'])->name('users.update-role');
    Route::put('users/{user}/status', [UserController::class, 'updateStatus'])->name('users.update-status');
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    
    // Categories management
    Route::resource('categories', CategoryController::class);
    Route::put('categories/{category}/default-percentage', [CategoryController::class, 'updateDefaultPercentage'])
        ->name('categories.update-default-percentage');
});

// Home route for authenticated users
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Fallback route for undefined routes
Route::fallback(function () {
    return view('errors.404');
});