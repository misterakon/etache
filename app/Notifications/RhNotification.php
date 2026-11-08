<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/** Cloche : alertes RH (validité des documents du dossier, sur-affectation). Canal « database » uniquement. */
class RhNotification extends Notification
{
    public function __construct(public string $type, public string $titre, public string $message, public string $lien, public string $ton = 'attente')
    {
        $this->locale('fr');
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return ['type' => $this->type, 'libelle' => 'RH', 'titre' => $this->titre, 'message' => $this->message, 'ton' => $this->ton, 'lien' => $this->lien];
    }
}
