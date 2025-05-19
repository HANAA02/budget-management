<?php

namespace App\Notifications;

use App\Models\Goal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GoalAchieved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $goal;
    protected $finalAmount;

    /**
     * Create a new notification instance.
     *
     * @param Goal $goal
     * @param float $finalAmount
     * @return void
     */
    public function __construct(Goal $goal, float $finalAmount)
    {
        $this->goal = $goal;
        $this->finalAmount = $finalAmount;
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
        return (new MailMessage)
            ->subject('Félicitations! Objectif atteint - ' . $this->goal->titre)
            ->greeting('Bravo ' . $notifiable->prenom . ' ' . $notifiable->nom . '!')
            ->line('Nous avons le plaisir de vous annoncer que vous avez atteint votre objectif financier : ' . $this->goal->titre)
            ->line('Détails de l\'objectif :')
            ->line('- Catégorie : ' . $this->goal->categorie->nom)
            ->line('- Montant cible : ' . number_format($this->goal->montant_cible, 2) . ' MAD')
            ->line('- Montant atteint : ' . number_format($this->finalAmount, 2) . ' MAD')
            ->line('- Période : du ' . $this->goal->date_debut->format('d/m/Y') . ' au ' . $this->goal->date_fin->format('d/m/Y'))
            ->action('Voir tous vos objectifs', url('/goals'))
            ->line('Continuez vos bonnes habitudes financières avec notre application de gestion de budget!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'goal_achieved',
            'goal_id' => $this->goal->id,
            'goal_title' => $this->goal->titre,
            'category_id' => $this->goal->categorie_id,
            'category_name' => $this->goal->categorie->nom,
            'target_amount' => $this->goal->montant_cible,
            'final_amount' => $this->finalAmount,
            'start_date' => $this->goal->date_debut->format('Y-m-d'),
            'end_date' => $this->goal->date_fin->format('Y-m-d'),
            'message' => 'Félicitations! Vous avez atteint votre objectif "' . $this->goal->titre . '"!',
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
            'goal_id' => $this->goal->id,
            'category_id' => $this->goal->categorie_id,
            'target_amount' => $this->goal->montant_cible,
            'final_amount' => $this->finalAmount,
        ];
    }
}