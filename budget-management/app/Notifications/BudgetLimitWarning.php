<?php

namespace App\Notifications;

use App\Models\Alert;
use App\Models\Budget;
use App\Models\Category;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BudgetLimitWarning extends Notification implements ShouldQueue
{
    use Queueable;

    protected $budget;
    protected $category;
    protected $alert;
    protected $percentageUsed;
    protected $totalExpenses;
    protected $allocatedAmount;

    /**
     * Create a new notification instance.
     *
     * @param Budget $budget
     * @param Category $category
     * @param Alert $alert
     * @param float $percentageUsed
     * @param float $totalExpenses
     * @param float $allocatedAmount
     * @return void
     */
    public function __construct(
        Budget $budget,
        Category $category,
        Alert $alert,
        float $percentageUsed,
        float $totalExpenses,
        float $allocatedAmount
    ) {
        $this->budget = $budget;
        $this->category = $category;
        $this->alert = $alert;
        $this->percentageUsed = $percentageUsed;
        $this->totalExpenses = $totalExpenses;
        $this->allocatedAmount = $allocatedAmount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $threshold = $this->alert->type === 'pourcentage'
            ? $this->alert->seuil . '%'
            : number_format($this->alert->seuil, 2) . ' ' . $this->budget->devise ?? 'MAD';

        return (new MailMessage)
            ->subject('Alerte de limite budgétaire - ' . $this->category->nom)
            ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->nom . ',')
            ->line('Nous vous informons que la catégorie "' . $this->category->nom . '" de votre budget "' . $this->budget->nom . '" a atteint un seuil d\'alerte.')
            ->line('Détails de l\'alerte :')
            ->line('- Seuil défini : ' . $threshold)
            ->line('- Montant alloué : ' . number_format($this->allocatedAmount, 2) . ' MAD')
            ->line('- Dépenses actuelles : ' . number_format($this->totalExpenses, 2) . ' MAD')
            ->line('- Pourcentage utilisé : ' . number_format($this->percentageUsed, 2) . '%')
            ->action('Voir le budget', url('/budgets/' . $this->budget->id))
            ->line('Merci d\'utiliser notre application de gestion de budget!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        $threshold = $this->alert->type === 'pourcentage'
            ? $this->alert->seuil . '%'
            : number_format($this->alert->seuil, 2) . ' ' . $this->budget->devise ?? 'MAD';

        return [
            'type' => 'budget_limit_warning',
            'budget_id' => $this->budget->id,
            'budget_name' => $this->budget->nom,
            'category_id' => $this->category->id,
            'category_name' => $this->category->nom,
            'alert_id' => $this->alert->id,
            'alert_type' => $this->alert->type,
            'alert_threshold' => $threshold,
            'allocated_amount' => $this->allocatedAmount,
            'total_expenses' => $this->totalExpenses,
            'percentage_used' => $this->percentageUsed,
            'message' => 'Votre catégorie "' . $this->category->nom . '" a atteint ' . number_format($this->percentageUsed, 2) . '% de son budget alloué.',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'budget_id' => $this->budget->id,
            'category_id' => $this->category->id,
            'alert_id' => $this->alert->id,
            'percentage_used' => $this->percentageUsed,
            'total_expenses' => $this->totalExpenses,
            'allocated_amount' => $this->allocatedAmount,
        ];
    }
}