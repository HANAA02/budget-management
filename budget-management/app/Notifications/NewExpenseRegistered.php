<?php

namespace App\Notifications;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewExpenseRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    protected $expense;

    /**
     * Create a new notification instance.
     *
     * @param Expense $expense
     * @return void
     */
    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $categoryName = $this->expense->categorieBudget->categorie->nom;
        $budgetName = $this->expense->categorieBudget->budget->nom;

        return (new MailMessage)
            ->subject('Nouvelle dépense enregistrée - ' . $this->expense->description)
            ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->nom . ',')
            ->line('Une nouvelle dépense a été enregistrée dans votre budget.')
            ->line('Détails de la dépense :')
            ->line('- Description : ' . $this->expense->description)
            ->line('- Montant : ' . number_format($this->expense->montant, 2) . ' MAD')
            ->line('- Date : ' . $this->expense->date_depense->format('d/m/Y'))
            ->line('- Catégorie : ' . $categoryName)
            ->line('- Budget : ' . $budgetName)
            ->action('Voir toutes vos dépenses', url('/expenses'))
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
        $categoryName = $this->expense->categorieBudget->categorie->nom;
        $budgetName = $this->expense->categorieBudget->budget->nom;

        return [
            'type' => 'new_expense',
            'expense_id' => $this->expense->id,
            'description' => $this->expense->description,
            'amount' => $this->expense->montant,
            'date' => $this->expense->date_depense->format('Y-m-d'),
            'category_id' => $this->expense->categorieBudget->categorie_id,
            'category_name' => $categoryName,
            'budget_id' => $this->expense->categorieBudget->budget_id,
            'budget_name' => $budgetName,
            'message' => 'Nouvelle dépense de ' . number_format($this->expense->montant, 2) . ' MAD pour "' . $this->expense->description . '" dans la catégorie ' . $categoryName,
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
            'expense_id' => $this->expense->id,
            'amount' => $this->expense->montant,
            'category_id' => $this->expense->categorieBudget->categorie_id,
            'budget_id' => $this->expense->categorieBudget->budget_id,
        ];
    }
}