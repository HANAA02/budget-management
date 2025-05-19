<?php

// routes/channels.php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// User-specific notifications channel
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Budget alerts channel
Broadcast::channel('budget.{budgetId}', function ($user, $budgetId) {
    return $user->budgets()->where('id', $budgetId)->exists();
});

// Goal notifications channel
Broadcast::channel('goal.{goalId}', function ($user, $goalId) {
    return $user->goals()->where('id', $goalId)->exists();
});

// Expense notifications channel
Broadcast::channel('expense.{budgetId}', function ($user, $budgetId) {
    return $user->budgets()->where('id', $budgetId)->exists();
});

// Admin channel (only accessible to admin users)
Broadcast::channel('admin', function ($user) {
    return $user->role === 'admin';
});