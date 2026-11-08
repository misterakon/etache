<?php

namespace App\Notifications;

use App\Models\Personnel;
use App\Models\RegleAlerte;
use App\Models\Tache;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

/**
 * Alerte d'echeance d'une tache. Un seul canal par instance :
 *  - 'database' : cloche de l'application (table notifications) ;
 *  - 'mail'     : email.
 */
class AlerteTacheNotification extends Notification
{
    public function __construct(
        public Tache $tache,
        public RegleAlerte $regle,
        public Personnel $destinataire,
        public string $canal,
    ) {
        $this->locale('fr'); // le message (et le pied de page du gabarit) sortent en francais, quelle que soit la langue de l'application
    }

    public function via(object $notifiable): array
    {
        return [$this->canal];
    }

    /** Ecart en jours entre le declenchement et l'echeance (negatif = avant l'echeance). */
    private function decalage(): int
    {
        return (int) $this->regle->decalage_jours;
    }

    public function message(): string
    {
        $n = abs($this->decalage());
        $jours = $n.' jour'.($n > 1 ? 's' : '');
        $quand = match (true) {
            $this->decalage() < 0 => "arrive à échéance dans {$jours}",
            $this->decalage() === 0 => "arrive à échéance aujourd'hui",
            default => "a dépassé son échéance de {$jours}",
        };

        return "La tâche {$this->tache->code} « {$this->tache->libelle} » {$quand} (échéance : {$this->tache->date_fin->format('d/m/Y')}).";
    }

    /** Ton visuel (palette des pages de pilotage) : retard, attente (jour J), avenir. */
    public function ton(): string
    {
        return match (true) {
            $this->decalage() > 0 => 'retard',
            $this->decalage() === 0 => 'attente',
            default => 'avenir',
        };
    }

    public function url(): string
    {
        return route('feuille_de_route.tache.details', $this->tache->id);
    }

    /** Canal database : contenu stocke dans notifications.data */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'alerte_tache',
            'tache_id' => $this->tache->id,
            'code' => $this->tache->code,
            'libelle' => Str::limit($this->tache->libelle, 120),
            'titre' => $this->regle->libelle,
            'regle' => $this->regle->code,
            'message' => $this->message(),
            'ton' => $this->ton(),
            'date_fin' => $this->tache->date_fin->toDateString(),
            'url' => $this->url(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('[e-Tache] '.$this->regle->libelle.' — '.$this->tache->code)
            ->markdown('emails.alerte_tache', [
                'tache' => $this->tache,
                'regle' => $this->regle,
                'destinataire' => $this->destinataire,
                'message' => $this->message(),
                'statut' => $this->tache->statutExecution?->libelle,
                'url' => $this->url(),
            ]);
    }
}
